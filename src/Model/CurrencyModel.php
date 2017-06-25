<?php

namespace OnCash\Model;


use OnCash\Traits\RelationshipTrait;

class CurrencyModel extends AbstractModel
{
    use RelationshipTrait;

    public static $primary_key = 'code';
    protected static $fields = ['code', 'name', 'rate'];
}