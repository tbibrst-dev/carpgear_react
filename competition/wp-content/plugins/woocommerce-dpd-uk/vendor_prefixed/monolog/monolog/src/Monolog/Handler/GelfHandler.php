<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DpdUKVendor\Monolog\Handler;

use DpdUKVendor\Gelf\PublisherInterface;
use DpdUKVendor\Monolog\Logger;
use DpdUKVendor\Monolog\Formatter\GelfMessageFormatter;
use DpdUKVendor\Monolog\Formatter\FormatterInterface;
/**
 * Handler to send messages to a Graylog2 (http://www.graylog2.org) server
 *
 * @author Matt Lehner <mlehner@gmail.com>
 * @author Benjamin Zikarsky <benjamin@zikarsky.de>
 */
class GelfHandler extends \DpdUKVendor\Monolog\Handler\AbstractProcessingHandler
{
    /**
     * @var PublisherInterface the publisher object that sends the message to the server
     */
    protected $publisher;
    /**
     * @param PublisherInterface $publisher a gelf publisher object
     */
    public function __construct(\DpdUKVendor\Gelf\PublisherInterface $publisher, $level = \DpdUKVendor\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        parent::__construct($level, $bubble);
        $this->publisher = $publisher;
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->publisher->publish($record['formatted']);
    }
    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter() : \DpdUKVendor\Monolog\Formatter\FormatterInterface
    {
        return new \DpdUKVendor\Monolog\Formatter\GelfMessageFormatter();
    }
}
