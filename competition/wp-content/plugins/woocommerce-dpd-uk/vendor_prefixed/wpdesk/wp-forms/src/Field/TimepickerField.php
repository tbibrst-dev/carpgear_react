<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

use DpdUKVendor\WPDesk\Forms\Serializer;
use DpdUKVendor\WPDesk\Forms\Serializer\JsonSerializer;
class TimepickerField extends \DpdUKVendor\WPDesk\Forms\Field\BasicField
{
    public function get_type() : string
    {
        return 'time';
    }
    public function has_serializer() : bool
    {
        return \true;
    }
    public function get_serializer() : \DpdUKVendor\WPDesk\Forms\Serializer
    {
        return new \DpdUKVendor\WPDesk\Forms\Serializer\JsonSerializer();
    }
    public function get_template_name() : string
    {
        return 'timepicker';
    }
}
