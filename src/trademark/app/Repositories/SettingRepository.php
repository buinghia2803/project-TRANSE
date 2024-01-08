<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SettingRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Setting $setting
     * @return  void
     */
    public function __construct(Setting $setting)
    {
        $this->model = $setting;
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
            case 'key':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
