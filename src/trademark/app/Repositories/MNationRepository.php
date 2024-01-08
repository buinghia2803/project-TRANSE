<?php

namespace App\Repositories;

use App\Models\MNation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class MNationRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MNation $mNation
     * @return  void
     */
    public function __construct(MNation $mNation)
    {
        $this->model = $mNation;
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
            case 'name':
                return $query->search($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            case 'id':
                return $query->orderBy($column, $data);
            default:
                return $query;
        }
    }

    /**
     * List nation options
     *
     * @return Collection
     */
    public function listNationOptions()
    {
        return $this->model->pluck('name', 'id');
    }
}
