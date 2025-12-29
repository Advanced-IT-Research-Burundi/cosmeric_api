<?php

namespace App\Mail;

use App\Models\Assistance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssistanceCreate extends Mailable
{
    use Queueable, SerializesModels;

    public $assistance;

    /**
     * Create a new message instance.
     */
    public function __construct(Assistance $assistance)
    {
        $this->assistance = $assistance;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.assistance.create',
            with: [
                'assistance' => $this->assistance,
            ],
        );  
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
