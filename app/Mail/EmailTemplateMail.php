<?php

namespace App\Mail;

use App\Models\Email as EmailTemplate;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable que envía una plantilla de email a un suscriptor.
 *
 * Uso en EmailController:
 *   Mail::to($subscriber->email)->send(new EmailTemplateMail($email, $subscriber));
 */
class EmailTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmailTemplate $template,
        public Subscriber    $subscriber
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->template->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.template',
            with: [
                'template'   => $this->template,
                'subscriber' => $this->subscriber,
            ],
        );
    }
}
