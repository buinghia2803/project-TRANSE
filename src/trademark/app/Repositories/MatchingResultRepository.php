<?php

namespace App\Repositories;

use App\Models\MatchingResult;
use Illuminate\Database\Eloquent\Builder;

class MatchingResultRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MatchingResult $matchingResult
     * @return  void
     */
    public function __construct(MatchingResult $matchingResult)
    {
        $this->model = $matchingResult;
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
            case 'pi_document_code':
            case 'pi_file_reference_id':
            case 'pi_ip_date':
            case 'pi_dispatch_number':
                return $query->where($column, $data);
            case 'trademark_id':
                return $query->where('trademark_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            case 'start_created_at':
                return $query->where('created_at', '>=', $data);
            case 'end_created_at':
                return $query->where('created_at', '<=', $data);
            default:
                return $query;
        }
    }
}
