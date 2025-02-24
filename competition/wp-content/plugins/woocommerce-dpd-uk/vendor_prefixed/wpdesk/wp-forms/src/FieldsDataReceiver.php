<?php

namespace DpdUKVendor\WPDesk\Forms;

use DpdUKVendor\Psr\Container\ContainerInterface;
/**
 * Some field owners can receive and process field data.
 * Probably should be used with FieldProvider interface.
 *
 * @package WPDesk\Forms
 */
interface FieldsDataReceiver
{
    /**
     * Set values corresponding to fields.
     *
     * @return void
     */
    public function update_fields_data(\DpdUKVendor\Psr\Container\ContainerInterface $data);
}
