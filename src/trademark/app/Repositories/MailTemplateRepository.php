<?php

namespace App\Repositories;

use App\Models\MailTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MailTemplateRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MailTemplate $mailTemplate
     * @return  void
     */
    public function __construct(MailTemplate $mailTemplate)
    {
        $this->model = $mailTemplate;
    }

    /**
     * Get status
     *
     * @return  array
     */
    public function types(): array
    {
        return $this->model->types;
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
            case 'type':
            case 'from_page':
            case 'lang':
            case 'guard_type':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
