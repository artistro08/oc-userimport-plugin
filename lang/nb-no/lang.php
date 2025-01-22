<?php

return [
    'plugin' => [
        'name' => 'Importere brukere',
        'description' => 'Importere brukere fra fil med kolonnemapping',
    ],
    'label' => [
        'import' => 'Import',
        'tab' => 'Brukerimport',
        'permission' => 'Brukerimport'
    ],
    'options' => [
        'send_welcome_email' => 'Send velkomst-e-post',
        'auto_activate' => 'Automatisk aktivering',
        'user_groups' => 'Brukergrupper',
        'username_format' => 'Brukernavn format',
        'username_format_firstname_lastname' => 'Fornavn.Etternavn',
        'username_format_firstinitial_lastname' => 'Initial.Etternavn'
    ]
];
