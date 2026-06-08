<?php

namespace App\Http\Controllers;

use App\Models\Branding;
use App\Models\Messages;
use App\Models\Responses;
use App\Models\SenderName;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function show(string $token)
    {
        $message = Messages::where('reply_token', $token)
            ->with('campaign.client')
            ->first();

        if (!$message || !$message->campaign) {
            return view('reply-to-campaign', [
                'invalid' => true,
                'token'   => $token,
            ]);
        }

        $campaign = $message->campaign;

        // Sender name actif du client
        $activeSender = SenderName::where('client_id', $campaign->client_id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->latest()
            ->first();

        $senderName = $activeSender?->name
            ?? $campaign->client?->company_name
            ?? "l'expediteur";

        // Branding pour l'identite visuelle de la page
        $branding = Branding::where('client_id', $campaign->client_id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->latest()
            ->first();

        return view('reply-to-campaign', [
            'token'      => $token,
            'senderName' => $senderName,
            'tokenValid' => true,
            'branding'   => $branding,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = Messages::where('reply_token', $token)->first();

        if (!$message) {
            return view('reply-to-campaign', [
                'invalid' => true,
                'token'   => $token,
            ]);
        }

        $alreadyReplied = Responses::where('message_id', $message->id)->exists();

        if ($alreadyReplied) {
            return redirect()->route('reply.show', $token)
                ->with('already_replied', true);
        }

        $campaign = $message->campaign;

        $activeSender = SenderName::where('client_id', $campaign?->client_id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->latest()
            ->first();

        $senderName = $activeSender?->name
            ?? $campaign?->client?->company_name
            ?? "l'expediteur";

        Responses::create([
            'message_id'  => $message->id,
            'contact_id'  => $message->contact_id,
            'content'     => $request->content,
            'received_at' => Carbon::now(),
        ]);

        return redirect()->route('reply.show', $token)
            ->with('success', true)
            ->with('senderName', $senderName);
    }
}
