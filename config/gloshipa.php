<?php

return [

    'name' => 'Glo Shipa',

    /*
    | Where "new registration waiting for approval" emails are sent.
    | Set ADMIN_EMAIL in .env.
    */
    'admin_email' => env('ADMIN_EMAIL', 'admin@gloshipa.com'),

    /*
    | Password given to the "admin" account by `php artisan db:seed`.
    | Set ADMIN_PASSWORD in .env BEFORE seeding, then change it after first login.
    */
    'seed_admin_password' => env('ADMIN_PASSWORD', 'ChangeMe123!'),

    /*
    | Shown in the footer and in emails. Replace with the real company details.
    */
    'contact' => [
        'email' => env('COMPANY_EMAIL', 'support@gloshipa.com'),
        'phone' => env('COMPANY_PHONE', '+27 00 000 0000'),
        'address' => env('COMPANY_ADDRESS', 'Johannesburg, South Africa'),
    ],

];
