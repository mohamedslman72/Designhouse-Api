<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 4/15/2020
 * Time: 11:35 PM
 */

namespace App\Repositories\Contracts;


interface BaseInterface
{
    public function all();
    public function find($id);
    public function findWhere($column,$value);
    public function findWhereFirst($column,$value);
    public function paginate($perPage=10);
    public function create(array  $data);
    public function update($id,array  $data);
    public function delete($id);
}