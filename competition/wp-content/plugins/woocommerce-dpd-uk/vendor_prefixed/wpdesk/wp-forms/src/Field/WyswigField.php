<?php

namespace DpdUKVendor\WPDesk\Forms\Field;

/**
 * @deprecated
 *
 * Use WPEditorField
 */
class WyswigField extends \DpdUKVendor\WPDesk\Forms\Field\BasicField
{
    public function get_template_name() : string
    {
        return 'wyswig';
    }
    public function should_override_form_template() : bool
    {
        return \true;
    }
}
