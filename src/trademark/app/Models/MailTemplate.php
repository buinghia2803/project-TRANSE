<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "from_page",
        "guard_type",
        "type",
        "lang",
        "subject",
        "cc",
        "bcc",
        "content",
        "attachment",
    ];

    /**
     * Const
     */
    const MAIL_TEMPLATES_PASSWORD_RESET = 1;
    const MAIL_TEMPLATES_CONTACT = 2;

    const LANG_EN = 'en';
    const LANG_JP = 'ja';

    /**
     * TYPE SEND MAIL
     */
    const CREDIT_CARD = 1;
    const BANK_TRANSFER = 2;
    const TYPE_OTHER = 3;
    const TYPE_ANKEN_TOP = 4;

    const TYPE_REMIND_JOB = 5;

    /**
     * Guard type
     */
    const GUARD_TYPE_USER = 1;
    const GUARD_TYPE_ADMIN = 2;


    /**
     * Set type
     *
     * @var array
     */
    public array $types = [
        [
            'type' => self::MAIL_TEMPLATES_PASSWORD_RESET,
            'label' => 'labels.mail_template_reset_password',
            'note' => [
                ['param' => 'link_url', 'label' => 'labels.mail_template_link_reset_password'],
            ]
        ],
        [
            'type' => self::MAIL_TEMPLATES_CONTACT,
            'label' => 'labels.mail_template_contact',
        ],
    ];

    /**
     * Parse Field
     *
     * @param   array  $data
     * @param   string $field
     * @return  string|null
     */
    public function parse(array $data, string $field = 'content'): ?string
    {
        return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($data) {
            list($shortCode, $index) = $matches;
            if (isset($data[trim($index)])) {
                return $data[trim($index)];
            }
            return null;
        }, $this->$field);
    }
}
