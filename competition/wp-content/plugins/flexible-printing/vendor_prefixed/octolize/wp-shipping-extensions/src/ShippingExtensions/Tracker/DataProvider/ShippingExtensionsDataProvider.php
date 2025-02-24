<?php

/**
 * Class ShippingExtensionsDataProvider
 */
namespace FPrintingVendor\Octolize\ShippingExtensions\Tracker\DataProvider;

use FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker;
/**
 * Provider data for page.
 */
class ShippingExtensionsDataProvider implements \WPDesk_Tracker_Data_Provider
{
    private const PROVIDER_KEY = 'shipping_extensions';
    /**
     * @var ViewPageTracker
     */
    private $tracker;
    /**
     * @param ViewPageTracker $tracker
     */
    public function __construct(\FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker $tracker)
    {
        $this->tracker = $tracker;
    }
    /**
     * @return array
     */
    public function get_data() : array
    {
        return [self::PROVIDER_KEY => ['views' => [\FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker::OPTION_DIRECT => $this->tracker->get_views(\FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker::OPTION_DIRECT), \FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker::OPTION_PLUGINS_LIST => $this->tracker->get_views(\FPrintingVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker::OPTION_PLUGINS_LIST)]]];
    }
}
