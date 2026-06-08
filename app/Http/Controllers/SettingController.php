<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected function authUser(): User
    {
        /** @var User $user */
        $user = auth()->user();
        return $user;
    }

    public function index()
    {
        $user = $this->authUser();

        $notify = NotificationSetting::where('client_id', $user->client_id)
            ->with(['client'])
            ->get();

        return response()->json(['notify' => $notify], 200);
    }

    public function UserData()
    {
        $user = $this->authUser();

        return response()->json([
            'id'                 => $user->id,
            'client_id'          => $user->client_id,
            'name'               => $user->name,
            'email'              => $user->email,
            'phone'              => $user->phone,
            'role'               => $user->role,
            'company'            => $user->client->company_name ?? '',
            'bio'                => $user->bio ?? '',
            'two_factor_enabled' => $user->two_factor_enabled,
            'profilePicture'     => $user->profil ? asset('storage/' . $user->profil) : null,
        ], 200);
    }

    public function updateProfile(Request $request, $id)
    {
        $user = $this->authUser();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:20',
            'bio'     => 'nullable|string|max:500',
            'company' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'bio'   => $validated['bio'] ?? $user->bio,
        ]);

        if (!empty($validated['company']) && $user->client) {
            if (!in_array($user->role, ['super_admin', 'admin'])) {
                return response()->json([
                    'message' => 'Seul l\'administrateur peut modifier le nom de l\'entreprise.',
                ], 403);
            }
            $user->client->update(['company_name' => $validated['company']]);
        }

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'data' => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'bio'     => $user->bio,
                'company' => $user->client->company_name ?? null,
            ],
        ], 200);
    }

    public function updateProfilePicture(Request $request)
    {
        $user = $this->authUser();

        $request->validate([
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('profilePicture')) {
            if ($user->profil && Storage::disk('public')->exists($user->profil)) {
                Storage::disk('public')->delete($user->profil);
            }

            $path = $request->file('profilePicture')->store('profiles', 'public');
            $user->update(['profil' => $path]);

            return response()->json([
                'message'           => 'Photo de profil mise à jour',
                'profilePictureUrl' => asset('storage/' . $path),
            ], 200);
        }

        if ($request->input('profilePicture') === null && $user->profil) {
            Storage::disk('public')->delete($user->profil);
            $user->update(['profil' => null]);

            return response()->json([
                'message'           => 'Photo de profil supprimée',
                'profilePictureUrl' => null,
            ], 200);
        }

        return response()->json(['message' => 'Aucune modification effectuée'], 200);
    }

    public function updateNotificationSettings(Request $request, $id)
    {
        $user = $this->authUser();

        $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $notification = NotificationSetting::where('id', $id)
            ->where('client_id', $user->client_id)
            ->firstOrFail();

        $notification->enabled = $request->boolean('enabled');
        $notification->save();

        return response()->json([
            'message'      => 'Notification mise à jour avec succès',
            'notification' => $notification,
        ]);
    }

    public function updatePassword(Request $request, $id)
    {
        $user = $this->authUser();

        $validated = $request->validate([
            'currentPassword' => 'required|string',
            'newPassword'     => 'required|string|min:8',
            'confirmPassword' => 'required|string|same:newPassword',
        ]);

        if (!Hash::check($validated['currentPassword'], $user->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect'], 403);
        }

        $user->update(['password' => Hash::make($validated['newPassword'])]);

        return response()->json(['message' => 'Mot de passe mis à jour avec succès'], 200);
    }

    public function updateTwoFactorAuthentication(Request $request)
    {
        $user = $this->authUser();

        $validated = $request->validate([
            'two_factor_enabled' => 'required|boolean',
        ]);

        $user->update(['two_factor_enabled' => $validated['two_factor_enabled']]);

        return response()->json(['message' => 'Authentification à deux facteurs mise à jour'], 200);
    }

    public function updateIntegrations(Request $request)
    {
        $user = $this->authUser();

        $validated = $request->validate([
            'integrations'   => 'array',
            'integrations.*' => 'string|max:255',
        ]);

        $user->update(['integrations' => json_encode($validated['integrations'] ?? [])]);

        return response()->json(['message' => 'Intégrations mises à jour'], 200);
    }

    public function destroySession(Request $request)
    {
        $user = $this->authUser();

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();

        return response()->json(['message' => 'Sessions supprimées avec succès'], 200);
    }

    public function enable2FA(Request $request)
    {
        $user = $this->authUser();
        $user->update(['two_factor_enabled' => true]);

        return response()->json([
            'message'            => '2FA activé avec succès',
            'two_factor_enabled' => true,
        ]);
    }

    public function disable2FA(Request $request)
    {
        $user = $this->authUser();
        $user->update(['two_factor_enabled' => false]);

        return response()->json([
            'message'            => '2FA désactivé',
            'two_factor_enabled' => false,
        ]);
    }
}
