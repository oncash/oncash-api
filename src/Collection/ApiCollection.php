<?php

namespace OnCash\Collection;


use OnCash\Core\AbstractPrefixable;
use OnCash\Core\ApiClient;
use OnCash\Exception\MethodNotSupportedException;

class ApiCollection extends AbstractPrefixable implements \ArrayAccess, \JsonSerializable, \IteratorAggregate, \Countable
{
    public static $primary_key = 'id';

    private $_container = [];
    private $_keys = [];
    private $parent = null;
    private $api_client = null;
    private $model = null;
    private $params = [];
    private $current_page = 0;
    private $meta_loaded = false;
    private $meta = null;

    /**
     * ApiCollection constructor.
     * @param ApiClient $api_client
     * @param AbstractPrefixable $parent
     * @param $model
     * @param array $params
     */
    public function __construct(ApiClient $api_client, AbstractPrefixable $parent, $model, array $params = [])
    {
        $this->model = $model;
        $this->api_client = $api_client;
        $this->parent = $parent;
        $this->parentPrefix($parent);
        if (is_array($params)) {
            $this->params = $params;
        }
        $this->meta = (object)[
            'total' => 0,
            'per_page' => isset($params['per_page']) && !empty($params['per_page']) ? $params['per_page'] : 15,
            'last_page' => 0,
        ];
    }

    public function loadPage($page = null)
    {

        if (is_null($page) || empty($page)) {
            $this->current_page = $page = $this->getNextPage();
        }

        if (isset($this->_container[$page])) {
            return $this->_container[$page];
        }

        $this->params['page'] = $page;
        $response = $this->api_client->get($this->buildUrl(), $this->params);

        foreach ($response as $key => $value) {
            if (isset($this->meta->$key)) {
                $this->meta->$key = $value;
            }
        }

        $this->meta_loaded = true;
        $model = $this->model;

        $this->_container[$page] = [];

        foreach ($response['data'] as $item) {
            $this->_keys[$item[$model::$primary_key]] = $page;
            $this->_container[$page][$item[$model::$primary_key]] = new $model($this->api_client, $this->parent, $item);
        }

        return $this->_container[$page];
    }

    public function isEnd()
    {
        if ($this->meta_loaded) {
            return $this->current_page == $this->meta->last_page;
        }

        return false;
    }

    public function getNextPage()
    {
        if ($this->isEnd()) {
            throw new \RuntimeException("End of collection reached");
        }
        return $this->current_page + 1;
    }

    public function loadComplete()
    {
        $page_count = $this->getMeta()->last_page;

        if (count($this->_container) == $page_count) {
            return;
        }

        for ($i = 1; $i <= $this->getMeta()->last_page; $i++) {
            $this->loadPage($i);
        }
    }

    public function getMeta()
    {
        if (!$this->meta_loaded) {
            $this->loadPage(1);
        }
        return $this->meta;
    }

    public function toArray()
    {
        $this->loadComplete();
        $result = [];

        foreach ($this->_container as $page) {
            $result += $page; //array_merge with preserve keys
        }

        return $result;
    }

    public function offsetExists($key)
    {
        return isset($this->_keys[$key]);
    }

    public function offsetGet($key)
    {
        return $this->offsetExists($key) ? $this->_container[$this->_keys[$key]][$key] : null;
    }

    public function offsetSet($key, $value)
    {
        throw new MethodNotSupportedException();
    }

    public function offsetUnset($key)
    {
        unset($this->_container[$this->_keys[$key]][$key]);
    }

    public function count()
    {
        return $this->getMeta()->total;
    }

    public function getIterator()
    {
        $this->loadComplete();
        $iterator = (function () {
            foreach ($this->_container as $page) {
                while (list($key, $val) = each($page)) {
                    yield $key => $val;
                }
            }
        });
        return $iterator();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function all()
    {
        return $this->toArray();
    }
}