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
namespace FPrintingVendor\Monolog\Handler;

use FPrintingVendor\Monolog\Logger;
use FPrintingVendor\Monolog\Formatter\FormatterInterface;
use FPrintingVendor\Monolog\Formatter\JsonFormatter;
use FPrintingVendor\PhpAmqpLib\Message\AMQPMessage;
use FPrintingVendor\PhpAmqpLib\Channel\AMQPChannel;
use AMQPExchange;
/**
 * @phpstan-import-type Record from \Monolog\Logger
 */
class AmqpHandler extends \FPrintingVendor\Monolog\Handler\AbstractProcessingHandler
{
    /**
     * @var AMQPExchange|AMQPChannel $exchange
     */
    protected $exchange;
    /** @var array<string, mixed> */
    private $extraAttributes = [];
    /**
     * @return array<string, mixed>
     */
    public function getExtraAttributes() : array
    {
        return $this->extraAttributes;
    }
    /**
     * Configure extra attributes to pass to the AMQPExchange (if you are using the amqp extension)
     *
     * @param array<string, mixed> $extraAttributes  One of content_type, content_encoding,
     *                                               message_id, user_id, app_id, delivery_mode,
     *                                               priority, timestamp, expiration, type
     *                                               or reply_to, headers.
     * @return AmqpHandler
     */
    public function setExtraAttributes(array $extraAttributes) : self
    {
        $this->extraAttributes = $extraAttributes;
        return $this;
    }
    /**
     * @var string
     */
    protected $exchangeName;
    /**
     * @param AMQPExchange|AMQPChannel $exchange     AMQPExchange (php AMQP ext) or PHP AMQP lib channel, ready for use
     * @param string|null              $exchangeName Optional exchange name, for AMQPChannel (PhpAmqpLib) only
     */
    public function __construct($exchange, ?string $exchangeName = null, $level = \FPrintingVendor\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        if ($exchange instanceof \FPrintingVendor\PhpAmqpLib\Channel\AMQPChannel) {
            $this->exchangeName = (string) $exchangeName;
        } elseif (!$exchange instanceof \AMQPExchange) {
            throw new \InvalidArgumentException('PhpAmqpLib\\Channel\\AMQPChannel or AMQPExchange instance required');
        } elseif ($exchangeName) {
            @\trigger_error('The $exchangeName parameter can only be passed when using PhpAmqpLib, if using an AMQPExchange instance configure it beforehand', \E_USER_DEPRECATED);
        }
        $this->exchange = $exchange;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $data = $record["formatted"];
        $routingKey = $this->getRoutingKey($record);
        if ($this->exchange instanceof \AMQPExchange) {
            $attributes = ['delivery_mode' => 2, 'content_type' => 'application/json'];
            if ($this->extraAttributes) {
                $attributes = \array_merge($attributes, $this->extraAttributes);
            }
            $this->exchange->publish($data, $routingKey, 0, $attributes);
        } else {
            $this->exchange->basic_publish($this->createAmqpMessage($data), $this->exchangeName, $routingKey);
        }
    }
    /**
     * {@inheritDoc}
     */
    public function handleBatch(array $records) : void
    {
        if ($this->exchange instanceof \AMQPExchange) {
            parent::handleBatch($records);
            return;
        }
        foreach ($records as $record) {
            if (!$this->isHandling($record)) {
                continue;
            }
            /** @var Record $record */
            $record = $this->processRecord($record);
            $data = $this->getFormatter()->format($record);
            $this->exchange->batch_basic_publish($this->createAmqpMessage($data), $this->exchangeName, $this->getRoutingKey($record));
        }
        $this->exchange->publish_batch();
    }
    /**
     * Gets the routing key for the AMQP exchange
     *
     * @phpstan-param Record $record
     */
    protected function getRoutingKey(array $record) : string
    {
        $routingKey = \sprintf('%s.%s', $record['level_name'], $record['channel']);
        return \strtolower($routingKey);
    }
    private function createAmqpMessage(string $data) : \FPrintingVendor\PhpAmqpLib\Message\AMQPMessage
    {
        $attributes = ['delivery_mode' => 2, 'content_type' => 'application/json'];
        if ($this->extraAttributes) {
            $attributes = \array_merge($attributes, $this->extraAttributes);
        }
        return new \FPrintingVendor\PhpAmqpLib\Message\AMQPMessage($data, $attributes);
    }
    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter() : \FPrintingVendor\Monolog\Formatter\FormatterInterface
    {
        return new \FPrintingVendor\Monolog\Formatter\JsonFormatter(\FPrintingVendor\Monolog\Formatter\JsonFormatter::BATCH_MODE_JSON, \false);
    }
}
