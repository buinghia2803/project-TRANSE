<?php

namespace App\Repositories;

use App\Models\MyFolderProduct;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class MyFolderProductRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MyFolderProduct $myFolderProduct
     * @return  void
     */
    public function __construct(MyFolderProduct $myFolderProduct)
    {
        $this->model = $myFolderProduct;
    }


    /**
     * @param   Builder $query
     * @param   string  $column
     * @param   mixed   $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'id':
            case 'my_folder_id':
            case 'type':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
