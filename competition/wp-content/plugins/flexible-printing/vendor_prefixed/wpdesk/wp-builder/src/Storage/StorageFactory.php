<?php

namespace FPrintingVendor\WPDesk\PluginBuilder\Storage;

class StorageFactory
{
    /**
     * @return PluginStorage
     */
    public function create_storage()
    {
        return new \FPrintingVendor\WPDesk\PluginBuilder\Storage\WordpressFilterStorage();
    }
}
