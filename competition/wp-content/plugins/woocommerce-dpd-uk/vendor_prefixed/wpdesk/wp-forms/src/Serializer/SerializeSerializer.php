<?php

namespace DpdUKVendor\WPDesk\Forms\Serializer;

use DpdUKVendor\WPDesk\Forms\Serializer;
class SerializeSerializer implements \DpdUKVendor\WPDesk\Forms\Serializer
{
    public function serialize($value) : string
    {
        return \serialize($value);
    }
    public function unserialize(string $value)
    {
        return \unserialize($value);
    }
}
