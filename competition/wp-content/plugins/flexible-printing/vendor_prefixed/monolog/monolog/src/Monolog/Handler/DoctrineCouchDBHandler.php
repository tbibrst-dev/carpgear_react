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
use FPrintingVendor\Monolog\Formatter\NormalizerFormatter;
use FPrintingVendor\Monolog\Formatter\FormatterInterface;
use FPrintingVendor\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends \FPrintingVendor\Monolog\Handler\AbstractProcessingHandler
{
    /** @var CouchDBClient */
    private $client;
    public function __construct(\FPrintingVendor\Doctrine\CouchDB\CouchDBClient $client, $level = \FPrintingVendor\Monolog\Logger::DEBUG, bool $bubble = \true)
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
    protected function getDefaultFormatter() : \FPrintingVendor\Monolog\Formatter\FormatterInterface
    {
        return new \FPrintingVendor\Monolog\Formatter\NormalizerFormatter();
    }
}
