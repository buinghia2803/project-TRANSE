<?php

namespace App\Helpers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Closure;

class QueryBuilderMixin
{
    /**
     * Search with Query Builder. Alternative to search Like.
     *
     * @return Closure
     */
    public function search(): Closure
    {
        return function ($attribute, $searchTerm) {
            $this->where(function (Builder $query) use ($attribute, $searchTerm) {
                $sql = "LOWER({$query->getGrammar()->wrap($attribute)}) LIKE ?  ESCAPE ?";

                $searchTerm = mb_strtolower($searchTerm, 'UTF8');
                $searchTerm = preg_replace_callback(
                    "/\\\{1,}/",
                    function ($matches) {
                        if (app()->environment('local')) {
                            return $this->getBackslashPrefixByPdo().str_replace('\\', '\\\\', $matches[0]);
                        }
                        return str_replace('\\', '\\\\', $matches[0]);
                    },
                    $searchTerm
                );

                $searchTerm = addcslashes($searchTerm, '%_');
                $query->whereRaw($sql, ["%{$searchTerm}%", '\\']);
            });

            return $this;
        };
    }

    /**
     * Multi search
     *
     * @return Closure
     */
    public function orSearch(): Closure
    {
        return function ($attribute, $searchTerm) {
            $this->orWhere(function (Builder $q) use ($attribute, $searchTerm) {
                $q->search($attribute, $searchTerm);
            });

            return $this;
        };
    }

    /**
     * Get Backslash Prefix By Pdo
     *
     * @return Closure
     */
    public static function getBackslashPrefixByPdo(): Closure
    {
        return function () {
            $pdoDriver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

            if ($pdoDriver === 'sqlite') {
                return '';
            }

            return '\\';
        };
    }
}
