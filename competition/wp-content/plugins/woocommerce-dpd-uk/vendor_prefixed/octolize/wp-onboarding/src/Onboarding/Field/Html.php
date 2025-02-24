<?php

namespace DpdUKVendor\Octolize\Onboarding\Field;

use DpdUKVendor\WPDesk\Forms\Field\BasicField;
/**
 * Html field.
 */
class Html extends \DpdUKVendor\WPDesk\Forms\Field\BasicField
{
    protected $meta = ['priority' => self::DEFAULT_PRIORITY, 'default_value' => '', 'label' => '', 'description' => '', 'description_tip' => '', 'data' => [], 'type' => 'html'];
    public function get_template_name() : string
    {
        return 'html';
    }
}
