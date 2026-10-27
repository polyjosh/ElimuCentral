# ElimuCentral — screen specifications

The design source of truth for building. Each section describes a screen from the design canvas
(https://claude.ai/artifact/FJjtFFpYfPaxppzYauAEVa — private; humans only).
Component names refer to `resources/views/components`. Sample data is fictional and matches the seeders.

Conventions for every screen:
- Staff screens use `<x-layouts.app>` (sidebar from `lg` up). Parent, teacher and learner phone screens use the same layout; below `lg` they get the app bar and bottom nav.
- Page header: breadcrumbs when the page is more than one level deep, then an H1 (`text-3xl font-bold`), then a muted subline, with actions at the right. Actions wrap under the title on phones.
- Content max width is 1360 px, padding is 24 px (16 px on phones) and gaps between sections are 24 px.
- Grids of cards use `grid-cols-[repeat(auto-fit,minmax(min(420px,100%),1fr))]`, and stat rows use `minmax(240px,1fr)`.

---

## S1 Login — `/login`

**Desktop:** a split screen that stacks on phones (`flex-wrap`, each half `flex: 1 1 420px`).
- Left panel, solid `#0F5634` with white text:
  - Inverted logo and wordmark.
  - School name at 36 px bold, with "Nakuru County · CBE & 8-4-4" under it.
  - Two lines with ochre check icons: "Staff log in with email or phone and password", "Parents get a one-time code by SMS — no password".
  - Footer: the school office phone.
- Right side, a form with max width 420 px:
  - Language segmented control (English | Kiswahili) at the top right.
  - H1 "Log in" with the subline "Welcome back. Choose how you use ElimuCentral."
  - Segmented tabs: **School staff** | **Parent / guardian**.
  - Staff form:
    - "Email or phone number" field (`autocomplete="username"`).
    - Password field with a show/hide icon button and a "Forgot password?" link.
    - "Keep me logged in on this computer" checkbox.
    - Large "Log in" primary button, full width.
    - Error alert: "That email and password don't match. 3 tries left before a 15-minute pause." Throttle 5 attempts per 15 min per identifier and IP.
  - Footnote: "Shared computer? Untick 'keep me logged in' and log out when done."

**Parent (phone, 390 px):**
- App bar with logo, school name, and an EN/SW segmented control.
- H2 "Log in as a parent" / "Ingia kama mzazi" with the subline "No password needed. We send a code to your phone."
- Tabs Parent | Staff.
- Step 1: `<x-phone-input>` and a "Send code" button.
- Step 2:
  - Phone shown disabled with a "Change number" link.
  - Success alert: "We sent a 6-digit code by SMS to +254 712 000 481."
  - Six one-digit inputs, each 56 px tall with 24 px bold text. The first has `autocomplete="one-time-code"`; pasting six digits fills all of them; focus auto-advances.
  - "Didn't get it? Resend in 0:42" countdown.
  - xl primary "Verify and log in".
  - Error: "Wrong code. Check the SMS and try again. 2 tries left."
- Footer: "Shared phone? You will be logged out after 30 minutes without use." and help phone.
- OTP rules: 6 digits, 5-minute expiry, 3 attempts, 60 s resend cooldown, max 5 SMS per number per hour. Parent sessions expire after 30 minutes idle.
- Every string has a Kiswahili version (see lang file).

## S2 Principal dashboard — `/dashboard`

- Header: "Tuesday 27/10/2026 · Term 3, week 10 of 13" (muted), then H1 "Good morning, Mrs. Wambugu" (greeting by time of day).
- Actions: secondary "Term summary (PDF)" and primary "Send notice" (goes to S13).
- **Stat row (4 tiles):**
  - Learners enrolled 1,284, with curriculum badges "CBE 746", "8-4-4 538".
  - Present today 93.2%: "1,197 of 1,284 · **87 absent** (danger) · 24/24 registers in".
  - Fees collected · Term 3 "KES 17.40M" (exact in title), progress bar 70%, "70.0% of KES 24.86M expected".
  - Waiting for your approval "5": accent-tinted tile with a "Review now →" link to the approvals card.
- **Row 2 (two cards):**
  - **Enrolment by level and stream:**
    - One row per level: label, a bar proportional to size (CBE levels green, 8-4-4 levels `border-strong` grey), the stream breakdown text under the bar ("Blue 44 · Green 43 · …") and the count.
    - Footer: legend and "28 streams · average 45.9 per stream".
  - **Attendance today by level:**
    - Per level: a bar of % present, then "92.9% · 12 abs."
    - Warning alert for the worst stream, e.g. "Grade 10 Green: 9 absent (19.6%) — highest this term. View register".
- **Row 3:**
  - **Fee collection vs expected · Term 3:** per level, a green collected bar on an ochre-soft track, "2.79 / 3.44" in KES millions. Footer: legend and "412 learners with a balance".
  - **Upcoming events:** next 5 events. Each has a date block (weekday, big day number, month), a title and a detail line.
- **Row 4:**
  - **Learning area performance · end-term:**
    - Segmented control "CBE · Junior School" | "8-4-4 · Form 4".
    - CBE view: one 100% stacked bar per learning area in the order EE (primary), ME (`#74C093`), AE (accent), BE (danger-solid), with "64% ≥ ME" at the right. Each bar has an `aria-label` listing all four percentages. Legend above it.
    - 8-4-4 view: mean points per subject (bar out of 12) with `<x-kcse-grade>`.
  - **Waiting for approval (count badge):**
    - Rows: icon avatar, title, detail, and a secondary "Review" button.
    - Approval types: marks submitted, fee waiver, transfer admission, report comments, staff leave.
- All widgets are lazy (`#[Lazy]`) with skeleton placeholders. Each widget fails independently (see S15).

## S3 Academic setup — `/setup/academic`

- Two columns: a sticky sub-nav on the left (Year & terms · Levels & streams · Learning areas · Pathways & combinations · Grading scales · Report form settings) and content on the right. The sub-nav stacks above the content on phones.
- **Year & terms card:**
  - Year select, "Set up 2027" button and a "Current" badge.
  - Table: Term, Opening date, Half-term break (range), Closing date, Weeks, Status (Closed / In session / Upcoming).
  - Closed terms are locked (disabled "Locked" action). The current term's dates are editable inline with `<x-date-input>` and Save.
  - Footer: "Next term opening date is printed on every report form and in the closing-day SMS: Monday 04/01/2027 · Change".
- **Levels & streams card:**
  - Actions: "Add level" and "Add stream".
  - Per level row:
    - Name, curriculum badge, and band ("Junior School", "Senior School · pathways", "Secondary · phasing out 2027", "KCSE candidates 2026").
    - Stream pills: avatar initials of the class teacher, "**Blue** · Ms. Akinyi Atieno (44)".
    - A stream without a class teacher shows a dashed warning pill: "White (44) — no class teacher".
- **Learning areas card (Junior School):** table of name, code, lessons/week, type (Core / Choice of one), strands assessed, head of department.
- **Pathways & combinations card:**
  - Intro: "Every Grade 10 learner takes the core subjects (English, Kiswahili, Mathematics, Community Service Learning, Physical Education) plus one combination of three electives."
  - Three pathway cards (STEM, Social Sciences, Arts & Sports Science) with `<x-pathway-badge>` and the learner count.
  - Each combination shows its name, its three subjects, "48 / 48" and a capacity bar (ochre when full).
  - "Add combination" button.

## S4a Learners list — `/learners`

- Header: H1 "Learners", subline "1,284 active · 746 CBE · 538 8-4-4". Actions: "Import from Excel" (S5), "Export", primary "Add learner".
- Tabs: Active (1,284) · Transferred out (23) · Alumni (2,906).
- Toolbar:
  - Search "Name, adm. no., UPI or guardian phone" (`wire:model.live.debounce.400ms`).
  - Selects: Level, Stream, Curriculum, Pathway.
  - Active-filter chips with a remove button.
  - All filters live in `#[Url]` so URLs are shareable.
- `<x-bulk-bar>` when rows are selected: "Send SMS to guardians", "Move to stream…", "Print report forms", "Clear selection".
- `<x-data-table>` columns:
  - Checkbox.
  - Learner: avatar, name, "F · 13 yrs", plus a "New" badge if admitted under 14 days ago. Links to S4b.
  - Adm. no., UPI, Stream.
  - Curriculum badge, Pathway badge (or "—").
  - Guardian: name and masked phone.
  - Attendance % (warning colour below 85%).
  - Balance KES (right-aligned, danger when ≥ 10,000; "Waiver pending" accent badge when relevant).
  - Sortable; `aria-sort` on the sorted header.
- Pagination: "Showing 1–25 of 412". On phones it's a card list with "Load 25 more".
- **Phone cards (S4 mobile):**
  - App bar with "Learners", add icon and avatar.
  - Search field and horizontally scrolling filter chips.
  - "48 learners" and a sort button.
  - Each card: checkbox, avatar, name, "MVS/2024/0412 · Grade 8 Blue", chevron, then "Balance KES 18,750.00" and "Attendance 91%". Selected cards get a 2 px primary border.
  - Sticky inverse bulk bar: "2 selected · Send SMS · Move · ✕".

## S4b Learner profile — `/learners/{learner}`

- Breadcrumbs: Learners › Grade 8 Blue › Chebet Kiprono.
- **Header card:**
  - Photo (`<x-avatar size="xl" :src>`, 112 px, initials fallback), name (H1) and an "Active" badge.
  - "Grade 8 Blue · CBE badge · Junior School · Day scholar · Pathway: chosen in Grade 9".
  - "Female · born 14/03/2013 (13 yrs) · Class teacher Ms. Wanjiru Kamau".
  - Actions: "SMS guardian", "Edit", and a more-menu.
  - ID grid: Admission no., UPI, KNEC assessment no., Birth certificate no. (partly masked), Admitted, House/dorm.
  - Guardian cards: avatar, name, "Primary contact" badge, "Father · +254 712 000 481 · receives SMS · M-Pesa payer", language preference, and a call icon link (`tel:`).
- **Tabs (links):** Academics · Attendance (91%) · Fees (KES 18,750 in a danger badge) · Discipline (1) · Documents (4).
- **Academics tab:**
  - Table per learning area: CAT 1, Mid-term, End-term scores, a Level `<x-rubric>` (or a "Not entered" warning badge) and the teacher.
  - Side summary card: Attendance (91%, 42 of 46 days, bar, absences), Fees (balance, last payment, "Send fee reminder"), Discipline, Documents.
- **Other tabs:**
  - Attendance: calendar or list of days with P/A/L.
  - Fees: statement (charges, payments with M-Pesa codes, balance).
  - Discipline: incident list.
  - Documents: uploaded files via `<x-file-upload>`.

## S5 Bulk import learners — `/learners/import`

- Focused layout without the sidebar:
  - Top bar with "← Cancel import" and the title.
  - Stepper: Upload ✓ · Map columns ✓ · **Preview & fix** · Confirm. Use `aria-current="step"`.
- Step 1: `<x-file-upload accept=".xlsx,.csv">` up to 5 MB, plus a "Download template" link. After upload, a summary card shows the file, sheet, row and column counts, and "Replace file".
- Step 2, column mapping:
  - Excel header (monospace chip) → app field, with a status badge: Matched, "Auto: '8B' → Grade 8 Blue", "Auto: 07… → +2547…", or Ignored.
  - Required fields: full name, admission no., date of birth, gender, stream, guardian name, guardian phone. Optional: UPI, KNEC no., residence.
  - Auto-match by header synonyms.
- Step 3, preview:
  - Summary tiles: Rows read, Ready (success), Errors — will be skipped (danger), Warnings — will import (warning).
  - Tabs: Needs attention / All rows.
  - Table with row number, the fields, the bad cell outlined red, a problem message and a "Fix" button that opens an inline edit.
  - Errors: duplicate UPI (name who has it), impossible date, unknown stream, missing required field, phone too short.
  - Warnings: missing UPI, phone shared with others (siblings).
- Sticky footer:
  - "Import 162 learners into Grade 8": "159 ready + 3 with warnings · 6 rows with errors are skipped — download them, fix, and import again."
  - Buttons: "Download 6 error rows" (xlsx) and "Continue to confirm".
- Step 4: confirm modal, then a queued job with a progress view, then a done toast.

## S6 Teacher home (phone) — `/teacher/today`

- App bar: "Today" with "Tuesday 27/10/2026 · 07:52", a bell and the avatar. H3 "Good morning, Mr. Simiyu".
- **Register card** (accent-soft, accent border) when the class register is not taken:
  - "Your class · due by 08:00".
  - "Morning register — Grade 9 Blue" with "44 learners · not taken yet".
  - xl primary "Take register" (S7).
  - Once taken, it collapses to a success line.
- **Today's lessons** from the timetable:
  - Each lesson shows time and duration, learning area, "Grade 9 Blue · Room 9B", and a badge (Next / Cover).
  - Free periods are muted.
  - "Week" link.
- **Marks to enter (count):** per assessment, "Grade 8 Blue · End-term", "38 / 42", a progress bar and "4 learners left · saved on this phone". Links to S8. Shows the deadline.
- Bottom nav: Today · Register · Marks · Timetable · More.

## S7 Take attendance (phone) — `/attendance/{stream}/{date}`

- App bar: back button, "Grade 9 Blue register", "Tue 27/10/2026 · morning", search icon.
- Sticky counts row with three tiles, Present / Absent / Late (`aria-live`), and a "Mark all present" ghost button.
- Learner rows:
  - Avatar, name (ellipsis) and "MVS/2024/0386 · Absent".
  - Three 44×44 toggle buttons **P / A / L** in a radiogroup: primary fill when present, danger-solid when absent, accent when late.
  - The row background tints danger-bg for absent and warning-bg for late.
- Default is everyone present, so one tap marks an exception.
- Sticky footer:
  - Checkbox "SMS guardians of 2 absent learners" with the exact message preview:
    "Dear parent, {first name} was absent from school today 27/10/2026. Please contact the class teacher. Mlima Vista Sec." (Kiswahili for guardians who prefer it).
  - xl primary "Save register (44 learners)".
- Offline: saves to IndexedDB with the banner "Register saved on this phone. SMS to 3 guardians will send when you're back online." Sync on reconnect.

## S8 Marks entry — `/assessments/{assessment}/marks` (desktop and tablet; usable on phones)

- Breadcrumbs: Marks entry › Grade 8 Blue · Mathematics.
- Save status badge in the header: "All changes saved · 10:42" / "Saving…" / "Saved on this device · 4 waiting" / "Couldn't save — retrying".
- Header: CBE badge, "Term 3, 2026 · deadline Fri 13/11", H1 "Mathematics — Grade 8 Blue". Actions: "Paste from Excel" and primary "Submit for approval", disabled with "(4 missing)" until complete.
- Toolbar:
  - Stream select, learning area select.
  - Assessment segmented control: CAT 1 / Mid-term / End-term.
  - "Out of [100]".
  - Mode segmented control: **Score → level** | **Rubric per strand**.
- **Score mode:** the grid in `resources/views/livewire/marks-entry.blade.php` (already built in the kit):
  - Columns: #, learner, adm., score input, auto level `<x-rubric>`, optional comment.
  - Invalid cells are red with "Max is 100". "AB" shows an "Absent" badge.
  - Keyboard legend under the grid.
- **Side panel:**
  - Progress: "38 / 42", mean "58.6 · ME1", distribution bars EE/ME/AE/BE with counts, and "2 blank · 1 invalid · 1 absent".
  - Rubric-per-strand preview for the selected learner: `<x-rubric-picker>` per strand (Numbers, Measurements, Geometry, Data handling, Algebra) and a strand comment textarea.
  - Info alert: "Marks save on this device every few seconds and sync when online. You can close the page safely."
- "Paste from Excel": paste a column of scores into the focused cell and fill downward.
- Submit sends it to the HOD/principal approval queue and locks the assessment (`locked_at`).

## S9 Term report form

**Screen (parent phone):**
- App bar: back, "Term 3 report", download icon.
- Header card:
  - Avatar, name, stream, adm.
  - Badges: CBE, Term 3 2026, End-term.
  - Overall: "Mostly Meeting Expectations", count chips (EE × 3, ME × 5, AE × 1).
  - "Class position is not shown — this school has turned it off" (only when the setting is off).
- Learning areas as `<details>` accordions:
  - Each summary shows the name, "58 / 100 · Mr. B. Simiyu" and a rubric chip.
  - The expanded body lists strands with chips and the teacher's comment.
- Core competencies card with 7 rows and a 4-level chip each:
  - Communication & collaboration
  - Critical thinking & problem solving
  - Creativity & imagination
  - Citizenship
  - Digital literacy
  - Learning to learn
  - Self-efficacy
- Class teacher and principal comments.
- Card with attendance, fee balance (danger), next term opening date and "Pay with M-Pesa".
- "Download A4 report (PDF, 86 KB)".

**A4 print, CBE** (`/reports/{learner}/{term}.pdf`; 794×1123 px, 36/40 px margins, 13 px body, white, prints well in greyscale):
- Header:
  - Logo, school name in caps, address and phone.
  - "Learner Progress Report · Junior School · Term 3, 2026" with a CBE badge.
  - 2 px green rule under it.
- Learner box: name, grade/stream, adm. no., UPI, KNEC assessment no., class teacher, and a photo box.
- Table: Learning area | Score | Level (chip) | Strand highlights & teacher's comment | Teacher initials.
- Key line with all 8 level ranges and "Bars on each level: 4 = EE, 3 = ME, 2 = AE, 1 = BE".
- Two columns:
  - Core competencies with chips.
  - Attendance and overall (and "Position in stream: 12 of 42" **only if the school enables it**).
  - Ochre "Fees & next term" box: fee balance, next term's fees, closing date, next term opens, and "Pay via M-Pesa Paybill …, account MVS0412".
- Class teacher and principal comment boxes with signature lines and a stamp box.
- Footer: "Generated by ElimuCentral · date · verify at …/r/{code}" and the page number.

**A4 print, 8-4-4 KCSE:**
- Same header ("Student Report Form · Form 4 · Term 3, 2026 · Pre-KCSE (mock)", 8-4-4 badge).
- Learner box with the KCSE index no.
- Big **mean grade** box ("B", "8.86 points").
- Table: Subject | Marks % | Grade | Points | Dev. vs last term | Remarks | Teacher.
  - Subjects outside the best 7 are hatched and marked "(not counted)".
  - Footer row: "Total (best 7) 465 / 700 · B · 62 / 84 · Mean 8.86 · Term 2: 8.29 (B-)".
- Grading key and the best-7 rule: English, Kiswahili, Mathematics, best 2 sciences, best humanity, best remaining.
- Attendance, conduct, positions (when enabled), mean grade trend, fee box, comments and signatures.

## S10 Fees: bursar dashboard — `/fees`

- Header: "Paybill 000000 · Account = admission no. (e.g. MVS0412)", H1 "Fees · Term 3, 2026". Actions: "Export to Excel" and primary "Record bank / cash payment".
- Stat row:
  - Expected this term KES 24,860,000.00.
  - Collected KES 17,402,300.00 with a bar and "70.0% · +KES 486,250.00 today".
  - Outstanding (danger) "412 learners · 96 owe over KES 20,000".
  - Accent tile "M-Pesa to match 7 · KES 61,900.00 waiting →".
- **Collected vs expected by vote head:**
  - Segmented control: All levels / Day / Boarding.
  - Two-column list of vote heads, each with a bar and "5.33 / 6.42 · 83%".
  - Note: "Payments are allocated to vote heads in the order set by the Board (tuition first)."
- **M-Pesa payments · matched automatically:** time, code, learner and stream, amount, "Receipt" link. Badge "32 today". The card scrolls.
- **Needs a match:** per payment, show code, amount, masked phone, and the account text the payer typed. Possible results:
  - "Suggested: Chebet Kiprono (MVS0412)" with primary "Match" and secondary "Other…".
  - "No match found" with "Find learner…".
  - "Split between siblings?" with "Split…".
- **Defaulters:**
  - Filters: "Balance over KES 5,000", level.
  - Bulk bar: "48 of 214 selected", primary "Send SMS reminder (48)", "Print statements", "Select all 214".
  - Table: checkbox, learner (with "Waiver pending" badge), stream · day/boarding, guardian and masked phone, Billed, Paid, Balance ↓ (danger), last reminder ("13/10 · SMS delivered", "failed" in danger, "Paid today — excluded").
  - Sending opens a confirm modal: "Send 48 fee reminders?" with SMS credit count.

## S11 Fee structure editor — `/fees/structures/{structure}`

- Breadcrumbs: Fees › Fee structures › Grade 10 · Boarding · 2026. A "Draft — 1 unpublished change" warning badge sits in the header.
- Actions: "Copy from 2025", "Discard draft", primary "Review & publish".
- Selectors: Level, Year, Residence segmented control (Day (118) | Boarding (112)).
- Table:
  - Rows are vote heads with codes.
  - Columns: Term 1 / Term 2 (closed, disabled inputs), Term 3 (editable `<x-currency-input>`-style cells), Year total.
  - Changed cells are tinted ochre with "was 3,750.00" under them.
  - Sticky footer row with per-term totals and the year total.
- Side panel:
  - "Effect of publishing": "112 boarding learners … billed KES 300.00 more for Term 3 …", expected rise, and a checkbox "SMS guardians about the change".
  - Compare Day vs Boarding.
  - Version history.
- Publishing creates an immutable version. Changing billed amounts for an open term needs principal approval.

## S12 Parent home (phone) — `/parent` · **most important screen**

- App bar: logo, school short name, "SW"/"EN" toggle, bell with a count.
- H3 "Hello, Daniel" / "Habari, Daniel".
- **Child switcher:** horizontal pill buttons with avatar, first name and stream. The selected one has a 2 px primary border and primary-soft fill. It's a tablist, and the choice is remembered.
- **Fee card:**
  - "Fee balance · Term 3" with a warning badge "Due 30/10".
  - Amount at 36 px extra-bold.
  - Progress bar and "Paid this term: KES 0.00 of 18,750.00".
  - **xl primary "Pay with M-Pesa"** with a phone icon (60 px, full width).
  - "Paybill 000000 · Account MVS0412" and a "Statement" ghost link.
- **Latest results** (mid-term or end-term):
  - Each learning area shows its rubric chip.
  - Info alert explaining EE/ME/AE/BE in plain words ("More bars = better").
  - "See full report" button (S9 screen).
- **Attendance this term:** "91%", "Present 42 of 46 days", then a 10-column grid of the last 20 days. Present is ✓ on success-bg, absent is ✕ on danger-solid, late is L on warning. Legend below. The grid has an `aria-label` summary.
- **Notices:** date blocks with title and detail. "See all".
- Bottom nav: Home · Fees · Results · Messages · More (Nyumbani · Karo · Matokeo · Ujumbe · Zaidi).
- Dark theme and Kiswahili must both work (designed variant: dark + SW).

**M-Pesa STK push flow** (four screens, `/parent/pay`):
1. **Amount & phone:**
   - Learner card.
   - "How much do you want to pay?" amount field (56 px, 24 px bold, prefilled with the balance).
   - Quick chips: Full balance / KES 5,000 / KES 10,000.
   - "M-Pesa phone number" `<x-phone-input>` (prefilled), hint "The M-Pesa prompt goes to this phone. It can be a different phone from yours."
   - Info alert with a lock icon: "You will type your M-Pesa PIN on the M-Pesa prompt — never in this app."
   - xl "Continue".
2. **Confirm:**
   - "You are paying KES 18,750.00" (40 px).
   - Summary list: To school, Paybill, For learner, Account, From phone, Balance after (success).
   - Fee note.
   - xl "Send M-Pesa prompt" and secondary "Change amount".
3. **Waiting:**
   - Phone illustration in a primary-soft circle (no animation).
   - "Enter your M-Pesa PIN — An M-Pesa prompt was sent to +254 712 000 481."
   - Steps: Request sent ✓, Waiting for your PIN (spinner, "Prompt expires in 0:47"), Receipt.
   - `<details>` "No prompt? Pay with Paybill instead" with manual steps.
   - "Cancel".
   - Poll the payment status every 3 s, for up to 90 s, with `wire:poll.3s.visible`.
4. **Receipt:**
   - Success check, "Asante! Payment received", amount.
   - List: M-Pesa code, receipt no., date/time, learner, paid by (masked), new balance.
   - "We also sent this receipt by SMS."
   - "Download receipt (PDF, 24 KB)" and xl "Done".
   - **Failure variant:** "Payment not completed — the prompt was cancelled or timed out. No money left your account." with "Try again" and "Pay with Paybill".

## S13 Send SMS / notice — `/messages/new`

- Breadcrumbs: SMS & notices › New message. The header shows an "SMS credit: 4,820" badge.
- **1. Who should get it?** Radio cards:
  - Whole school (counts).
  - A level.
  - A stream.
  - **Fee defaulters**, which reveals: Balance over (currency), Levels select, and "Skip anyone who paid in the last 3 days".
  - Live result line: "214 learners → 241 guardian numbers (duplicates and siblings merged, 6 numbers opted out) · See list".
- **2. What should it say?**
  - Template select (Fee reminder, Absence, Closing day, Meeting, Custom).
  - "Send as" segmented control: SMS / App notice / Both.
  - Language tabs English | Kiswahili, each with a "1 SMS" badge.
  - Message editor showing `{tokens}` highlighted: `{guardian_first_name} {learner_first_name} {balance} {adm_no} {stream}`.
  - Counter (`smsCounter` in `resources/js/ui/sms-counter.js`): "123 / 160 characters", bar, "1 SMS per number", and insert-token buttons.
  - Warning alert when the longest real values exceed one part: "With the longest names filled in, 12 messages reach 171 characters and will be sent as 2 SMS each. Shorten the text by 11 characters to keep everyone at 1 SMS."
  - Note about Kiswahili preference counts, and that curly quotes or emoji switch to Unicode with a 70-character limit.
- **3. When?** Send now / Schedule (date + time); "No SMS after 20:00".
- Side panel:
  - **Preview with real data:** two phone-style bubbles, English to one guardian and Kiswahili to another, each with character and SMS counts.
  - **Summary:** recipients, SMS used ("253 credits (229 × 1 + 12 × 2)"), credit after, send time.
  - Primary lg "Review & schedule 241 SMS" (confirm modal) and "Send a test to my phone".
- Sending queues jobs in batches of 100. A "Sent" view shows delivery reports.

## S14 Timetable — `/timetable`

- Header: "Term 3, 2026 · 40 lessons of 40 minutes per week", H1 "Timetable — Grade 8 Blue". Actions: "Print A4" and primary "Edit timetable".
- Toolbar: "View by" segmented control (Stream | Teacher | Room), stream select, and a danger alert for clashes ("1 clash: Mr. Kamau Gitau is also in Grade 9 Green on Thursday, lesson 4. Resolve").
- Weekly grid (CSS grid, `role="grid"`):
  - Rows are periods with times: Lesson 1 08:00–08:40 … Lesson 8 14:40–15:20, plus short break, tea break, lunch, and games & clubs 15:20–16:30. Breaks are full-width hatched bands.
  - Columns are Mon–Fri. Today's column header is primary-coloured.
  - Each slot shows the learning area (bold), teacher and room, on a soft tint per subject. The current lesson has a primary outline and a "Now" badge; a clash has a danger inset border and a "Clash" badge.
- Below 768 px it becomes a Mon–Fri segmented day picker with a single-column list.
- Edit mode opens slots in a drawer. The server checks for clashes on save.

## S15 Empty, loading, error and offline states

- **Dashboard loading:** static skeleton blocks (title bar, two stat tiles, a card with 4 lines) plus "Loading this term's figures…" with a small spinner.
- **Dashboard empty (new school):** "Karibu, Mlima Vista!", progress bar and a setup checklist:
  - Set academic year & terms (done, struck through)
  - Add levels & streams (done)
  - **Import learners from Excel** (current, with a "Start" button)
  - Invite teachers
  - Set fee structures
- **Dashboard partial error:** working tiles stay, and the failed tile shows "Couldn't load" with "Retry". Danger alert: "Fee figures are temporarily unavailable. The payments service didn't respond. Everything else is up to date. Error ref EC-5021 · 10:46".
- **Learners list:**
  - Loading: skeleton rows.
  - Empty: "No learners in Grade 8 Blue", "Import from Excel" and "Add one learner".
  - No results: 'No one matches "Kiprotich" in Form 3' plus a smart hint "There is a Kiprotich Dennis in Grade 8 White", "Search all levels" and "Clear filters".
  - Error: "We couldn't load learners", "Check your connection and try again… ref EC-4410" and "Try again".
- **Offline banner** (`<x-offline-banner>`, inverse colours, top, `aria-live`):
  - Text: "You're offline. Your marks are saved on this device and will sync when you're back online."
  - Shows a "4 waiting" pill.
  - Cell status in grids: "Not synced" (warning) / "Synced" (success).
  - Toasts: "Back online. 4 marks synced." and the conflict toast "1 mark wasn't saved — someone else changed it. Compare".
- Error refs: every user-facing server error shows a short reference (`EC-####`) that's logged with the exception.

---

## Learner app (phone) — Junior and Senior School learners

Learners log in with **admission number + password**. The school issues a starting password and the learner must change it on first login; there is no OTP, because many learners don't have their own phone line.
Learner screens never show fee balances or class position. They are always on a phone, and the bottom nav is **Today · Assignments · Results · Notices · More**.
Sample learner: Otieno Odhiambo, Grade 10 Blue, STEM · Pure Sciences.

## S16 Learner today + timetable — `/learner`

- App bar:
  - Logo with "Today" and the date.
  - SW/EN toggle.
  - Bell.
- Greeting row:
  - Avatar.
  - "Hi, Otieno".
  - "Grade 10 Blue" and a STEM pathway badge.
- **Current lesson card** (solid primary, `aria-label="Current lesson"`):
  - "NOW · UNTIL 10:50" with "20 min left".
  - Learning area at 22 px bold ("Chemistry").
  - "Mr. A. Hassan · Lab 1".
  - Thin ochre progress bar for the time elapsed.
  - Divider, then "**Next:** 11:20 Core Mathematics · Room 10B".
  - Outside lesson time, show the next lesson, or "No more lessons today".
- **Timetable card:**
  - Header "Timetable" with "Term 3 · week 10".
  - Mon–Fri day picker:
    - 5 equal tabs, 52 px tall, weekday and date number.
    - Selected tab is a white raised pill with primary text.
    - Today is selected by default.
  - Lesson list for the chosen day, in a tabpanel:
    - Each lesson shows time (plus duration on the first), learning area, teacher and room.
    - Finished lessons are muted with "Done".
    - The current lesson sits on a primary-soft background with `aria-current="time"` and a "Now" badge.
    - Optional badges: "Homework due Thu" (accent), "Bring kit".
    - Breaks (short break, tea break, lunch, games & clubs) are thin hatched rows.
  - Data comes from `timetable_slots` (S14) for the learner's stream.
- **Due soon card:**
  - Up to 3 assignments sorted by urgency, with "All assignments" link.
  - Each has an icon chip, title and status line:
    - Late: danger, "1 day late · was due Mon 26/10".
    - Due today: warning, "Due today, 16:00".
    - Upcoming: muted, "Due Thu 29/10".
- **Latest notice** as an info alert.

## S17 Learner assignments — `/learner/assignments`

- App bar: "Assignments", SW/EN toggle, avatar.
- Tabs (links): **To do (4)** (danger count) · Submitted (1) · Marked (6).
- Subject filter chips that scroll sideways ("All subjects", then one per learning area the learner takes).
- **Assignment card:**
  - Status badge:
    - "1 day late" (danger, card border danger-bd).
    - "Due today, 16:00" (warning).
    - "Due Thu 29/10" (info).
    - Plain date when more than 3 days away.
  - "Subject · Type" (Homework / Practical / Project / Group project).
  - Bold title, and a muted line with the teacher and rules ("accepts late work until Fri 30/10", group members).
- **Open assignment** (2 px primary border; on phones this can be the detail route `/learner/assignments/{id}`):
  - Instructions.
  - "Teacher · set date · marked on a rubric (EE / ME / AE / BE)".
  - Attachment row (file name, type and size, download icon).
  - "Your work":
    - Two large dashed pickers: **Take photo** (`accept="image/*" capture="environment"`) and **Choose file**.
    - Each picked file appears as a thumbnail row with name and "Shrunk to 180 KB to save data". Images are resized in the browser to 1280 px / WebP before upload. Each row has a 44 px remove button.
    - Optional "Note to your teacher" textarea.
    - xl primary **Submit work**.
    - Helper: "You can change your work until 16:00. No signal? It's saved on this phone and sends when you're back online." This uses the offline queue (`offline-queue.js`) with the files kept in IndexedDB.
  - After the deadline, submitting marks the work late and needs `accepts_late_until`. Show "Submitted · 27/10 15:12" with a success badge and "Change submission" until the deadline.
- **Recently marked:** title, "Marked 23/10 · teacher", `<x-rubric>` level, and the teacher's feedback quote.
- Teacher side (built in the same phase, desktop and phone):
  - Create assignment: stream(s), learning area, type, title, instructions, attachment, due date and time, late policy, group size.
  - Submissions list per assignment: submitted / late / missing counts, open each, rubric level and feedback, return.
  - "SMS guardians of learners who haven't submitted" bulk action.

## S18 Learner results — `/learner/results`

- App bar: "Results", SW/EN toggle, avatar.
- Term select, then assessment segmented control **CAT 1 · Mid-term · End-term**:
  - Assessments that haven't been released are disabled.
  - Note under it: "End-term results are released on Fri 20/11".
  - Results show only after the principal approves the marks (S8 submit → approve).
- **Overall card:**
  - Badges: CBE, stream, pathway and combination ("STEM · Pure Sciences").
  - "Overall, mid-term" then H1 "Mostly Meeting Expectations" (the most common band).
  - A 12 px stacked bar of EE / ME / AE / BE counts, with an `aria-label` reading the counts.
  - Count chips "EE × 3", "ME × 4", "AE × 1".
  - Success alert with encouragement from the data: "You went up in 5 of 8 subjects since CAT 1. Biggest jump: Core Mathematics +9." If nothing improved, show a neutral tip instead.
- **Two grouped cards:**
  - **Core subjects** and **Pathway subjects · STEM** (for Junior School: just "Learning areas").
  - Each row shows the subject, then "64 / 100 · ↑ up 4" (success), "↓ down 2" (danger) or "same" (muted), and an `<x-rubric>` chip at the right. The change is always written in words, never shown by arrow or colour alone.
  - Rows expand (`<details>`) to show the teacher's comment.
- Info alert explaining the levels: "EE Exceeding · ME Meeting · AE Approaching · BE Below expectations. 1 is the higher half of each level. More bars = better."
- Button to the previous term's full report (S9 screen view, without the fee box for learners).
