<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

use DpdUKVendor\WPDesk\Forms\Sanitizer;
use DpdUKVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class DatePickerField extends \DpdUKVendor\WPDesk\Forms\Field\BasicField
{
    public function __construct()
    {
        $this->add_class('date-picker');
        $this->set_placeholder('YYYY-MM-DD');
    }
    public function get_sanitizer() : \DpdUKVendor\WPDesk\Forms\Sanitizer
    {
        return new \DpdUKVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer();
    }
    public function get_template_name() : string
    {
        return 'input-date-picker';
    }
}
