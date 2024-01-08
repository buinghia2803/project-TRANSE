<?php

namespace App\Repositories;

use App\Models\PanDetailDoc;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PanDetailDocRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PanDetailDoc $panDetailDoc
     * @return  void
     */
    public function __construct(PanDetailDoc $panDetailDoc)
    {
        $this->model = $panDetailDoc;
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
