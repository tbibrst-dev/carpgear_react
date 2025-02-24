<?php

namespace FPrintingVendor\WPDesk\License\ActivationForm;

use FPrintingVendor\WPDesk_Plugin_Info;
class Renderer
{
    /**
     * @var WPDesk_Plugin_Info
     */
    private $plugin_info;
    /**
     * @var bool
     */
    private $update_possible;
    /**
     * @param WPDesk_Plugin_Info $plugin_info .
     * @param bool               $update_possible .
     */
    public function __construct(\FPrintingVendor\WPDesk_Plugin_Info $plugin_info, bool $update_possible)
    {
        $this->plugin_info = $plugin_info;
        $this->update_possible = $update_possible;
    }
    /**
     * @return string
     */
    public function render()
    {
        \ob_start();
        $this->output_render();
        return \ob_get_clean();
    }
    public function output_render()
    {
        $form_content = new \FPrintingVendor\WPDesk\License\ActivationForm\FormContentRenderer($this->plugin_info, $this->update_possible);
        $plugin_slug = $this->plugin_info->get_plugin_slug();
        $plugin_file = $this->plugin_info->get_plugin_file_name();
        include __DIR__ . '/views/activation-form.php';
    }
}
