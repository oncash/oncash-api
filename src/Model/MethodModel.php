<?php

namespace OnCash\Model;


use OnCash\Traits\RelationshipTrait;

class MethodModel extends AbstractModel
{
    use RelationshipTrait;

    protected static $fields = ['id', 'name', 'logo', 'service_name', 'currency_code', 'email_required', 'phone_required'];
}