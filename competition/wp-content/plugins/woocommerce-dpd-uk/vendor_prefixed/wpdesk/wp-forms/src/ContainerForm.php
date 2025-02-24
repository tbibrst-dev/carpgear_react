<?php

namespace DpdUKVendor\WPDesk\Forms;

use DpdUKVendor\Psr\Container\ContainerInterface;
use DpdUKVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Persistent container support for forms.
 *
 * @package WPDesk\Forms
 */
interface ContainerForm
{
    /**
     * @param ContainerInterface $data
     *
     * @return void
     */
    public function set_data(\DpdUKVendor\Psr\Container\ContainerInterface $data);
    /**
     * Put data from form into a container.
     *
     * @return void
     */
    public function put_data(\DpdUKVendor\WPDesk\Persistence\PersistentContainer $container);
}
