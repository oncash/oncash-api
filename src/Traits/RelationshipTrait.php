<?php

namespace OnCash\Traits;


use OnCash\Core\AbstractPrefixable;
use OnCash\Service\AbstractService;
use OnCash\Model\AbstractModel;

trait RelationshipTrait
{
    private $_relations = [];

    /**
     * @param $key
     * @return AbstractService|void
     */
    public function __get($key)
    {
        if (!method_exists(gettype($this), $key) && method_exists($this, $key)) { //Detect non-static method
            if (isset($this->_relations[$key])) {
                return $this->_relations[$key];
            }

            $relation = $this->$key();

            if (!($relation instanceof AbstractService)) {
                throw new \LogicException(sprintf('Method "%s" must return an object of type "%s"', $key, 'OnCash\\Service\\AbstractService'));
            }

            $this->_relations[$key] = $relation;

            return $this->_relations[$key]->index();
        }

        if (isset($this->_container) && method_exists($this, 'offsetGet')) {
            return $this->offsetGet($key);
        }

        return;
    }

    /**
     * @param $class_name
     * @param array $args func_get_args()
     * @return AbstractService|AbstractModel
     */
    private function bind($class_name, array $args = [])
    {
        $parent = $this instanceof AbstractPrefixable ? $this : null;
        $object = new $class_name($this->getClient(), $parent);


        switch (count($args)) {
            case 0:
                return $object;
            case 1:
                return $object->getModelByPrimary($args[0]);
            default:
                return ($args[1] === true) ?
                    $object->findModelByPrimary($args[0]) :
                    $object->getModelByPrimary($args[0]);
        }
    }
}