<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

class MultipleInputTextField extends \DpdUKVendor\WPDesk\Forms\Field\InputTextField
{
    public function get_template_name() : string
    {
        return 'input-text-multiple';
    }
}
