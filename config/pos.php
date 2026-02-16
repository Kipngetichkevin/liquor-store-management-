<?php

return [
    /*
    |--------------------------------------------------------------------------
    | POS Tax Rate
    |--------------------------------------------------------------------------
    |
    | This is the default tax rate applied to all POS sales.
    | You can change this value anytime and it will affect new sales.
    |
    */
    'tax_rate' => env('POS_TAX_RATE', 16), // Default 16% (Kenya VAT)
];