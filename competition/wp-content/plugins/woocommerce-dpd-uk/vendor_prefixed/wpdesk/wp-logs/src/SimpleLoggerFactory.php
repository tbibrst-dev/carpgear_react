<?php

declare (strict_types=1);
namespace DpdUKVendor\WPDesk\Logger;

use DpdUKVendor\Monolog\Handler\HandlerInterface;
use DpdUKVendor\Monolog\Handler\NullHandler;
use DpdUKVendor\Monolog\Logger;
use DpdUKVendor\Monolog\Handler\ErrorLogHandler;
use DpdUKVendor\WPDesk\Logger\WC\WooCommerceHandler;
final class SimpleLoggerFactory implements \DpdUKVendor\WPDesk\Logger\LoggerFactory
{
    /** @var Settings */
    private $options;
    /** @var string */
    private $channel;
    /** @var Logger */
    private $logger;
    public function __construct(string $channel, \DpdUKVendor\WPDesk\Logger\Settings $options = null)
    {
        $this->channel = $channel;
        $this->options = $options ?? new \DpdUKVendor\WPDesk\Logger\Settings();
    }
    public function getLogger($name = null) : \DpdUKVendor\Monolog\Logger
    {
        if ($this->logger) {
            return $this->logger;
        }
        $this->logger = new \DpdUKVendor\Monolog\Logger($this->channel);
        if ($this->options->use_wc_log) {
            if (\function_exists('wc_get_logger')) {
                $this->create_wc_handler();
            } else {
                \add_action('woocommerce_init', [$this, 'create_wc_handler']);
            }
        }
        // Adding WooCommerce logger may have failed, if so add WP by default.
        if ($this->options->use_wp_log || empty($this->logger->getHandlers())) {
            $this->logger->pushHandler($this->get_wp_handler());
        }
        return $this->logger;
    }
    /**
     * @internal
     */
    public function create_wc_handler()
    {
        while (!$this->options->use_wp_log && !empty($this->logger->getHandlers())) {
            $this->logger->popHandler();
        }
        $this->logger->pushHandler(new \DpdUKVendor\WPDesk\Logger\WC\WooCommerceHandler(\wc_get_logger(), $this->channel));
    }
    private function get_wp_handler() : \DpdUKVendor\Monolog\Handler\HandlerInterface
    {
        if (\defined('DpdUKVendor\\WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            return new \DpdUKVendor\Monolog\Handler\ErrorLogHandler(\DpdUKVendor\Monolog\Handler\ErrorLogHandler::OPERATING_SYSTEM, $this->options->level);
        }
        return new \DpdUKVendor\Monolog\Handler\NullHandler();
    }
}
