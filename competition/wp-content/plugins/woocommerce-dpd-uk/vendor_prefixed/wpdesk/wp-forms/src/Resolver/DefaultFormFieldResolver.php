<?php

namespace DpdUKVendor\WPDesk\Forms\Resolver;

use DpdUKVendor\WPDesk\View\Renderer\Renderer;
use DpdUKVendor\WPDesk\View\Resolver\DirResolver;
use DpdUKVendor\WPDesk\View\Resolver\Resolver;
/**
 * Use with View to resolver form fields to default templates.
 *
 * @package WPDesk\Forms\Resolver
 */
class DefaultFormFieldResolver implements \DpdUKVendor\WPDesk\View\Resolver\Resolver
{
    /** @var Resolver */
    private $dir_resolver;
    public function __construct()
    {
        $this->dir_resolver = new \DpdUKVendor\WPDesk\View\Resolver\DirResolver(__DIR__ . '/../../templates');
    }
    public function resolve($name, \DpdUKVendor\WPDesk\View\Renderer\Renderer $renderer = null) : string
    {
        return $this->dir_resolver->resolve($name, $renderer);
    }
}
