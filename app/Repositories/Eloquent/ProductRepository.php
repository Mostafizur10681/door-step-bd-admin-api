<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->latest('id')->get($columns);
    }

    public function paginate(int $perPage = 15, array $relations = [], array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with($relations)->latest('id')->paginate($perPage, $columns);
    }
}
