<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

class Paragraph extends \DpdUKVendor\WPDesk\Forms\Field\NoValueField
{
    public function get_template_name() : string
    {
        return 'paragraph';
    }
    public function should_override_form_template() : bool
    {
        return \true;
    }
}
