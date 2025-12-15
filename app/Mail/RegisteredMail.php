<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisteredMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $licensePath;

    /**
     * Create a new message instance.
     */
    public function __construct($user,$licensePath)
    {
       $this->user=$user;
       $this->licensePath=$licensePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registered Confirmed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {     
         return new Content(
            view: 'emails.registered', // your Blade view for the email
            with: [
                'user' => $this->user,
                'code'=>$this->user->id,
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
        return [
                Attachment::fromPath($this->licensePath)
                    ->as('pharmacy_license.' . pathinfo($this->licensePath, PATHINFO_EXTENSION))
                    ->withMime('application/pdf'),
            ];
    }
}
