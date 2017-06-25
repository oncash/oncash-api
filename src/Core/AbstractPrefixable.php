<?php

namespace OnCash\Core;


abstract class AbstractPrefixable
{
    protected $prefix = [];

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function buildUrl(array $prefix = [])
    {
        return '/' . ltrim(implode('/', array_merge($this->prefix, $prefix)), '/');
    }

    protected function parentPrefix(AbstractPrefixable $parent = null)
    {
        if(!is_null($parent)) {
            $this->prefix = array_merge($parent->getPrefix(), $this->prefix);
        }
    }
}