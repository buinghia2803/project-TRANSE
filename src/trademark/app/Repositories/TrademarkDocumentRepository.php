<?php

namespace App\Repositories;

use App\Models\TrademarkDocument;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class TrademarkDocumentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   TrademarkDocument $trademarkDocument
     * @return  void
     */
    public function __construct(TrademarkDocument $trademarkDocument)
    {
        $this->model = $trademarkDocument;
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
            case 'notice_detail_btn_id':
            case 'trademark_id':
            case 'type':
                return $query->where($column, $data);
            default:
                return $query;
        }
    }
}
