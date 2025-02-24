<?php

namespace FPrintingVendor\WPDesk\License\Page\License\Action;

use FPrintingVendor\WPDesk\License\Page\Action;
/**
 * Do nothing.
 *
 * @package WPDesk\License\Page\License\Action
 */
class Nothing implements \FPrintingVendor\WPDesk\License\Page\Action
{
    public function execute(array $plugin)
    {
        // NOOP
    }
}
