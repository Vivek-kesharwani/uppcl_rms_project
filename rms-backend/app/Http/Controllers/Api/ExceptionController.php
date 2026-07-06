<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExceptionRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExceptionController extends Controller
{
    public function index(Request $request)
    {
        $query = ExceptionRecord::with([
            'reconciliationResult.batch',
            'reconciliationResult.matchingSet',
            'reconciliationResult.leftSource',
            'reconciliationResult.rightSource',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('exception_code')) {
            $query->where('exception_code', $request->exception_code);
        }

        if ($request->filled('txn_id')) {
            $query->where('txn_id', 'like', '%' . $request->txn_id . '%');
        }

        if ($request->filled('case_number')) {
            $query->where('case_number', 'like', '%' . $request->case_number . '%');
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', 'like', '%' . $request->assigned_to . '%');
        }

        if ($request->filled('batch_id')) {
            $query->whereHas('reconciliationResult', function ($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            });
        } 

        if ($request->filled('matching_set_id')) {
            $query->whereHas('reconciliationResult', function ($q) use ($request) {
                $q->where('matching_set_id', $request->matching_set_id);
            });
        }

        if ($request->filled('business_date')) {
            $query->whereHas('reconciliationResult', function ($q) use ($request) {
                $q->whereDate('business_date', $request->business_date);
            });
        }

        $query->latest();

        $exceptions = $query->paginate(
            $request->integer('per_page', 50)
        );

        return response()->json([
            'status' => 'success',
            'count' => $exceptions->total(),
            'data' => $exceptions,
        ]);
    }

    public function show($id)
    {
        $exception = $this->findException($id);

        if (!$exception) {
            return $this->notFound();
        }

        return response()->json([
            'status' => 'success',
            'data' => $exception->load([
                'reconciliationResult.batch',
                'reconciliationResult.matchingSet',
                'reconciliationResult.leftSource',
                'reconciliationResult.rightSource',
            ]),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string',
            'priority' => 'nullable|in:LOW,MEDIUM,HIGH,CRITICAL',
            'root_cause' => 'nullable|string',
        ]);

        $exception = $this->findException($id);

        if (!$exception) {
            return $this->notFound();
        }

        $exception->update($request->only([
            'remarks',
            'priority',
            'root_cause',
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Exception case updated successfully.',
            'data' => $exception,
        ]);
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_role' => 'required|string',
            'assigned_to' => 'nullable|string',
        ]);

        $exception = $this->findException($id);

        if (!$exception) {
            return $this->notFound();
        }

        if ($response = $this->validateTransition($exception, 'ASSIGNED')) {
            return $response;
        }

        $exception->update([
            'status' => 'ASSIGNED',
            'assigned_role' => $request->assigned_role,
            'assigned_to' => $request->assigned_to,
            'assigned_at' => now(),
            'resolved_by' => null,
            'resolved_at' => null,
            'verified_by' => null,
            'verified_at' => null,
            'closed_by' => null,
            'closed_at' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception assigned successfully.',
            'data' => $exception,
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'resolution_notes' => 'required|string',
            'root_cause' => 'nullable|string',
        ]);

        $exception = $this->findException($id);

        if (!$exception) {
            return $this->notFound();
        }

        if ($response = $this->validateTransition($exception, 'RESOLVED')) {
            return $response;
        }

        $exception->update([
            'status' => 'RESOLVED',
            'resolution_notes' => $request->resolution_notes,
            'root_cause' => $request->root_cause,
            'resolved_by' => auth()->user()?->name,
            'resolved_at' => now(),
            'verified_by' => null,
            'verified_at' => null,
            'closed_by' => null,
            'closed_at' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception resolved successfully.',
            'data' => $exception,
        ]);
    }

    public function verify(Request $request, $id)
    {
        $exception = $this->findException($id);

        if (!$exception) {
            return $this->notFound();
        }

        if ($response = $this->validateTransition($exception, 'VERIFIED')) {
            return $response;
        }

        $exception->update([
            'status' => 'VERIFIED',
            'verified_by' => auth()->user()?->name,
            'verified_at' => now(),
            'closed_by' => null,
            'closed_at' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception verified successfully.',
            'data' => $exception,
        ]);
    }

    public function close(Request $request, $id)
    {
        $exception = $this->findException($id);

        if (!$exception) {
            return $this->notFound();
        }

        if ($response = $this->validateTransition($exception, 'CLOSED')) {
            return $response;
        }

        $exception->update([
            'status' => 'CLOSED',
            'closed_by' => auth()->user()?->name,
            'closed_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception closed successfully.',
            'data' => $exception,
        ]);
    }

    public function reopen($id)
    {
        $exception = $this->findException($id);

        if (!$exception) {
            return $this->notFound();
        }

        if (!in_array($exception->status, ['RESOLVED', 'VERIFIED', 'CLOSED'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only resolved, verified or closed cases can be reopened.',
            ], 422);
        }

        $exception->update([
            'status' => 'OPEN',
            'resolved_by' => null,
            'resolved_at' => null,
            'verified_by' => null,
            'verified_at' => null,
            'closed_by' => null,
            'closed_at' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception reopened successfully.',
            'data' => $exception,
        ]);
    }

    private function validateTransition(ExceptionRecord $exception, string $nextStatus): ?JsonResponse
    {
        $allowedTransitions = [
            'OPEN' => ['ASSIGNED'],
            'ASSIGNED' => ['RESOLVED'],
            'RESOLVED' => ['VERIFIED'],
            'VERIFIED' => ['CLOSED'],
            'CLOSED' => [],
        ];

        if (!in_array($nextStatus, $allowedTransitions[$exception->status] ?? [])) {
            return response()->json([
                'status' => 'error',
                'message' => "Invalid status transition from {$exception->status} to {$nextStatus}.",
            ], 422);
        }

        return null;
    }

    private function findException($id): ?ExceptionRecord
    {
        return ExceptionRecord::find($id);
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Exception case not found',
        ], 404);
    }

    public function summary()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_exceptions' => \App\Models\ExceptionRecord::count(),
                'open' => \App\Models\ExceptionRecord::where('status', 'OPEN')->count(),
                'assigned' => \App\Models\ExceptionRecord::where('status', 'ASSIGNED')->count(),
                'resolved' => \App\Models\ExceptionRecord::where('status', 'RESOLVED')->count(),
                'verified' => \App\Models\ExceptionRecord::where('status', 'VERIFIED')->count(),
                'closed' => \App\Models\ExceptionRecord::where('status', 'CLOSED')->count(),
                'high_priority' => \App\Models\ExceptionRecord::whereIn('priority', ['HIGH', 'CRITICAL'])->count(),
                'sla_breached' => \App\Models\ExceptionRecord::where('sla_breached', true)->count(),
            ],
        ]);
    }
}