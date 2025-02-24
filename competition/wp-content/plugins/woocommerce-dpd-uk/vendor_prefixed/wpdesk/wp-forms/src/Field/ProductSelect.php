<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

use DpdUKVendor\WPDesk\Forms\Serializer\ProductSelectSerializer;
use DpdUKVendor\WPDesk\Forms\Serializer;
class ProductSelect extends \DpdUKVendor\WPDesk\Forms\Field\SelectField
{
    public function __construct()
    {
        $this->set_multiple();
    }
    public function has_serializer() : bool
    {
        return \true;
    }
    public function get_serializer() : \DpdUKVendor\WPDesk\Forms\Serializer
    {
        return new \DpdUKVendor\WPDesk\Forms\Serializer\ProductSelectSerializer();
    }
    public function get_template_name() : string
    {
        return 'product-select';
    }
}
