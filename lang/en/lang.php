<?php

return [
    'plugin' => [
        'name' => 'User import',
        'description' => 'Import users from CSV with column mapping',
    ],
    'label' => [
        'import' => 'Import',
        'tab' => 'User import',
        'permission' => 'User import'
    ],
    'options' => [
        'send_welcome_email' => 'Send welcome email',
        'auto_activate' => 'Auto activate',
        'user_groups' => 'User groups',
        'username_format' => 'Username format',
        'username_format_firstname_lastname' => 'Firstname.Lastname',
        'username_format_firstinitial_lastname' => 'Firstinitial.Lastname'
    ]
];
