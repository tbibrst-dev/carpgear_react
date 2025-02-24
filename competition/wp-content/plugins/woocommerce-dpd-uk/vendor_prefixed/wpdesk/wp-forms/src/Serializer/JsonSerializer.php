<?php

namespace DpdUKVendor\WPDesk\Forms\Serializer;

use DpdUKVendor\WPDesk\Forms\Serializer;
class JsonSerializer implements \DpdUKVendor\WPDesk\Forms\Serializer
{
    public function serialize($value) : string
    {
        return (string) \json_encode($value);
    }
    public function unserialize(string $value)
    {
        return \json_decode($value, \true);
    }
}
