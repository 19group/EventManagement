<?php

return [
/*
    'debug'       => env('APP_DEBUG_PDF', false),
    'binpath'     => 'lib/',
    'binfile'     => env('WKHTML2PDF_BIN_FILE', 'wkhtmltopdf-amd64'),
    'output_mode' => 'I',
    */
  'consumer_key' => env('PESAPAL_CONSUMER_KEY'),
  'consumer_secret' => env('PESAPAL_CONSUMER_SECRET'),
  'currency' => env('PESAPAL_CURRENCY_CODE'),
  'live' => env('PESAPAL_LIVE'),
  'callback_route' => env('PESAPAL_CALLBACK_ROUTE'),
  //'callback_route' => env('')
];
