<?php

namespace DpdUKVendor\WPDesk\Forms\Validator;

use DpdUKVendor\WPDesk\Forms\Validator;
class RequiredValidator implements \DpdUKVendor\WPDesk\Forms\Validator
{
    public function is_valid($value) : bool
    {
        return $value !== null;
    }
    public function get_messages() : array
    {
        return [];
    }
}
