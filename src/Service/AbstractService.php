<?php

namespace OnCash\Service;

use OnCash\Collection\ApiCollection;
use OnCash\Core\AbstractPrefixable;
use OnCash\Model\AbstractModel;
use OnCash\Core\ApiClient;
use OnCash\Exception\MethodNotSupportedException;

abstract class AbstractService extends AbstractPrefixable
{
    const MAX_PAGE_SIZE = 100;
    protected $api_client = null;
    protected $filter = [];
    protected $sort = [];
    protected $require = [];
    protected $per_page = 15;
    protected $model = 'OnCash\\Model\\AbstractModel';

    public function __construct(ApiClient $api_client, AbstractPrefixable $parent = null)
    {
        $this->api_client = $api_client;
        $this->parentPrefix($parent);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->index()->all();
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->per_page;
    }

    /**
     * @param int $per_page
     * @return $this
     */
    public function setPerPage($per_page)
    {
        $per_page = intval($per_page);
        if ($per_page > static::MAX_PAGE_SIZE || $per_page <= 0) {
            throw new \InvalidArgumentException(sprintf('"$per_page" must be in the range 1..%d', static::MAX_PAGE_SIZE));
        }
        $this->per_page = $per_page;
        return $this;
    }


    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param array $filter
     * @return $this
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param array $sort
     * @return $this
     */
    public function setSort(array $sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequire()
    {
        return $this->require;
    }

    /**
     * @param array $require
     * @return $this
     */
    public function setRequire(array $require)
    {
        $this->require = $require;
        return $this;
    }

    /**
     * @throws MethodNotSupportedException
     */
    public function index()
    {
        return $this->getCollection($this->buildParams());
    }

    /**
     * @throws MethodNotSupportedException
     */
    public function create()
    {
        throw new MethodNotSupportedException();
    }

    /**
     * @param AbstractModel $model
     * @throws MethodNotSupportedException
     */
    public function store(AbstractModel $model)
    {
        throw new MethodNotSupportedException();
    }

    /**
     * @param $primary
     * @return AbstractModel
     */
    public function get($primary)
    {
        return $this->findModelByPrimary($primary);
    }

    /**
     * @param AbstractModel $model
     * @throws MethodNotSupportedException
     */
    public function update(AbstractModel $model)
    {
        throw new MethodNotSupportedException();
    }

    /**
     * @param $primary
     * @throws MethodNotSupportedException
     */
    public function delete($primary)
    {
        throw new MethodNotSupportedException();
    }



    /**
     * @return array
     */
    protected function buildParams()
    {
        $params = [
            "per_page" => $this->per_page
        ];

        if(!empty($this->filter)) {
            $params["filter"] = $this->filter;
        }

        if(!empty($this->sort)) {
            $params["sort"] = $this->sort;
        }

        if(!empty($this->require)) {
            $params["require"] = $this->require;
        }

        return $params;
    }

    protected function getCollection($params)
    {
        return new ApiCollection($this->api_client, $this, $this->model, $params);
    }

    public function getModelByPrimary($primary)
    {
        $model = $this->model;
        return $model::get($this->api_client, $this, $primary);
    }

    public function findModelByPrimary($primary)
    {
        $model = $this->model;
        return $model::find($this->api_client, $this, $primary);
    }
}