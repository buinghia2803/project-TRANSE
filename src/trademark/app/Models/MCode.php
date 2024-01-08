<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class MCode extends Model
{
    use SoftDeletes;

    protected $table = 'm_code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'admin_id',
        'branch_number',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'admin_id',
        'name',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * Const type: 1: オリジナルクリーン | 2: 登録クリーン | 3: 創作クリーン, 4: =準クリーン
     */
    const TYPE_ORIGINAL_CLEAN = 1;
    const TYPE_REGISTERED_CLEAN = 2;
    const TYPE_CREATIVE_CLEAN = 3;
    const TYPE_SEMI_CLEAN = 4;

    /**
     * Get mcode by name
     *
     * @param string $name
     * @return mixed
     */
    public static function getMCodeByName(string $name)
    {
        return MCode::where('name', $name)->first();
    }
}
