<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

class ButtonField extends \DpdUKVendor\WPDesk\Forms\Field\NoValueField
{
    public function get_template_name() : string
    {
        return 'button';
    }
    public function get_type() : string
    {
        return 'button';
    }
}
