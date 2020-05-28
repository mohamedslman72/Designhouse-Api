<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 4/17/2020
 * Time: 8:50 PM
 */

namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\BaseCriterionInterface;

class EagerLoad implements BaseCriterionInterface
{
    protected $relationships;
    public function __construct($relationships)
    {
        $this->relationships = $relationships;
    }

    public  function apply($model)
    {
        return $model->with($this->relationships);
    }
}