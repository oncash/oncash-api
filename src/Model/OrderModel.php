<?php

namespace OnCash\Model;


use OnCash\Traits\RelationshipTrait;

class OrderModel extends AbstractModel
{
    use RelationshipTrait;

    protected static $fields = ['id', 'merchant_order_id', 'required_amount', 'payed_amount', 'method_id', 'is_paid', 'is_transferred', 'user_email', 'user_phone', 'custom_fields', 'created_at', 'updated_at'];

    public function notifications()
    {
        return $this->bind('OnCash\\Service\\NotificationService', func_get_args());
    }
}