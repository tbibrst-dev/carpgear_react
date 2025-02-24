<?php

namespace DpdUKVendor\WPDesk\Forms\Sanitizer;

use DpdUKVendor\WPDesk\Forms\Sanitizer;
class CallableSanitizer implements \DpdUKVendor\WPDesk\Forms\Sanitizer
{
    /** @var callable */
    private $callable;
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }
    public function sanitize($value) : string
    {
        return \call_user_func($this->callable, $value);
    }
}
