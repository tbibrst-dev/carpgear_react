<?php

namespace DpdUKVendor\WPDesk\Logger;

use DpdUKVendor\Psr\Log\LogLevel;
final class Settings
{
    /** @var string */
    public $level = \DpdUKVendor\Psr\Log\LogLevel::DEBUG;
    /** @var bool */
    public $use_wc_log = \true;
    /** @var bool */
    public $use_wp_log = \true;
    /**
     * @param string $level
     * @param bool   $use_wc_log
     * @param bool   $use_wp_log
     */
    public function __construct(string $level = \DpdUKVendor\Psr\Log\LogLevel::DEBUG, bool $use_wc_log = \true, bool $use_wp_log = \true)
    {
        $this->level = $level;
        $this->use_wc_log = $use_wc_log;
        $this->use_wp_log = $use_wp_log;
    }
}
