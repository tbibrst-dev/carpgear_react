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
namespace DpdUKVendor\Monolog\Test;

use DpdUKVendor\Monolog\Logger;
use DpdUKVendor\Monolog\DateTimeImmutable;
use DpdUKVendor\Monolog\Formatter\FormatterInterface;
/**
 * Lets you easily generate log records and a dummy formatter for testing purposes
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 *
 * @phpstan-import-type Record from \Monolog\Logger
 * @phpstan-import-type Level from \Monolog\Logger
 *
 * @internal feel free to reuse this to test your own handlers, this is marked internal to avoid issues with PHPStorm https://github.com/Seldaek/monolog/issues/1677
 */
class TestCase extends \DpdUKVendor\PHPUnit\Framework\TestCase
{
    public function tearDown() : void
    {
        parent::tearDown();
        if (isset($this->handler)) {
            unset($this->handler);
        }
    }
    /**
     * @param mixed[] $context
     *
     * @return array Record
     *
     * @phpstan-param  Level $level
     * @phpstan-return Record
     */
    protected function getRecord(int $level = \DpdUKVendor\Monolog\Logger::WARNING, string $message = 'test', array $context = []) : array
    {
        return ['message' => (string) $message, 'context' => $context, 'level' => $level, 'level_name' => \DpdUKVendor\Monolog\Logger::getLevelName($level), 'channel' => 'test', 'datetime' => new \DpdUKVendor\Monolog\DateTimeImmutable(\true), 'extra' => []];
    }
    /**
     * @phpstan-return Record[]
     */
    protected function getMultipleRecords() : array
    {
        return [$this->getRecord(\DpdUKVendor\Monolog\Logger::DEBUG, 'debug message 1'), $this->getRecord(\DpdUKVendor\Monolog\Logger::DEBUG, 'debug message 2'), $this->getRecord(\DpdUKVendor\Monolog\Logger::INFO, 'information'), $this->getRecord(\DpdUKVendor\Monolog\Logger::WARNING, 'warning'), $this->getRecord(\DpdUKVendor\Monolog\Logger::ERROR, 'error')];
    }
    protected function getIdentityFormatter() : \DpdUKVendor\Monolog\Formatter\FormatterInterface
    {
        $formatter = $this->createMock(\DpdUKVendor\Monolog\Formatter\FormatterInterface::class);
        $formatter->expects($this->any())->method('format')->will($this->returnCallback(function ($record) {
            return $record['message'];
        }));
        return $formatter;
    }
}
