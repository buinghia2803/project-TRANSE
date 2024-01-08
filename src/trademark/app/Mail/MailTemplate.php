<?php

namespace App\Mail;

use App\Models\MailTemplate as MailTemplateModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailTemplate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var     string
     */
    protected string $template;

    /**
     * Create a new message instance.
     *
     * @param   MailTemplateModel $mailTemplate
     * @param   array             $params
     * @return  void
     */
    public function __construct(MailTemplateModel $mailTemplate, array $params = [])
    {
        if (!empty($mailTemplate->subject)) {
            $subject = $mailTemplate->parse($params, 'subject');
            $this->subject($subject);
        }
        if (!empty($mailTemplate->attachment)) {
            $this->attach(public_path($mailTemplate->attachment));
        }
        if (!empty($mailTemplate->cc)) {
            $this->cc(explode(",", $mailTemplate->cc));
        }
        if (!empty($mailTemplate->bcc)) {
            $this->bcc(explode(",", $mailTemplate->bcc));
        }
        $this->template = $mailTemplate->parse($params, 'content');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->html('<html>' . $this->template . '</html>', 'text/html');
    }
}
