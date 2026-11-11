<?php

/*
|--------------------------------------------------------------------------
| Documentation
|--------------------------------------------------------------------------
|
| The sidebar of each package, in order. A key is a Markdown file below
| resources/docs/{package}, without its extension, and the first page of a
| package is always "overview".
|
*/

return [

    'packages' => [

        'introduction' => [
            'label' => 'Introduction',
            'tagline' => 'What Xelqun is, and how to get started.',
            'icon' => 'book-open',
            'popular' => ['overview', 'installation', 'getting-started'],
            'navigation' => [
                'Introduction' => [
                    'overview' => 'What is Xelqun?',
                    'installation' => 'Installation',
                    'getting-started' => 'Getting started',
                    'architecture' => 'How it works',
                    'local-development' => 'Local development',
                    'version-support' => 'Version support',
                ],
            ],
        ],

        'forms' => [
            'label' => 'Forms',
            'tagline' => 'Define forms in PHP and render them as Vue components.',
            'icon' => 'file-text',
            'popular' => ['installation', 'fields/select', 'fields/repeater', 'layouts/wizard'],
            'navigation' => [
                'Getting started' => [
                    'overview' => 'Overview',
                    'installation' => 'Installation',
                    'your-first-form' => 'Your first form',
                    'production' => 'Before going to production',
                ],
                'Fields' => [
                    'fields/overview' => 'Overview',
                    'fields/text-input' => 'Text input',
                    'fields/textarea' => 'Textarea',
                    'fields/select' => 'Select',
                    'fields/checkbox' => 'Checkbox',
                    'fields/checkbox-list' => 'Checkbox list',
                    'fields/radio' => 'Radio',
                    'fields/toggle' => 'Toggle',
                    'fields/toggle-buttons' => 'Toggle buttons',
                    'fields/date-time' => 'Date and time pickers',
                    'fields/file-upload' => 'File upload',
                    'fields/rich-editor' => 'Rich editor',
                    'fields/markdown-editor' => 'Markdown editor',
                    'fields/code-editor' => 'Code editor',
                    'fields/color-picker' => 'Color picker',
                    'fields/slider' => 'Slider',
                    'fields/tags-input' => 'Tags input',
                    'fields/key-value' => 'Key-value',
                    'fields/repeater' => 'Repeater',
                    'fields/builder' => 'Builder',
                    'fields/morph-to-select' => 'Morph-to select',
                    'fields/hidden' => 'Hidden',
                    'fields/custom-fields' => 'Custom fields',
                ],
                'Layouts' => [
                    'layouts/overview' => 'Overview',
                    'layouts/grid' => 'Grid, flex and groups',
                    'layouts/sections' => 'Sections and fieldsets',
                    'layouts/tabs' => 'Tabs',
                    'layouts/wizard' => 'Wizard',
                    'layouts/callouts' => 'Callouts and empty states',
                    'layouts/primes' => 'Text, icons and images',
                ],
                'Behaviour' => [
                    'validation' => 'Validation',
                    'conditional-logic' => 'Conditional logic',
                    'live-fields' => 'Live and dependent fields',
                    'eloquent' => 'Eloquent and relationships',
                    'uploads-security' => 'Uploads and security',
                ],
                'Actions' => [
                    'actions/overview' => 'Overview',
                    'actions/modals' => 'Modals',
                    'actions/grouping' => 'Grouping actions',
                    'actions/standalone' => 'Standalone actions',
                ],
                'Notifications' => [
                    'notifications/overview' => 'Overview',
                    'notifications/queued' => 'Queued notifications',
                ],
                'Extending' => [
                    'plugins' => 'Plugins',
                    'custom-components' => 'Custom components',
                    'translations' => 'Translations',
                    'commands' => 'Commands',
                    'testing' => 'Testing',
                ],
                'Under the hood' => [
                    'architecture' => 'How it works',
                ],
            ],
        ],

        'tables' => [
            'label' => 'Tables',
            'tagline' => 'Sorting, search, filters, actions and inline editing, defined in PHP.',
            'icon' => 'table',
            'popular' => ['installation', 'columns/text', 'filters/overview', 'actions/bulk'],
            'navigation' => [
                'Getting started' => [
                    'overview' => 'Overview',
                    'installation' => 'Installation',
                    'your-first-table' => 'Your first table',
                    'custom-data' => 'Custom data',
                    'production' => 'Before going to production',
                ],
                'Columns' => [
                    'columns/overview' => 'Overview',
                    'columns/text' => 'Text',
                    'columns/icon' => 'Icon',
                    'columns/image' => 'Image',
                    'columns/color' => 'Color',
                    'columns/select' => 'Select',
                    'columns/toggle' => 'Toggle',
                    'columns/text-input' => 'Text input',
                    'columns/checkbox' => 'Checkbox',
                    'columns/custom' => 'Custom columns',
                ],
                'Filters' => [
                    'filters/overview' => 'Overview',
                    'filters/select' => 'Select',
                    'filters/ternary' => 'Ternary',
                    'filters/query-builder' => 'Query builder',
                    'filters/custom' => 'Custom filters',
                    'filters/trashed' => 'Trashed',
                ],
                'Actions' => [
                    'actions/record' => 'Record actions',
                    'actions/header' => 'Header actions',
                    'actions/bulk' => 'Bulk actions',
                ],
                'Display' => [
                    'search-and-sorting' => 'Search and sorting',
                    'pagination' => 'Pagination',
                    'summaries' => 'Summaries',
                    'grouping' => 'Grouping',
                    'reordering' => 'Reordering',
                    'layout' => 'Content grid and stripes',
                    'empty-state' => 'Empty state',
                    'polling' => 'Polling',
                    'column-toggles' => 'Column toggles',
                ],
                'Extending' => [
                    'plugins' => 'Plugins',
                    'commands' => 'Commands',
                    'testing' => 'Testing',
                    'limits' => 'Current limits',
                ],
                'Under the hood' => [
                    'architecture' => 'How it works',
                ],
            ],
        ],

        'panels' => [
            'label' => 'Panels',
            'tagline' => 'Full admin panels with auth, CRUD, dashboards and tenancy.',
            'icon' => 'layout-dashboard',
            'popular' => ['installation', 'resources/overview', 'widgets/overview', 'tenancy'],
            'navigation' => [
                'Getting started' => [
                    'overview' => 'Overview',
                    'installation' => 'Installation',
                    'your-first-panel' => 'Your first panel',
                    'multiple-panels' => 'Multiple panels and domains',
                    'production' => 'Before going to production',
                ],
                'Resources' => [
                    'resources/overview' => 'Overview',
                    'resources/pages' => 'List, create, edit and view',
                    'resources/simple' => 'Simple resources',
                    'resources/singular' => 'Singular resources',
                    'resources/nested' => 'Nested resources',
                    'resources/soft-deletes' => 'Soft deletes',
                    'resources/tabs' => 'List tabs',
                    'resources/global-search' => 'Global search',
                    'resources/widgets' => 'Widgets on resource pages',
                    'resources/custom-pages' => 'Custom pages',
                    'resources/relation-managers' => 'Relation managers',
                ],
                'Infolists' => [
                    'infolists/overview' => 'Overview',
                    'infolists/entries' => 'Entries',
                    'infolists/custom' => 'Custom entries',
                ],
                'Actions' => [
                    'actions/overview' => 'Overview',
                    'actions/crud' => 'Create, edit, view and delete',
                    'actions/replicate' => 'Replicate',
                    'actions/soft-deletes' => 'Restore and force delete',
                    'actions/relations' => 'Attach and associate',
                    'actions/import-export' => 'Import and export',
                ],
                'Dashboards' => [
                    'widgets/overview' => 'Overview',
                    'widgets/stats' => 'Stats',
                    'widgets/charts' => 'Charts',
                    'widgets/lists' => 'Lists and tiles',
                    'widgets/tables' => 'Tables',
                    'widgets/custom' => 'Custom widgets',
                ],
                'Panel' => [
                    'pages' => 'Pages',
                    'navigation' => 'Navigation',
                    'tenancy' => 'Tenancy',
                ],
                'Authentication' => [
                    'auth/login' => 'Login',
                    'auth/registration' => 'Registration',
                    'auth/password-reset' => 'Password reset',
                    'auth/email-verification' => 'Email verification',
                    'auth/profile' => 'Profile',
                    'auth/two-factor' => 'Two-factor',
                ],
                'Notifications' => [
                    'notifications/database' => 'Database',
                    'notifications/broadcast' => 'Broadcast',
                ],
                'Theming' => [
                    'theming/colors' => 'Colors',
                    'theming/fonts' => 'Fonts',
                    'theming/logos' => 'Logos and favicon',
                    'theming/dark-mode' => 'Dark mode',
                    'theming/animations' => 'Animations',
                    'theming/css-hooks' => 'CSS hooks',
                ],
                'Extending' => [
                    'plugins' => 'Plugins',
                    'render-hooks' => 'Render hooks',
                    'translations' => 'Translations',
                    'commands' => 'Commands',
                    'testing' => 'Testing',
                    'parity' => 'Parity with Filament',
                ],
                'Under the hood' => [
                    'architecture' => 'How it works',
                ],
            ],
        ],

    ],

];
