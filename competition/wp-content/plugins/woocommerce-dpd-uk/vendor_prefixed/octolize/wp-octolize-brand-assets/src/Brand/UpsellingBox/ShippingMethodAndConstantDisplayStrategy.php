<?php

namespace DpdUKVendor\Octolize\Brand\UpsellingBox;

use DpdUKVendor\WPDesk\ShowDecision\ShouldShowStrategy;
class ShippingMethodAndConstantDisplayStrategy implements \DpdUKVendor\WPDesk\ShowDecision\ShouldShowStrategy
{
    /**
     * @var string
     */
    private $method_id;
    /**
     * @var string
     */
    private $constant;
    public function __construct(string $method_id, string $constant)
    {
        $this->constant = $constant;
        $this->method_id = $method_id;
    }
    public function shouldDisplay() : bool
    {
        return (new \DpdUKVendor\Octolize\Brand\UpsellingBox\ConstantShouldShowStrategy($this->constant))->shouldDisplay() && (new \DpdUKVendor\Octolize\Brand\UpsellingBox\ShippingMethodShouldShowStrategy($this->method_id))->shouldDisplay();
    }
}
