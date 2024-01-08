<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $guard_name = ['web', 'api'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_trademark',
        'is_image_trademark',
        'email',
        'user_number',
        'info_type_acc',
        'info_name',
        'info_name_furigana',
        'info_corporation_number',
        'info_nation_id',
        'info_postal_code',
        'info_prefectures_id',
        'info_address_second',
        'info_address_three',
        'info_phone',
        'info_member_id',
        'password',
        'info_gender',
        'info_birthday',
        'info_question',
        'info_answer',
        'contact_type_acc',
        'contact_name',
        'contact_name_furigana',
        'contact_name_department',
        'contact_name_department_furigana',
        'contact_name_manager',
        'contact_name_manager_furigana',
        'contact_nation_id',
        'contact_postal_code',
        'contact_prefectures_id',
        'contact_address_second',
        'contact_address_three',
        'contact_phone',
        'contact_email_second',
        'contact_email_three',
        'status',
        'status_withdraw',
        'reason_withdraw',
        'problems',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'info_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'email',
        'status',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * Const
     */

    const TENTATIVE = 1;
    const ENABLED = 2;

    const NO_IMAGE_TRADEMARK = 0;
    const HAS_IMAGE_TRADEMARK = 1;

    const STATUS_WITHDRAW_INACTIVE = 0;
    const STATUS_WITHDRAW_ACTIVE = 1;

    const INACTIVE = 1;
    const ACTIVE = 2;
    /**
     * Info_type_acc
     */
    const INFO_TYPE_ACC_GROUP = 1;
    const INFO_TYPE_ACC_INDIVIDUAL = 2;

    /**
     * Gender type
     */
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    /**
     * Set status
     *
     * @var array
     */
    public array $statusTypes = [
        'tentative' => self::TENTATIVE,
        'enabled' => self::ENABLED,
    ];

    /**
     * List contact type acc
     */
    public static function listContactTypeAcc(): array
    {
        return [
            CONTACT_TYPE_ACC_GROUP => __('labels.profile_edit.form.group'),
            CONTACT_TYPE_ACC_INDIVIDUAL => __('labels.profile_edit.form.individual'),
        ];
    }

    /**
     * Authentications
     *
     * @return void
     */
    public function authentications()
    {
        return $this->belongsTo(Authentications::class, 'id', 'user_id');
    }

    /**
     * Relationship QuestionAnswers
     *
     * @return void
     */
    public function questionAnswers()
    {
        return $this->belongsTo(QuestionAnswer::class, 'id', 'user_id');
    }

    /**
     * Relationship Nation
     *
     * @return void
     */
    public function nation()
    {
        return $this->hasOne(MNation::class, 'id', 'info_nation_id');
    }

    /**
     * Relationship Contact Nation
     *
     * @return void
     */
    public function contactNation()
    {
        return $this->hasOne(MNation::class, 'id', 'contact_nation_id');
    }


    /**
     * Relationship Prefecture
     *
     * @return void
     */
    public function prefecture()
    {
        return $this->hasOne(MPrefecture::class, 'id', 'info_prefectures_id');
    }

    /**
     * Relationship Contact Prefecture
     *
     * @return void
     */
    public function contactPrefecture()
    {
        return $this->hasOne(MPrefecture::class, 'id', 'contact_prefectures_id');
    }

    /* Get storage path profile to confirm
     *
     * @return string
     */
    public function getStoragePathProfile(): string
    {
        return URL_STORATE_FILE_PROFILE . auth()->user()->id . '/profile.txt';
    }

    /**
     * List info gender
     */
    public static function listGenderOptions()
    {
        return [
            self::GENDER_MALE => __('labels.user_mod.male'),
            self::GENDER_FEMALE => __('labels.user_mod.female'),
        ];
    }

    /**
     * List info gender
     *
     * @return array
     */
    public function getListMail(): array
    {
        $emails = [];

        if (!empty($this->email)) {
            $emails[] = $this->email;
        }

        if (!empty($this->contact_email_second)) {
            $emails[] = $this->contact_email_second;
        }

        if (!empty($this->contact_email_three)) {
            $emails[] = $this->contact_email_three;
        }

        return $emails;
    }

    /**
     * Get gender
     *
     * @return null|string
     */
    public function getGender(): ?string
    {
        switch ($this->info_gender) {
            case self::GENDER_MALE:
                $gender = '女性';
                break;
            case self::GENDER_FEMALE:
                $gender = '男性';
                break;
            default:
                $gender = null;
        }
        return $gender;
    }
}
