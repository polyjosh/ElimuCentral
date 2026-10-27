# ElimuCentral — data model and integrations

This is a proposal: adjust names as you build, but keep the rules. Every table marked **(S)** has
`school_id` (FK, indexed) and uses the `BelongsToSchool` global scope + trait (fills `school_id` from `session('school_id')`).
Money columns are `unsignedBigInteger` **cents** named `*_cents`. Phones are `string(13)` E.164.

## Tenancy and people

| Table | Key columns |
|---|---|
| `schools` | name, short_name, county, paybill, knec_code, settings (json: `show_class_position`, `sms_sender_id`, `report_footer`) |
| `users` | name, email (nullable, unique), phone (nullable, unique, E.164), password (nullable for parents), locale (`en`/`sw`), theme (`system`/`light`/`dark`), last_seen_at |
| `school_user` | school_id, user_id, role_label — roles via spatie/laravel-permission **teams** (team = school) |
| `otp_codes` | phone, code_hash, expires_at, attempts, sent_at — throttled (see S1) |

Roles (learner logins are issued per learner from S4b): `principal`, `deputy`, `bursar`, `hod`, `class_teacher`, `teacher`, `parent`, `learner`, `admin`.
`User::bottomNavRole()` returns `parent` | `teacher` | `learner` | null for the shell.

## Academic structure (S)

| Table | Key columns |
|---|---|
| `academic_years` | year (2026), is_current |
| `terms` | academic_year_id, number (1–3), opens_on, half_term_starts_on, half_term_ends_on, closes_on, status (`upcoming`/`current`/`closed`) |
| `levels` | name ("Grade 8", "Form 4"), curriculum (`cbe`/`844`), band (`pre_primary`/`primary`/`junior`/`senior`/`secondary`), sort |
| `streams` | level_id, name ("Grade 8 Blue"), short ("Blue"), class_teacher_id (users), capacity |
| `learning_areas` | name, code, curriculum, level_band, lessons_per_week, type (`core`/`choice`/`elective`), hod_id |
| `strands` | learning_area_id, number ("2.0"), name |
| `pathways` | key (`stem`/`social_sciences`/`arts_sports`), name |
| `combinations` | pathway_id, name, capacity; `combination_learning_area` pivot (3 electives) |
| `teaching_assignments` | user_id, stream_id, learning_area_id, term_id |
| `grading_scales` | curriculum, scale json (defaults from `App\Support\Rubric`) |

## Learners (S)

| Table | Key columns |
|---|---|
| `learners` | name, gender, date_of_birth, admission_number (unique per school), upi (nullable, unique), knec_assessment_number, kcse_index_number, birth_certificate_number (encrypted cast), residence (`day`/`boarding`), status (`active`/`transferred`/`alumni`), admitted_on, photo_path |
| `enrolments` | learner_id, stream_id, academic_year_id, combination_id (nullable) — current stream = enrolment for current year |
| `guardians` | name, phone, relationship, preferred_locale, receives_sms, user_id (nullable → parent login) |
| `guardian_learner` | guardian_id, learner_id, is_primary, is_payer |
| `learner_imports` | file_path, mapping json, status, counts json, errors_path |
| `discipline_incidents`, `learner_documents` | learner_id, … |

## Attendance (S)

| Table | Key columns |
|---|---|
| `attendance_registers` | stream_id, date, session (`morning`), taken_by, taken_at, sms_sent_at — unique(stream_id, date, session) |
| `attendance_entries` | register_id, learner_id, status (`present`/`absent`/`late`), note, updated_at |

## Assessment (S)

