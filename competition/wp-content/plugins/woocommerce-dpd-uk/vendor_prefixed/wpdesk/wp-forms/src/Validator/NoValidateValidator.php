<?php

namespace DpdUKVendor\WPDesk\Forms\Validator;

use DpdUKVendor\WPDesk\Forms\Validator;
class NoValidateValidator implements \DpdUKVendor\WPDesk\Forms\Validator
{
    public function is_valid($value) : bool
    {
        return \true;
    }
    public function get_messages() : array
    {
        return [];
    }
}
