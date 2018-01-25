<?php

return [

    'debug'       => env('APP_DEBUG_PDF', true),
    'binpath'     => 'lib/',
//    'binpath'     => app_path().'/vendor/nitmedia/wkhtml2pdf/src/Nitmedia/Wkhtml2pdf/lib/',
    'binfile'     => env('WKHTML2PDF_BIN_FILE', 'wkhtmltopdf-amd64'),    
//    'tmppath' => app_path().'/resources/html/',
    'output_mode' => 'I',
];
