<?php

namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\BaseCriterionInterface;

class WithTrashed implements BaseCriterionInterface
{
    public  function apply($model)
    {
        return $model->withTrashed();
    }
}