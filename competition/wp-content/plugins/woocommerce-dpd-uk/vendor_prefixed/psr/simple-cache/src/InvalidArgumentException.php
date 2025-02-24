<?php

namespace DpdUKVendor\Psr\SimpleCache;

/**
 * Exception interface for invalid cache arguments.
 *
 * When an invalid argument is passed it must throw an exception which implements
 * this interface
 */
interface InvalidArgumentException extends \DpdUKVendor\Psr\SimpleCache\CacheException
{
}
