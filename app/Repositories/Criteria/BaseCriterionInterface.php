<?php
namespace  App\Repositories\Criteria;

interface  BaseCriterionInterface
{
    public function apply($model);
}