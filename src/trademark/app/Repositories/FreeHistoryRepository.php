<?php

namespace App\Repositories;

use App\Models\FreeHistory;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class FreeHistoryRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   FreeHistory $freeHistory
     * @return  void
     */
    public function __construct(FreeHistory $freeHistory)
    {
        $this->model = $freeHistory;
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
            case 'maching_result_id':
            case 'flag_role':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get type option
     *
     * @return array
     */
    public function types(): array
    {
        return [
            FreeHistory::TYPE_1 => __('labels.free_history.type_1'),
            FreeHistory::TYPE_2 => __('labels.free_history.type_2'),
            FreeHistory::TYPE_3 => __('labels.free_history.type_3'),
            FreeHistory::TYPE_4 => __('labels.free_history.type_4'),
        ];
    }

    /**
     * Get properties option
     *
     * @return array
     */
    public function properties(): array
    {
        return [
            FreeHistory::PROPERTY_1 => __('labels.free_history.property_1'),
            FreeHistory::PROPERTY_2 => __('labels.free_history.property_2'),
            FreeHistory::PROPERTY_3 => __('labels.free_history.property_3'),
            FreeHistory::PROPERTY_4 => __('labels.free_history.property_4'),
            FreeHistory::PROPERTY_5 => __('labels.free_history.property_5'),
        ];
    }
}
