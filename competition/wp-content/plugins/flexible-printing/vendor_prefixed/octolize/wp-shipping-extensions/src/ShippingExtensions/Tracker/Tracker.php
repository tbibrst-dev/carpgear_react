<?php

namespace FPrintingVendor\Octolize\ShippingExtensions\Tracker;

use FPrintingVendor\Octolize\ShippingExtensions\Tracker\DataProvider\ShippingExtensionsDataProvider;
use FPrintingVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FPrintingVendor\WPDesk_Tracker;
/**
 * .
 */
class Tracker implements \FPrintingVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var ViewPageTracker
     */
    private $view_page_tracker;
    /**
     * @param ViewPageTracker $view_page_tracker
     */
    public function __construct(\FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker $view_page_tracker)
    {
        $this->view_page_tracker = $view_page_tracker;
    }
    /**
     * Hooks.
     */
    public function hooks() : void
    {
        \add_action('wpdesk_tracker_started', [$this, 'register_tracker_provider']);
    }
    public function register_tracker_provider($tracker) : void
    {
        if ($tracker instanceof \FPrintingVendor\WPDesk_Tracker) {
            $tracker->add_data_provider(new \FPrintingVendor\Octolize\ShippingExtensions\Tracker\DataProvider\ShippingExtensionsDataProvider($this->view_page_tracker));
        }
    }
}
