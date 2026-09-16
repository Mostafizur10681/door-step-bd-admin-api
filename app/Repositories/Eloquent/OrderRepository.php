<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $relations = [], array $columns = ['*']): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->model->with($relations)->latest()->paginate($perPage, $columns);
    }
}
