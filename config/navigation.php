<?php

// Sidebar (desktop) and bottom nav (phones) per role. Items are filtered with
// Gate::allows($item['can']) when 'can' is set, so permissions stay server-side.
// 'count' names a key in the shared `navCounts` view data (cached, refreshed every 60 s).

return [
    'sidebar' => [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'house'],

        ['group' => 'Academics'],
        ['label' => 'Learners', 'route' => 'learners.index', 'icon' => 'users', 'can' => 'viewAny-learners'],
        ['label' => 'Attendance', 'route' => 'attendance.index', 'icon' => 'clipboard-check'],
        ['label' => 'Assessments', 'route' => 'assessments.index', 'icon' => 'book-open', 'count' => 'pendingMarks'],
        ['label' => 'Timetable', 'route' => 'timetable.index', 'icon' => 'calendar'],
        ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'chart-column', 'can' => 'view-reports'],

        ['group' => 'Finance', 'can' => 'view-fees'],
        ['label' => 'Fees', 'route' => 'fees.index', 'icon' => 'wallet', 'can' => 'view-fees', 'count' => 'unmatchedPayments'],
        ['label' => 'Fee structures', 'route' => 'fees.structures.index', 'icon' => 'sliders-horizontal', 'can' => 'manage-fees'],

        ['group' => 'Communication'],
        ['label' => 'SMS & notices', 'route' => 'messages.index', 'icon' => 'message-square', 'can' => 'send-messages'],

        ['group' => 'School', 'can' => 'manage-school'],
        ['label' => 'Academic setup', 'route' => 'setup.academic', 'icon' => 'school', 'can' => 'manage-school'],
        ['label' => 'Settings', 'route' => 'settings', 'icon' => 'settings', 'can' => 'manage-school'],
    ],

    // Max 5 items. Labels are translation keys (lang/sw.json carries the Kiswahili).
    'bottom' => [
        'parent' => [
            ['label' => 'Home', 'route' => 'parent.home', 'icon' => 'house'],
            ['label' => 'Fees', 'route' => 'parent.fees', 'icon' => 'wallet'],
            ['label' => 'Results', 'route' => 'parent.results', 'icon' => 'chart-column'],
            ['label' => 'Messages', 'route' => 'parent.messages', 'icon' => 'message-square'],
            ['label' => 'More', 'route' => 'parent.more', 'icon' => 'layout-grid'],
        ],
        'teacher' => [
            ['label' => 'Today', 'route' => 'teacher.today', 'icon' => 'house'],
            ['label' => 'Register', 'route' => 'attendance.index', 'icon' => 'clipboard-check'],
            ['label' => 'Marks', 'route' => 'assessments.index', 'icon' => 'book-open'],
            ['label' => 'Timetable', 'route' => 'timetable.index', 'icon' => 'calendar'],
            ['label' => 'More', 'route' => 'teacher.more', 'icon' => 'layout-grid'],
        ],
        'learner' => [
            ['label' => 'Today', 'route' => 'learner.today', 'icon' => 'house'],              // S16 today + timetable
            ['label' => 'Assignments', 'route' => 'learner.assignments', 'icon' => 'notebook-pen'], // S17
            ['label' => 'Results', 'route' => 'learner.results', 'icon' => 'chart-column'],   // S18
            ['label' => 'Notices', 'route' => 'learner.notices', 'icon' => 'message-square'],
            ['label' => 'More', 'route' => 'learner.more', 'icon' => 'layout-grid'],
        ],
    ],
];
