<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

class WPEditorField extends \DpdUKVendor\WPDesk\Forms\Field\BasicField
{
    public function get_template_name() : string
    {
        return 'wp-editor';
    }
}
