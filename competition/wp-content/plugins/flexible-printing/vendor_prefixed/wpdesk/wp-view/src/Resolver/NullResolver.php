<?php

namespace FPrintingVendor\WPDesk\View\Resolver;

use FPrintingVendor\WPDesk\View\Renderer\Renderer;
use FPrintingVendor\WPDesk\View\Resolver\Exception\CanNotResolve;
/**
 * This resolver never finds the file
 *
 * @package WPDesk\View\Resolver
 */
class NullResolver implements \FPrintingVendor\WPDesk\View\Resolver\Resolver
{
    public function resolve($name, \FPrintingVendor\WPDesk\View\Renderer\Renderer $renderer = null)
    {
        throw new \FPrintingVendor\WPDesk\View\Resolver\Exception\CanNotResolve("Null Cannot resolve");
    }
}
