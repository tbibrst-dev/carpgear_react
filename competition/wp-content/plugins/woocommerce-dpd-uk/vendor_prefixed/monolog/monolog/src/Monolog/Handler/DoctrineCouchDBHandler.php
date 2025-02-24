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

use DpdUKVendor\Monolog\Logger;
use DpdUKVendor\Monolog\Formatter\NormalizerFormatter;
use DpdUKVendor\Monolog\Formatter\FormatterInterface;
use DpdUKVendor\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends \DpdUKVendor\Monolog\Handler\AbstractProcessingHandler
{
    /** @var CouchDBClient */
    private $client;
    public function __construct(\DpdUKVendor\Doctrine\CouchDB\CouchDBClient $client, $level = \DpdUKVendor\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->client->postDocument($record['formatted']);
    }
    protected function getDefaultFormatter() : \DpdUKVendor\Monolog\Formatter\FormatterInterface
    {
        return new \DpdUKVendor\Monolog\Formatter\NormalizerFormatter();
    }
}
