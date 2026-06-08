<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignRejectionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $campaignName;
    public $recipientName;
    public $reason;
    public $senderName;
    public $senderTitle;
    public $organization;
    public $contactEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($campaignName, $recipientName, $reason, $senderName, $senderTitle, $organization, $contactEmail)
    {
        $this->campaignName = $campaignName;
        $this->recipientName = $recipientName;
        $this->reason = $reason;
        $this->senderName = $senderName;
        $this->senderTitle = $senderTitle;
        $this->organization = $organization;
        $this->contactEmail = $contactEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Campaign Rejection Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign_rejection',
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
