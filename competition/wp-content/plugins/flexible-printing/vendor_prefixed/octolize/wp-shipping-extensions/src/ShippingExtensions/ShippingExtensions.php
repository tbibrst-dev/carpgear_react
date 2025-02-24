<?php

namespace FPrintingVendor\Octolize\ShippingExtensions;

use FPrintingVendor\Octolize\ShippingExtensions\Tracker\Tracker;
use FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker;
use FPrintingVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FPrintingVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use FPrintingVendor\WPDesk_Plugin_Info;
/**
 * .
 */
class ShippingExtensions implements \FPrintingVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    use HookableParent;
    private const VERSION = 2;
    private const OCTOLIZE_WP_SHIPPING_EXTENSIONS_INITIATED_FILTER = 'octolize/shipping-extensions/initiated';
    /**
     * @var WPDesk_Plugin_Info .
     */
    private $plugin_info;
    /**
     * @param WPDesk_Plugin_Info $plugin_info .
     */
    public function __construct(\FPrintingVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $this->plugin_info = $plugin_info;
    }
    /**
     * @return void
     */
    public function hooks() : void
    {
        $this->add_hookable(new \FPrintingVendor\Octolize\ShippingExtensions\PluginLinks($this->plugin_info));
        if (\apply_filters(self::OCTOLIZE_WP_SHIPPING_EXTENSIONS_INITIATED_FILTER, \false) === \false) {
            \add_filter(self::OCTOLIZE_WP_SHIPPING_EXTENSIONS_INITIATED_FILTER, '__return_true');
            $tracker = new \FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker();
            $this->add_hookable(new \FPrintingVendor\Octolize\ShippingExtensions\Page($this->get_assets_url(), $tracker));
            $this->add_hookable(new \FPrintingVendor\Octolize\ShippingExtensions\Assets($this->get_assets_url(), self::VERSION));
            $this->add_hookable(new \FPrintingVendor\Octolize\ShippingExtensions\Tracker\Tracker($tracker));
            $this->add_hookable(new \FPrintingVendor\Octolize\ShippingExtensions\PageViewTracker($tracker));
        }
        $this->hooks_on_hookable_objects();
    }
    /**
     * @return string
     */
    private function get_assets_url() : string
    {
        return \plugin_dir_url(__DIR__ . '/../../../') . 'assets/';
    }
}
