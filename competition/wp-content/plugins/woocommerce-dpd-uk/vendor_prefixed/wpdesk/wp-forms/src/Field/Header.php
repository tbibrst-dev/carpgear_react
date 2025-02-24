<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

use DpdUKVendor\WPDesk\Forms\Field;
class Header extends \DpdUKVendor\WPDesk\Forms\Field\NoValueField
{
    public function __construct()
    {
        parent::__construct();
        $this->meta['header_size'] = '';
    }
    public function get_template_name() : string
    {
        return 'header';
    }
    public function should_override_form_template() : bool
    {
        return \true;
    }
    public function set_header_size(int $value) : \DpdUKVendor\WPDesk\Forms\Field
    {
        $this->meta['header_size'] = $value;
        return $this;
    }
}
