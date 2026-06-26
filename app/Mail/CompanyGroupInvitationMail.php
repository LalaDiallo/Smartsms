<?php

namespace App\Mail;

use App\Models\CompanyGroup;
use App\Models\CompanyGroupBranch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyGroupInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public CompanyGroupBranch $branch;
    public CompanyGroup $group;
    public string $acceptUrl;

    public function __construct(CompanyGroupBranch $branch, CompanyGroup $group)
    {
        $this->branch    = $branch;
        $this->group     = $group;
        $this->acceptUrl = rtrim(config('app.frontend_url'), '/') . '/group-invitations/' . $branch->invitation_token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invitation à rejoindre le groupe « {$this->group->name} »",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company_group_invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
