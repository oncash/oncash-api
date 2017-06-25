<?php

namespace OnCash\Model;


use OnCash\Traits\RelationshipTrait;

class TariffModel extends AbstractModel
{
    use RelationshipTrait;

    protected static $fields = ['id', 'name', 'about'];

    public function commissions()
    {
        return $this->bind('OnCash\\Service\\CommissionService', func_get_args());
    }
}