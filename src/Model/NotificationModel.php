<?php

namespace OnCash\Model;


use OnCash\Traits\RelationshipTrait;

class NotificationModel extends AbstractModel
{
    use RelationshipTrait;

    protected static $fields = ['id', 'order_id', 'attempt', 'success', 'result_code', 'created_at'];
}