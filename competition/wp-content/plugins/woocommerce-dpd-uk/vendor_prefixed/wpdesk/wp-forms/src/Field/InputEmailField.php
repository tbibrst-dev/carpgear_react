<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

use DpdUKVendor\WPDesk\Forms\Sanitizer;
use DpdUKVendor\WPDesk\Forms\Sanitizer\EmailSanitizer;
class InputEmailField extends \DpdUKVendor\WPDesk\Forms\Field\BasicField
{
    public function get_type() : string
    {
        return 'email';
    }
    public function get_sanitizer() : \DpdUKVendor\WPDesk\Forms\Sanitizer
    {
        return new \DpdUKVendor\WPDesk\Forms\Sanitizer\EmailSanitizer();
    }
    public function get_template_name() : string
    {
        return 'input-text';
    }
}
