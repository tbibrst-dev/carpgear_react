<?php

namespace DpdUKVendor\WPDesk\Forms\Sanitizer;

use DpdUKVendor\WPDesk\Forms\Sanitizer;
class NoSanitize implements \DpdUKVendor\WPDesk\Forms\Sanitizer
{
    public function sanitize($value)
    {
        return $value;
    }
}
