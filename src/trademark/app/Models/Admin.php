<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $guard_name = [ 'web', 'admin' ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'admin_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'email',
        'role',
    ];

    public $selectable = [
        '*'
    ];

    /**
     * Role admin const
     */
    const ROLE_ADMIN_JIMU = 1;
    const ROLE_ADMIN_TANTO = 2;
    const ROLE_ADMIN_SEKI = 3;
    const ROLE_ADMIN_KANRI = 4;

    const IS_NOT_CONFIRM = 0;
    const IS_CONFIRM = 1;

    /**
     * Relationship QuestionAnswers
     *
     * @return void
     */
    public function questionAnswers()
    {
        return $this->belongsTo(QuestionAnswer::class, 'id', 'admin_id');
    }

    /**
     * Get admin id of seki
     *
     * @return mixed
     */
    public static function getAdminIdOfSeki()
    {
        $admin = Admin::where('role', self::ROLE_ADMIN_SEKI)->first();
        if ($admin) {
            return $admin->id;
        }
        return null;
    }

    /**
     * Get admin id of tanto
     *
     * @return mixed
     */
    public static function getAdminIdOfTanto()
    {
        $admin = Admin::where('role', self::ROLE_ADMIN_TANTO)->first();
        if ($admin) {
            return $admin->id;
        }
        return null;
    }

    /**
     * Get id by role of admin
     *
     * @param integer $role
     * @return mixed
     */
    public static function getAdminIdByRole(int $role)
    {
        $admin = Admin::where('role', $role)->first();
        if ($admin) {
            return $admin->id;
        }
        return null;
    }
}
