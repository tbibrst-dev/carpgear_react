<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

use DpdUKVendor\WPDesk\Forms\Sanitizer;
use DpdUKVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class InputTextField extends \DpdUKVendor\WPDesk\Forms\Field\BasicField
{
    public function get_sanitizer() : \DpdUKVendor\WPDesk\Forms\Sanitizer
    {
        return new \DpdUKVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer();
    }
    public function get_template_name() : string
    {
        return 'input-text';
    }
}
