<?php

use Tests\TestCase;

/*
 * Every test runs inside the Laravel application, including unit tests: the rubric labels
 * and Kenyan formats read the app locale and timezone.
 */
pest()->extend(TestCase::class)->in('Feature', 'Unit');
