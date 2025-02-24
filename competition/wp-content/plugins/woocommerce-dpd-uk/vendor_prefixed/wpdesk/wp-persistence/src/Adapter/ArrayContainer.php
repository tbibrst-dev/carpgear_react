<?php

namespace DpdUKVendor\WPDesk\Persistence\Adapter;

use DpdUKVendor\WPDesk\Persistence\ElementNotExistsException;
use DpdUKVendor\WPDesk\Persistence\FallbackFromGetTrait;
use DpdUKVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Container that uses array as a persistent memory.
 *
 * @package WPDesk\Persistence
 */
class ArrayContainer implements \DpdUKVendor\WPDesk\Persistence\PersistentContainer
{
    use FallbackFromGetTrait;
    /** @var array */
    protected $array;
    public function __construct(array $initial = [])
    {
        $this->array = $initial;
    }
    public function set(string $id, $value)
    {
        if ($value === null) {
            $this->delete($id);
        } else {
            $this->array[$id] = $value;
        }
    }
    public function delete(string $id)
    {
        unset($this->array[$id]);
    }
    public function has($id) : bool
    {
        return \key_exists($id, $this->array);
    }
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new \DpdUKVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $this->array[$id];
    }
    /**
     * Return array that is used internally to save the data.
     *
     * @return array
     */
    public function get_array()
    {
        return $this->array;
    }
}
