<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected string $mailView;
    protected array $params;

    /**
     * Create a new message instance.
     *
     * @param   string $view
     * @param   array  $params
     * @return  void
     */
    public function __construct(string $view, array $params)
    {
        $this->mailView = $view;
        $this->params = $params;

        $this->subject($this->params['subject'] ?? '');

        if (!empty($this->params['attachment'])) {
            $this->attach($this->params['attachment']);
        }
        if (!empty($this->params['attachments'])) {
            foreach ($this->params['attachments'] as $attach) {
                $this->attach($attach);
            }
        }
        if (!empty($this->params['cc'])) {
            $this->cc($this->params['cc']);
        }
        if (!empty($this->params['bcc'])) {
            $this->bcc($this->params['bcc']);
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->mailView, $this->params);
    }
}
