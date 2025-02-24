<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

use DpdUKVendor\WPDesk\Forms\Validator;
use DpdUKVendor\WPDesk\Forms\Validator\NonceValidator;
class NoOnceField extends \DpdUKVendor\WPDesk\Forms\Field\BasicField
{
    public function __construct(string $action_name)
    {
        $this->meta['action'] = $action_name;
    }
    public function get_validator() : \DpdUKVendor\WPDesk\Forms\Validator
    {
        return new \DpdUKVendor\WPDesk\Forms\Validator\NonceValidator($this->get_meta_value('action'));
    }
    public function get_template_name() : string
    {
        return 'noonce';
    }
}
