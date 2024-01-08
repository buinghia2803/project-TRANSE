<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyFolderProduct extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'my_folder_id',
        'm_product_id',
        'type',
        'is_additional_search',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'my_folder_id',
        'type',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * Const
     */
    const TYPE_EXIST = 1;
    const TYPE_ADDITIONAL = 2;

    /**
     * Relation of My Floder product with m_product.
     */
    public function mProduct(): belongsTo
    {
        return $this->belongsTo(MProduct::class);
    }

    /**
     * Get Name product
     *
     * @return string
     */
    public function getNameProd(): ?string
    {
        return $this->mProduct ? $this->mProduct->name : '';
    }
}
