<?php

namespace App\Repositories;

use App\Models\MPrefecture;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class MPrefectureRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MPrefecture $mPrefecture
     * @return  void
     */
    public function __construct(MPrefecture $mPrefecture)
    {
        $this->model = $mPrefecture;
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
            case 'm_nation_id':
                return $query->search($column, $data);
            case 'mNationId':
                return $query->where('m_nation_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * List prefection options
     *
     * @return Collection
     */
    public function listPrefectureOptions(): Collection
    {
        return $this->model->where('m_nation_id', NATION_JAPAN_ID)->pluck('name', 'id');
    }
}
