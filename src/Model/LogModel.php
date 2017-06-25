<?php

namespace OnCash\Model;


use OnCash\Traits\RelationshipTrait;

class LogModel extends AbstractModel
{
    use RelationshipTrait;

    protected static $fields = ['id', 'type', 'order_id', 'user_amount', 'user_commission', 'merchant_amount', 'merchant_commission', 'created_at', 'updated_at'];
}