<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoticeDetailBtn extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notice_detail_id',
        'btn_type',
        'url',
        'date_click',
        'from_page',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
    ];

    public $selectable = [
        '*',
    ];

    // Type BTN
    const BTN_CREATE_HTML = 1; // HTML作成
    const BTN_XML_UPLOAD = 2; // XMLアップロード
    const BTN_FIX = 3; // 修正
    const BTN_SEND_MAIL_REMIND = 4; // リマインドメール送信
    const BTN_CONTACT_CUSTOMER = 5; // お客様へ連絡
    const BTN_CONTACT_RESPONSE_PERSON = 6; // 責任者へ連絡
    const BTN_PDF_UPLOAD = 7; // PDFアップロード

    /**
     * Get Btn Text
     *
     * @return string
     */
    public function getBtnText(): string
    {
        $textBtn = '';

        switch ($this->btn_type) {
            case self::BTN_CREATE_HTML:
                $textBtn = __('labels.application_detail.notice_btn.create_html');
                break;
            case self::BTN_XML_UPLOAD:
                $textBtn = __('labels.application_detail.notice_btn.xml_upload');
                break;
            case self::BTN_FIX:
                $textBtn = __('labels.application_detail.notice_btn.fix');
                break;
            case self::BTN_SEND_MAIL_REMIND:
                $textBtn = __('labels.application_detail.notice_btn.send_mail_remind');
                break;
            case self::BTN_CONTACT_CUSTOMER:
                $textBtn = __('labels.application_detail.notice_btn.contact_customer');
                break;
            case self::BTN_CONTACT_RESPONSE_PERSON:
                $textBtn = __('labels.application_detail.notice_btn.contact_response_person');
                break;
            case self::BTN_PDF_UPLOAD:
                $textBtn = __('labels.application_detail.notice_btn.pdf_upload');
                break;
        }

        return $textBtn;
    }

    /**
     * Trademark Documents
     *
     * @return HasMany
     */
    public function trademarkDocuments(): HasMany
    {
        return $this->hasMany(TrademarkDocument::class, 'notice_detail_btn_id', 'id');
    }

    /**
     * Notice Detail
     *
     * @return BelongsTo
     */
    public function noticeDetail(): BelongsTo
    {
        return $this->belongsTo(NoticeDetail::class);
    }
}
