<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    /**
     * Return a paginated and filterable audit log list.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:100'],
            'module' => ['nullable', 'string', 'max:50'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = AuditLog::query()
            ->with([
                'user:id,name,email',
            ]);

        if (!empty($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('action', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }

        if (!empty($validated['action'])) {
            $query->where('action', $validated['action']);
        }

        if (!empty($validated['module'])) {
            $query->where('module', $validated['module']);
        }

        if (!empty($validated['ip_address'])) {
            $query->where('ip_address', $validated['ip_address']);
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $logs = $query
            ->latest('id')
            ->paginate($validated['per_page'] ?? 50);

        return response()->json([
            'status' => 'success',
            'count' => $logs->total(),
            'data' => $logs,
        ]);
    }

    /**
     * Return one audit log with its user information.
     */
    public function show(int $id): JsonResponse
    {
        $log = AuditLog::with([
            'user:id,name,email',
        ])->find($id);

        if (!$log) {
            return response()->json([
                'status' => 'error',
                'message' => 'Audit log not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $log,
        ]);
    }

    /**
     * Return audit-log summary values for frontend cards.
     */
    public function summary(): JsonResponse
    {
        $today = now()->toDateString();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_logs' => AuditLog::count(),

                'today_activities' => AuditLog::whereDate(
                    'created_at',
                    $today
                )->count(),

                'upload_actions' => AuditLog::where(
                    'module',
                    'FILE_UPLOAD'
                )->count(),

                'reconciliation_actions' => AuditLog::where(
                    'module',
                    'RECONCILIATION'
                )->count(),

                'exception_actions' => AuditLog::where(
                    'module',
                    'EXCEPTION'
                )->count(),

                'result_file_actions' => AuditLog::where(
                    'module',
                    'RESULT_FILE'
                )->count(),

                'authentication_actions' => AuditLog::where(
                    'module',
                    'AUTHENTICATION'
                )->count(),
            ],
        ]);
    }

    /**
     * Return distinct values required by frontend filters.
     */
    public function filters(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'actions' => AuditLog::query()
                    ->whereNotNull('action')
                    ->distinct()
                    ->orderBy('action')
                    ->pluck('action')
                    ->values(),

                'modules' => AuditLog::query()
                    ->whereNotNull('module')
                    ->distinct()
                    ->orderBy('module')
                    ->pluck('module')
                    ->values(),
            ],
        ]);
    }
}

AuditLog::create([
    'user_id' => $user->id,
    'action' => 'LOGIN',
    'module' => 'AUTHENTICATION',
    'description' => 'User logged into RMS.',
    'ip_address' => $request->ip(),
]);

AuditLog::create([
    'user_id' => $request->user()?->id,
    'action' => 'LOGOUT',
    'module' => 'AUTHENTICATION',
    'description' => 'User logged out of RMS.',
    'ip_address' => $request->ip(),
]);