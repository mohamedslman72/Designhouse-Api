<?php
namespace  App\Repositories\Criteria;

interface  BaseCriteriaInterface
{
    public function withCriteria(...$criteria);
}