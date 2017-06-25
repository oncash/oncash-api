<?php

namespace OnCash\Model;

use OnCash\Core\AbstractPrefixable;
use OnCash\Core\ApiClient;

class AbstractModel extends AbstractPrefixable implements \ArrayAccess, \JsonSerializable, \IteratorAggregate, \Countable
{
    public static $primary_key = 'id';

    protected $_container = [];
    private $api_client;
    protected static $fields = ['id'];

    public function __construct(ApiClient $api_client, AbstractPrefixable $parent, array $fields)
    {
        if (!isset($fields[static::$primary_key])) {
            throw new \InvalidArgumentException('$fields must contains primary key');
        }

        $this->prefix = [
            $fields[static::$primary_key]
        ];

        $this->api_client = $api_client;
        $this->_container = $fields;
        $this->parentPrefix($parent);
    }

    /**
     * Get instance of model without loading
     *
     * @param ApiClient $api_client
     * @param AbstractPrefixable $parent
     * @param $primary
     * @return AbstractModel
     */
    public static function get(ApiClient $api_client, AbstractPrefixable $parent, $primary)
    {
        $class = get_called_class();
        return new $class($api_client, $parent, [
            static::$primary_key => $primary
        ]);
    }

    /**
     * Load model by primary key
     *
     * @param ApiClient $api_client
     * @param AbstractPrefixable $parent
     * @param $primary
     * @return AbstractModel
     */
    public static function find(ApiClient $api_client, AbstractPrefixable $parent, $primary)
    {
        $model = static::get($api_client, $parent, $primary);
        return $model->load();
    }

    public static function create(ApiClient $api_client, AbstractPrefixable $parent, array $fields)
    {
        $model = new static($api_client, $parent, $fields);

        return $model->save();
    }

    public function load()
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_container = $this->api_client->get($this->buildUrl());
        return $this;
    }

    public function reload()
    {
        $this->_container = [];
        return $this->load();
    }

    public function update(array $fields = null)
    {
        $this->_container = array_merge($this->_container, $fields);
        return $this->save();
    }

    public function save()
    {
        $this->_container = $this->api_client->put($this->buildUrl(), $this->_container);
        return $this;
    }

    public function toArray()
    {
        if (!$this->isLoaded()) {
            $this->load();
        }
        return $this->_container;
    }

    public function offsetExists($key)
    {
        if (!$this->isLoaded() && !isset($this->_container[$key])) {
            $this->load();
        }
        return isset($this->_container[$key]);
    }

    public function offsetGet($key)
    {
        return $this->offsetExists($key) ? $this->_container[$key] : null;
    }

    public function offsetSet($key, $value)
    {
        $this->_container[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->_container[$key]);
    }

    public function count()
    {
        return count($this->_container);
    }

    public function getIterator()
    {
        $iterator = (function () {
            $container = $this->toArray();
            while (list($key, $val) = each($container)) {
                yield $key => $val;
            }
        });
        return $iterator();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function getClient()
    {
        return $this->api_client;
    }

    public function isLoaded()
    {
        return array_diff(static::getFields(), array_keys($this->_container)) === [];
    }

    /**
     * @return array
     */
    public static function getFields()
    {
        return static::$fields;
    }
}