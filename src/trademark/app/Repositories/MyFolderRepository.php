<?php

namespace App\Repositories;

use App\Models\MyFolder;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class MyFolderRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MyFolder $myFolder
     * @return  void
     */
    public function __construct(MyFolder $myFolder)
    {
        $this->model = $myFolder;
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
            case 'user_id':
            case 'target_id':
            case 'folder_number':
            case 'type':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
