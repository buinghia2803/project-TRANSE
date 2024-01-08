<?php

namespace App\Services\XMLProcedures;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * This is not a spelling error.
 * It's because we are retrieving the tag from an XML file to map it with the system's class, and the tag in the XML file is named as such.
 */
class ProcedureInfomation extends BaseClsProcedure
{
    // Document name from XML of the Patent Office
    const REGISTRATION_ASSESSMENT = '登録査定';
    const NOTIFICATION_REASONS_REFUSAL = '拒絶理由通知書';
    const DECISION_REFUSAL = '拒絶査定';
    const NOTICE = '通知書';
    const CORRECTION_ORDER = '補正指令書';
    const FILE_CORRECTION_NOTICE = 'ファイル訂正通知書';
    const TRADEMARK_REGISTRATION_APPLICATION = '商標登録願';

    public $result;
    public $law;
    public $document_name;
    public $document_code;
    public $file_reference_id;
    public $invention_title;
    public $application_reference;
    public $submission_date;
    public $page;
    public $image_total;
    public $size;
    public $receipt_number;
    public $wad_message_digest_compare;
    public $input_date;
    public $html_file_name;
    public ApplicantArticle $applicant_article;
    public $payment;
    public $claims_total;
    public $abstract;
    public $representation_image;
    public $time_for_responce;
    public $dispatch_number;
    public $dispatch_date;

    /**
     * Get document name of procedure information.
     * @return string
     */
    public function getDocumentName(): string
    {
        try {
            $documentNames = [
                self::REGISTRATION_ASSESSMENT,
                self::NOTIFICATION_REASONS_REFUSAL,
                self::DECISION_REFUSAL,
                self::NOTICE,
                self::CORRECTION_ORDER,
                self::FILE_CORRECTION_NOTICE,
                self::TRADEMARK_REGISTRATION_APPLICATION,
            ];
            if (isset($this->document_name) && isset($this->document_name['text'])) {
                if(is_array($this->document_name['text']) && count($this->document_name['text']) > 0) {
                    if(in_array($this->document_name['text'][0], $documentNames)) {
                        return $this->document_name['text'][0];
                    } else {
                        throw new \Exception(__('messages.general.import_app_trademark_error'));
                    }
                } else {
                    if(in_array($this->document_name['text'], $documentNames)) {
                        return $this->document_name['text'];
                    } else {
                        throw new \Exception(__('messages.general.import_app_trademark_error'));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error($e);

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get application reference from procedure information.
     * @return string
     */
    public function getAppNumber(): string
    {
        if(isset($this->application_reference)
            && isset($this->application_reference->application_number)
            && $this->application_reference->application_number
        ) {
            return $this->application_reference->application_number;
        } else {
            return '';
        }
    }

    /**
     * Get application reference from procedure information.
     * @return string
     */
    public function getDispatchDate($format = 'Y/m/d'): string
    {
        if(isset($this->dispatch_date)
            && isset($this->dispatch_date['date'])
            && $this->dispatch_date['date']
        ) {
            return Carbon::createFromFormat('Ymd', $this->dispatch_date['date'])->format($format);
        }

        return '';
    }

    /**
     * convertDispatchDate
     * @return Carbon
     */
    public function convertDispatchDate(): Carbon
    {
        if ($this->dispatch_date
            && isset($this->dispatch_date['date'])
            && isset($this->dispatch_date['time'])
            && $this->dispatch_date['date']
        ) {
            $date = $this->dispatch_date['date']. ' ' .$this->dispatch_date['time'];
            return Carbon::createFromFormat('Ymd His', $date);
        } else {
            return now();
        }
    }

    /**
     * Get reference ID
     * @return string
     */
    public function getReferenceId(): string
    {
        if(isset($this->application_reference)
            && isset($this->application_reference->reference_id)
            && $this->application_reference->reference_id
        ) {
            return $this->application_reference->reference_id;
        }

        return '';
    }

    /**
     * Get File reference ID
     * @return string
     */
    public function getFileReferenceId(): string
    {
        return $this->file_reference_id ?? '';
    }

    /**
     * Get reference ID
     * @return string
     */
    public function getDispatchNumber(): string
    {
        return $this->dispatch_number ?? '';
    }

    /**
     * Get time for responce
     * @return array
     */
    public function getTimeResponse(): array
    {
        $result = [];
        if (isset($this->time_for_responce) && isset($this->time_for_responce['division']) && !is_array($this->time_for_responce['division'])) {
            $result['division'] = $this->time_for_responce['division'];
        } else {
            $result['division'] = '';
        }

        if (isset($this->time_for_responce) && isset($this->time_for_responce['period']) && !is_array($this->time_for_responce['period'])) {
            $result['period'] = $this->time_for_responce['period'];
        } else {
            $result['period'] = '';
        }

        return $result;
    }

    /**
     * Get representation image
     * @return array
     */
    public function getRepresentationImg()
    {
        $result = [];
        if (isset($this->representation_image) && isset($this->representation_image['title']) && !is_array($this->representation_image['title'])) {
            $result['title'] = $this->representation_image['title'];
        } else {
            $result['title'] = '';
        }

        if (isset($this->representation_image) && isset($this->representation_image['file_name']) && !is_array($this->representation_image['file_name'])) {
            $result['file_name'] = $this->representation_image['file_name'];
        } else {
            $result['file_name'] = '';
        }

        return $result;
    }
}
