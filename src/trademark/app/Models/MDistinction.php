<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MDistinction extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get MDistinction By Name
     *
     * @param mixed $name
     * @return MDistinction
     */
    public static function getMDistinctionByName($name)
    {
        return MDistinction::where('name', $name)->first();
    }

    /**
     * Format name m distinction
     *
     * @param mixed $name
     * @return string|null
     */
    public static function formatNameMDistinction($name)
    {
        if ($name) {
            return __('labels.support_first_times.No').$name .__('labels.support_first_times.kind');
        }
        return null;
    }
}
