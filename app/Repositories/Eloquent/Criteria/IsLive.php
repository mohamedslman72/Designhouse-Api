<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 4/17/2020
 * Time: 8:50 PM
 */

namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\BaseCriterionInterface;

class IsLive implements BaseCriterionInterface
{
public  function apply($model)
{
  return $model->where('is_live',true);
}
}