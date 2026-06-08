<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $clientId = Auth::user()->client_id;

        $query = Logs::with('user:id,name,email,role')
            ->where('client_id', $clientId)
            ->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', 'like', $request->action . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'data'       => $logs->items(),
            'pagination' => [
                'total'        => $logs->total(),
                'per_page'     => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
            ],
        ]);
    }

    public function actions()
    {
        $clientId = Auth::user()->client_id;

        $actions = Logs::where('client_id', $clientId)
            ->distinct()
            ->pluck('action')
            ->sort()
            ->values();

        return response()->json(['data' => $actions]);
    }

    public function teamActivities()
    {
        $clientId = Auth::user()->client_id;

        $logs = Logs::with('user:id,name,email,role')
            ->where('client_id', $clientId)
            ->where('action', 'like', 'team.%')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($log) => [
                'id'         => $log->id,
                'user'       => $log->user?->name ?? 'Système',
                'action'     => $this->formatAction($log->action, $log->details ?? []),
                'type'       => $this->resolveType($log->action),
                'created_at' => $log->created_at,
            ]);

        return response()->json(['data' => $logs]);
    }

    private function formatAction(string $action, array $details): string
    {
        return match ($action) {
            'team.member_added'      => 'a ajouté ' . ($details['name'] ?? 'un membre') . ' à l\'équipe (rôle : ' . ($details['role'] ?? '?') . ')',
            'team.member_updated'    => 'a modifié le membre ' . ($details['after']['name'] ?? $details['name'] ?? 'inconnu'),
            'team.member_deleted'    => 'a supprimé ' . ($details['name'] ?? 'un membre') . ' de l\'équipe',
            'team.member_suspended'  => 'a suspendu ' . ($details['name'] ?? 'un membre') . ($details['reason'] ? ' — ' . $details['reason'] : ''),
            'team.member_reactivated'=> 'a réactivé ' . ($details['name'] ?? 'un membre'),
            default                  => $action,
        };
    }

    private function resolveType(string $action): string
    {
        return match ($action) {
            'team.member_added'       => 'create',
            'team.member_updated'     => 'edit',
            'team.member_deleted'     => 'delete',
            'team.member_suspended'   => 'suspend',
            'team.member_reactivated' => 'reactivated',
            default                   => 'edit',
        };
    }
}
