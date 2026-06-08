<?php

namespace App\Mail;

use App\Models\Branding;
use App\Models\Campagnes;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Campagnes $campaign,
        public Branding  $branding,
        public object    $contact,
        public string    $content,
        public ?string   $ctaText          = null,
        public ?string   $ctaUrl           = null,
        public ?string   $unsubscribeUrl   = null,
        public ?string   $trackingPixelUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->name,
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    config('mail.from.address'),
                    $this->branding->brand_name ?? config('app.name')
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign-email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
