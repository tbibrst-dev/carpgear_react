<?php

namespace DpdUKVendor\WPDesk\Persistence;

use DpdUKVendor\Psr\Container\NotFoundExceptionInterface;
trait FallbackFromGetTrait
{
    public function get_fallback(string $id, $fallback = null)
    {
        try {
            return $this->get($id);
        } catch (\DpdUKVendor\Psr\Container\NotFoundExceptionInterface $e) {
            return $fallback;
        }
    }
}
