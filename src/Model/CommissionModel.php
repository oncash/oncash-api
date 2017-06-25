<?php

namespace OnCash\Model;


use OnCash\Traits\RelationshipTrait;

class CommissionModel extends AbstractModel
{
    use RelationshipTrait;

    protected static $fields = ['id', 'method_id', 'user_rate', 'merchant_rate'];
}