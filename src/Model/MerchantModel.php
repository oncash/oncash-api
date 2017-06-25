<?php

namespace OnCash\Model;


use OnCash\Traits\RelationshipTrait;

class MerchantModel extends AbstractModel
{
    use RelationshipTrait;

    protected static $fields = ['id', 'balance', 'name', 'url', 'logo', 'result_url', 'result_method', 'success_url', 'success_method', 'fail_url', 'fail_method', 'currency_code', 'auto_payout', 'to_payout'];

    public function orders()
    {
        return $this->bind('OnCash\\Service\\OrderService', func_get_args());
    }

    public function logs()
    {
        return $this->bind('OnCash\\Service\\LogService', func_get_args());
    }
}