<?php

namespace DpdUKVendor\WPDesk\Forms\Sanitizer;

use DpdUKVendor\WPDesk\Forms\Sanitizer;
class EmailSanitizer implements \DpdUKVendor\WPDesk\Forms\Sanitizer
{
    public function sanitize($value) : string
    {
        return \sanitize_email($value);
    }
}
