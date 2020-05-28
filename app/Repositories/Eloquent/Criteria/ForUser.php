<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 4/17/2020
 * Time: 8:50 PM
 */

namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\BaseCriterionInterface;

class ForUser implements BaseCriterionInterface
{
    protected $user_id;
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public  function apply($model)
{
  return $model->where('user_id',$this->user_id);
}
}