<?php

// Only the settings ElimuCentral changes; everything else uses Livewire's defaults.

return [

    // resources/views/components holds the UI kit's Blade components (<x-button> …), so
    // Livewire's own view-based components live in resources/views/livewire only.
    'component_locations' => [
        resource_path('views/livewire'),
    ],

    // Full-page Livewire components render inside the kit's app shell.
    'component_layout' => 'components.layouts.app',

];