| Table | Key columns |
|---|---|
| `assessments` | stream_id, learning_area_id, term_id, type (`cat1`/`cat2`/`midterm`/`endterm`/`mock`), out_of, due_on, submitted_at, approved_by, locked_at |
| `marks` | assessment_id, learner_id, score (decimal 5,1 nullable), absent (bool), comment, entered_by, updated_at — unique(assessment_id, learner_id) |
| `strand_ratings` | assessment_id, learner_id, strand_id, level (`EE1`…`BE2`), comment |
| `competency_ratings` | learner_id, term_id, competency (enum of the 7), level (`EE`/`ME`/`AE`/`BE`) |
| `report_comments` | learner_id, term_id, class_teacher_comment, principal_comment, approved_at |
| `report_verifications` | code (8 chars), learner_id, term_id, pdf_path — powers "verify at …/r/{code}" |

## Fees (S)

| Table | Key columns |
|---|---|
| `vote_heads` | code (TUI, BRD, PE, ACT, EWC, RMI, LTT, MED, ADM), name, allocation_priority |
| `fee_structures` | level_id, academic_year_id, residence, version, status (`draft`/`published`), published_at, approved_by |
| `fee_structure_lines` | fee_structure_id, vote_head_id, term_number, amount_cents |
| `charges` | learner_id, term_id, fee_structure_id, vote_head_id, amount_cents (billed per learner per term) |
| `payments` | learner_id (nullable until matched), method (`mpesa`/`bank`/`cash`), amount_cents, mpesa_code (unique), payer_phone, account_reference (raw text typed), paid_at, matched_by, matched_at, receipt_number |
| `payment_allocations` | payment_id, charge_id, amount_cents — allocate by vote_head priority |
| `fee_waivers` | learner_id, term_id, percent / amount_cents, reason, status, approved_by |
| `stk_requests` | learner_id, phone, amount_cents, checkout_request_id, merchant_request_id, status (`pending`/`success`/`failed`/`timeout`), result_desc, payment_id |

Balance = sum(charges) − sum(allocations) − waivers, cached per learner per term (`learner_balances` view or table updated by events).

## Communication (S)

| Table | Key columns |
|---|---|
| `messages` | channel (`sms`/`notice`/`both`), audience json, template_key, body_en, body_sw, scheduled_for, sent_at, created_by, estimated_credits |
| `message_recipients` | message_id, guardian_id, phone, locale, rendered_body, segments, status (`queued`/`sent`/`delivered`/`failed`), provider_id, error |
| `notices` | title_en/sw, body_en/sw, audience json, published_at |
| `sms_opt_outs` | phone |

## Assignments (S)

| Table | Key columns |
|---|---|
| `assignments` | learning_area_id, teacher_id, term_id, type (`homework`/`practical`/`project`/`group_project`), title, instructions, attachment_path, due_at, accepts_late_until (nullable), group_size, marking (`rubric`/`score`), out_of |
| `assignment_stream` | assignment_id, stream_id |
| `assignment_submissions` | assignment_id, learner_id, group_key (nullable), note, submitted_at, is_late, level (`EE1`…`BE2`, nullable), score, feedback, marked_by, marked_at, returned_at — unique(assignment_id, learner_id) |
| `submission_files` | submission_id, path, mime, size_bytes, original_name |

Learners log in with `learners.user_id` → `users` (username = admission number, `must_change_password` flag).

## Timetable (S)

`periods` (number, label, starts_at, ends_at, kind `lesson`/`break`/`lunch`/`games`),
`timetable_slots` (term_id, stream_id, weekday 1–5, period_id, learning_area_id, teacher_id, room) — unique(term_id, stream_id, weekday, period_id) and unique(term_id, teacher_id, weekday, period_id) to block clashes.

## Integrations

### SMS — `App\Contracts\SmsGateway`

