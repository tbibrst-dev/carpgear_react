<?php

namespace DpdUKVendor\Octolize\Brand\Assets;

use DpdUKVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use DpdUKVendor\WPDesk\ShowDecision\ShouldShowStrategy;
/**
 * Loads assets.
 *
 */
class AdminAssets implements \DpdUKVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * Scripts version.
     *
     * @var string
     */
    private $scripts_version = '1';
    /**
     * Assets URL.
     *
     * @var string
     */
    private $assets_url = '';
    /**
     * Assets URL.
     *
     * @var string
     */
    private $assets_suffix = '';
    /**
     * @var ShouldShowStrategy
     */
    private $should_show;
    /**
     * Assets constructor.
     *
     * @param string $assets_url .
     * @param string $assets_suffix .
     * @param ShouldShowStrategy|null $should_show .
     */
    public function __construct(string $assets_url, string $assets_suffix, $should_show = null)
    {
        $this->assets_url = $assets_url;
        $this->assets_suffix = $assets_suffix;
        $this->should_show = $should_show;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('admin_enqueue_scripts', [$this, 'enqueue_style']);
    }
    /**
     * Enqueue admin scripts.
     */
    public function enqueue_style()
    {
        if ($this->should_show === null || $this->should_show instanceof \DpdUKVendor\WPDesk\ShowDecision\ShouldShowStrategy && $this->should_show->shouldDisplay()) {
            \wp_enqueue_style($this->get_handle(), \trailingslashit($this->assets_url) . 'dist/css/admin.css', [], $this->scripts_version);
        }
    }
    private function get_handle()
    {
        return 'octolize_brand_' . $this->assets_suffix;
    }
    public function get_scripts_version()
    {
        return $this->scripts_version;
    }
}
