<?php

return [

    'debug'       => env('APP_DEBUG_PDF', true),
//    'binpath'     => 'lib/',
    'binpath'     => "'C:\wkhtmltopdf\bin\'",
//    'binfile'     => env('WKHTML2PDF_BIN_FILE', 'wkhtmltopdf-amd64'),  
    'binfile'     => "wkhtmltopdf.exe",
//    'tmppath' => app_path().'/resources/html/',
    'output_mode' => 'I',
];
