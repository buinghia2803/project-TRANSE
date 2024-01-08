<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyFolder extends BaseModel
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'target_id',
        'folder_number',
        'keyword',
        'type_trademark',
        'name_trademark',
        'image_trademark',
        'type',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'user_id',
        'target_id',
        'keyword',
        'folder_number',
        'type',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * Const
     */
    // Type of support first time
    const TYPE_SFT = 1;
    // Type of precheck
    const TYPE_PRECHECK = 2;
    // Type of search-ai
    const TYPE_OTHER = 3;

    // Value Input Save Data My Folder
    const SAVE_DATA_MY_FOLDER = 'マイリストを保存';
    const UPDATE_DATA_MY_FOLDER = 'マイリストを上書き保存';

    /**
     * Const type_trademark
     */
    const TRADEMARK_TYPE_LETTER = 1;
    const TRADEMARK_TYPE_OTHER = 2;

    /**
     * My Folder Product
     *
     * @return HasMany
     */
    public function myFolderProduct(): HasMany
    {
        return $this->hasMany(MyFolderProduct::class);
    }

    /**
     * Get trademark from my folder
     */
    public function relationTrademark(): ?HasOneThrough
    {
        if ($this->type == self::TYPE_SFT) {
            return $this->hasOneThrough(SupportFirstTime::class, Trademark::class, 'id', 'trademark_id', 'target_id', 'id');
        } elseif ($this->type == self::TYPE_PRECHECK) {
            return $this->hasOneThrough(Precheck::class, Trademark::class, 'id', 'trademark_id', 'target_id', 'id');
        }
    }
}
