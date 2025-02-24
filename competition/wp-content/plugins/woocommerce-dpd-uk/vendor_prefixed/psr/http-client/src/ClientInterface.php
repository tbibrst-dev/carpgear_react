<?php

namespace DpdUKVendor\Psr\Http\Client;

use DpdUKVendor\Psr\Http\Message\RequestInterface;
use DpdUKVendor\Psr\Http\Message\ResponseInterface;
interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(\DpdUKVendor\Psr\Http\Message\RequestInterface $request) : \DpdUKVendor\Psr\Http\Message\ResponseInterface;
}
