<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'target_id',
        'admin_id',
        'page',
        'action',
        'type',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    const TYPE_ANSWERS_ADMIN = 8;
    const PAGE_ANSWERS_ADMIN = 'a000qa02';
    const ACTION_ANSWERS_ADMIN = 1;

    // Role Seki
    const PAGE_ANSWERS_ADMIN_ROLE_SEKI = 'a000qa02s';
    const PAGE_ANSWERS_AMS_ADMIN_ROLE_SEKI = 'a000qa_from_ams_s';
    const ACTION_ANSWERS_ADMIN_ROLE_SEKI = 4;

    // Const Create History From Admin
    const PAGE_CREATE_QUESTION = 'a000qa_from_ams';

    //Const action
    const ACTION_CREATE = 1;
    const ACTION_EDIT = 2;
    const ACTION_DELETE = 3;

    //Const type
    const TYPE_SUPPORT_FIRST_TIME = 1;
    const TYPE_TRADEMARK_REGISTER = 4;
}
