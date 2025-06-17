<?php

return [
    /*
    |--------------------------------------------------------------------------
    | School Information
    |--------------------------------------------------------------------------
    */

    'name_lao' => env('SCHOOL_NAME_LAO', 'ໂຮງຮຽນເອກະຊົນໄຊຟອນ'),
    'name_en' => env('SCHOOL_NAME_EN', 'Sayfone Private School'),

    'address' => env('SCHOOL_ADDRESS', 'ບ້ານ..., ເມືອງ..., ແຂວງວຽງຈັນ'),
    'phone' => env('SCHOOL_PHONE', '020 XXXX XXXX'),
    'email' => env('SCHOOL_EMAIL', 'info@sayfone.edu.la'),
    'website' => env('SCHOOL_WEBSITE', 'www.sayfone.edu.la'),

    'logo' => env('SCHOOL_LOGO', '/images/logo.png'),
    'logo_width' => env('SCHOOL_LOGO_WIDTH', '80px'),
    'logo_height' => env('SCHOOL_LOGO_HEIGHT', '80px'),

    /*
    |--------------------------------------------------------------------------
    | Receipt Settings
    |--------------------------------------------------------------------------
    */

    'receipt' => [
        'prefix' => env('RECEIPT_PREFIX', 'PAY'),
        'footer_text' => env('RECEIPT_FOOTER', 'ລະບົບຈັດການໂຮງຮຽນໄຊຟອນ v1.0'),
        'show_images' => env('RECEIPT_SHOW_IMAGES', false),
        'auto_print' => env('RECEIPT_AUTO_PRINT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Print Settings
    |--------------------------------------------------------------------------
    */

    'print' => [
        'paper_size' => env('PRINT_PAPER_SIZE', 'a4'),
        'orientation' => env('PRINT_ORIENTATION', 'portrait'),
        'dpi' => env('PRINT_DPI', 150),
        'font_family' => env('PRINT_FONT_FAMILY', 'NotoSansLao'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Settings
    |--------------------------------------------------------------------------
    */

    'pdf' => [
        'enabled' => env('PDF_ENABLED', true),
        'quality' => env('PDF_QUALITY', 'high'),
        'compression' => env('PDF_COMPRESSION', true),
    ],
];