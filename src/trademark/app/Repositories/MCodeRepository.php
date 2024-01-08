<?php

namespace App\Repositories;

use App\Models\MCode;
use Illuminate\Database\Eloquent\Builder;

class MCodeRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MCode $mCode
     * @return  void
     */
    public function __construct(MCode $mCode)
    {
        $this->model = $mCode;
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
            case 'm_distinction_id':
            case 'products_number':
            case 'admin_id':
            case 'type':
            case 'rank':
            case 'name':
                return $query->where($column, $data);
            case 'keyword':
                return $query->where('name', 'like', '%' . $data . '%')
                            ->whereIn('type', [ORIGINAL_CLEAN, REGISTER_CLEAN]);
            default:
                return $query;
        }
    }
}
