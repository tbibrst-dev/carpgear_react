<?php

namespace FPrintingVendor\WPDesk\License;

use FPrintingVendor\WPDesk_Plugin_Info;
/**
 * Replaces WPDesk_Helper_Plugin. Gets info from plugin and sends it to subscription/update integrations
 *
 * @depreacted Check LicenseServer namespace
 * @package    WPDesk\License
 */
class PluginRegistrator implements \FPrintingVendor\WPDesk\License\PluginRegistratorInterface
{
    /** @var PluginRegistratorInterface */
    private $true_registrator;
    public function __construct(\FPrintingVendor\WPDesk_Plugin_Info $plugin_info)
    {
        if (\FPrintingVendor\WPDesk\License\LicenseServer\PluginRegistrator::should_use_license_server()) {
            $this->true_registrator = new \FPrintingVendor\WPDesk\License\LicenseServer\PluginRegistrator($plugin_info);
        } else {
            $this->true_registrator = new \FPrintingVendor\WPDesk\License\OldLicenseRegistrator($plugin_info);
        }
    }
    public function is_active() : bool
    {
        return $this->true_registrator->is_active();
    }
    /**
     * Initializes license manager.
     */
    public function initialize_license_manager()
    {
        $this->true_registrator->initialize_license_manager();
    }
}
