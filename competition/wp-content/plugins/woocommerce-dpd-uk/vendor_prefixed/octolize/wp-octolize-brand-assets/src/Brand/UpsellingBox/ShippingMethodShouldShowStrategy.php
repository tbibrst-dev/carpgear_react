<?php

namespace DpdUKVendor\Octolize\Brand\UpsellingBox;

use DpdUKVendor\WPDesk\ShowDecision\GetStrategy;
class ShippingMethodShouldShowStrategy extends \DpdUKVendor\WPDesk\ShowDecision\GetStrategy
{
    /**
     * @var string
     */
    private $constant;
    public function __construct(string $method_id)
    {
        parent::__construct([['page' => 'wc-settings', 'tab' => 'shipping', 'section' => $method_id]]);
    }
}
