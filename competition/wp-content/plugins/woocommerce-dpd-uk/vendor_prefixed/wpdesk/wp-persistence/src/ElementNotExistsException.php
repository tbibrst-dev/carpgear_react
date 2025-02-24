<?php

namespace DpdUKVendor\WPDesk\Persistence;

use DpdUKVendor\Psr\Container\NotFoundExceptionInterface;
/**
 * @package WPDesk\Persistence
 */
class ElementNotExistsException extends \RuntimeException implements \DpdUKVendor\Psr\Container\NotFoundExceptionInterface
{
}