```php
interface SmsGateway {
    /** @return string provider message id */
    public function send(string $toE164, string $body, ?string $senderId = null): string;
}
```
Drivers: `fake` (default local/tests; logs to `storage/logs/sms.log` and records sent messages for assertions) and `africastalking` (or the school's chosen provider). Config `services.sms.driver`. Delivery reports hit `POST /webhooks/sms/delivery` (signed / IP-allowlisted). Count segments with the same GSM-7/UCS-2 rules as `resources/js/ui/sms-counter.js` (port to `App\Support\Sms::segments()` and test both agree).

### M-Pesa (Daraja) — `App\Contracts\MpesaGateway`

```php
interface MpesaGateway {
    public function stkPush(string $phoneE164, int $amountCents, string $accountReference, string $description): StkPushResult;
    public function stkQuery(string $checkoutRequestId): StkStatus;
}
```
- Drivers: `fake` (default; `success` after 5 s, or a configurable failure) and `daraja` (sandbox first).
- STK push: Daraja wants whole shillings, so **round up / reject cents** and say so in the UI. AccountReference = admission number without slashes (`MVS0412`).
- Callbacks, each idempotent on `mpesa_code` / `checkout_request_id`:
  - `POST /webhooks/mpesa/stk` updates `stk_requests` and creates the `payment`.
  - `POST /webhooks/mpesa/c2b/validation` and `POST /webhooks/mpesa/c2b/confirmation` handle Paybill payments made from the phone menu.
- Auto-matching: account reference → learner by normalized admission number. If that fails, match by payer phone → guardian → single learner. Anything else goes to "Needs a match" with suggestions (fuzzy name match on the typed text, siblings → split).
- Never log full callback payloads containing phone numbers at info level; mask them.

### PDF

Report forms and receipts use `spatie/laravel-pdf` (Browsershot), rendering the same Blade print views as the screen at A4 with `@page { size: A4; margin: 0 }`. Fall back to dompdf if Chrome isn't available on the server; keep the print CSS simple (tables, no grid gaps) so both work.

## Demo seed (fictional — required)

`DatabaseSeeder` builds **Mlima Vista Secondary School, Nakuru County** (short "Mlima Vista Sec.", Paybill `000000`) with:
- Year 2026; terms 05/01–02/04, 27/04–31/07, 24/08–20/11 (current; half-term 01/10–04/10); next term opens 04/01/2027.
- Levels/streams: Grade 7–9 (Blue, Green, Yellow, White), Grade 10 (Blue, Green, Yellow, White, Red), Form 3 & Form 4 (East, West, North, South, Central, Rift). ~1,284 learners total via factories; Grade 9 White has no class teacher.
- Named staff: Mrs. Jane Wambugu (principal), M. Otieno (bursar), Mr. Barasa Simiyu (Mathematics HOD, class teacher Grade 9 Blue), Ms. Wanjiru Kamau (class teacher Grade 8 Blue), plus the teachers listed in SCREENS.md §S3.
- Named learners: Chebet Kiprono (MVS/2024/0412, UPI 9KTR4M2, KNEC 41736104008, Grade 8 Blue, balance KES 18,750.00, guardians Daniel Kiprono +254712000481 primary payer and Sarah Jepkosgei, prefers sw), Mageto Joshua, Amina Hassan, Mutua Musyoka (Form 4 East), Otieno Odhiambo (Grade 10 Blue, STEM), Njeri Wambui, Naserian Lekishon, Kerubo Nyaboke, Halima Abdi.
- Login users: `principal@mlimavista.test`, `bursar@mlimavista.test`, `simiyu@mlimavista.test` (password `password`, local only), parent phone +254712000481 (OTP printed to the log by the fake SMS driver).
- M-Pesa codes in fixtures: `TEST12AB34`, `TEST48KQ71`, `TEST93LM20`, `TEST30QW11` (unmatched, typed "chebet"), etc.
- Otieno Odhiambo (Grade 10 Blue, STEM · Pure Sciences) has a learner login `MVS2026-0067` / `password` (local only, must change on first login), 4 to-do assignments (one late, one due today), 1 submitted and 6 marked, and CAT 1 + mid-term results as in S18.
- A second school "Mto Safi Comprehensive School, Kisumu County" for the tenancy test and school switcher.
