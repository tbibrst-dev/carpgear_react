<?php

namespace DpdUKVendor\WPDesk\Persistence\Decorator;

use DpdUKVendor\WPDesk\Persistence\DeferredPersistentContainer;
use DpdUKVendor\WPDesk\Persistence\ElementNotExistsException;
use DpdUKVendor\WPDesk\Persistence\FallbackFromGetTrait;
use DpdUKVendor\WPDesk\Persistence\PersistentContainer;
/**
 * You can use this class to delay write access to any PersistenceContainer.
 *
 * @package WPDesk\Persistence
 */
class DelayPersistentContainer implements \DpdUKVendor\WPDesk\Persistence\DeferredPersistentContainer
{
    use FallbackFromGetTrait;
    /**
     * Container with deferred access.
     *
     * @var PersistentContainer
     */
    protected $container;
    /**
     * Data that has been set but not yet saved to $container.
     *
     * @var array
     */
    protected $internal_data = [];
    /**
     * The keys that was changed in using internal data.
     *
     * @var bool[]
     */
    protected $changed = [];
    public function __construct(\DpdUKVendor\WPDesk\Persistence\PersistentContainer $container)
    {
        $this->container = $container;
    }
    public function get($id)
    {
        if (isset($this->changed[$id]) && $this->changed[$id]) {
            if (isset($this->internal_data[$id])) {
                return $this->internal_data[$id];
            }
            throw new \DpdUKVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $this->container->get($id);
    }
    public function has($id) : bool
    {
        if (isset($this->changed[$id]) && $this->changed[$id]) {
            return isset($this->internal_data[$id]);
        }
        return $this->container->has($id);
    }
    public function save()
    {
        foreach ($this->changed as $key => $value) {
            $this->container->set($key, $this->internal_data[$key]);
        }
        $this->reset();
    }
    public function is_changed() : bool
    {
        return !empty($this->changed);
    }
    public function reset()
    {
        $this->changed = [];
    }
    public function set(string $id, $value)
    {
        $this->changed[$id] = \true;
        $this->internal_data[$id] = $value;
    }
    public function delete(string $id)
    {
        $this->changed[$id] = \true;
        unset($this->internal_data[$id]);
    }
}
