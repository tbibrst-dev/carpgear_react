<?php

namespace DpdUKVendor\Octolize\Brand\UpsellingBox;

use DpdUKVendor\WPDesk\ShowDecision\ShouldShowStrategy;
class ConstantShouldShowStrategy implements \DpdUKVendor\WPDesk\ShowDecision\ShouldShowStrategy
{
    /**
     * @var string
     */
    private $constant;
    public function __construct(string $constant)
    {
        $this->constant = $constant;
    }
    public function shouldDisplay() : bool
    {
        return !\defined($this->constant);
    }
}
