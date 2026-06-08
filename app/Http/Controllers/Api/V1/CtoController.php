<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CtoEntry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCtoRequest;
use App\Http\Requests\UpdateCtoStatusRequest;

class CtoController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = CtoEntry::with(['user', 'approver'])->orderBy('created_at', 'desc');

        if ($request->user()->isAdmin() || $request->user()->isManager()) {
            $userId = $request->query('user_id');
            if ($userId && $userId !== 'all') {
                $query->where('user_id', $userId);
            }
        } else {
            $query->where('user_id', $request->user()->id);
        }

        $entries = $query->get();
        return $this->successResponse($entries, 'CTO entries retrieved successfully');
    }

    public function balance(Request $request)
    {
        $userId = clone $request; // just to keep syntax clean

        if ($request->user()->isAdmin() || $request->user()->isManager()) {
            $targetUserId = $request->query('user_id');
            if (!$targetUserId || $targetUserId === 'all') {
                $targetUserId = null; // null means 'all'
            }
        } else {
            $targetUserId = $request->user()->id;
        }

        $query = CtoEntry::query();
        if ($targetUserId) {
            $query->where('user_id', $targetUserId);
        }

        $earned = (clone $query)->where('type', 'earned')->where('status', 'approved')->sum('hours');
        $used = (clone $query)->where('type', 'used')->where('status', 'approved')->sum('hours');
        $pending_earned = (clone $query)->where('type', 'earned')->where('status', 'pending')->sum('hours');
        $pending_used = (clone $query)->where('type', 'used')->where('status', 'pending')->sum('hours');

        return $this->successResponse([
            'total_earned' => $earned,
            'total_used' => $used,
            'available_balance' => $earned - $used,
            'pending_earned' => $pending_earned,
            'pending_used' => $pending_used,
        ], 'CTO balance retrieved successfully');
    }

    public function overview(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isManager()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $earned = clone CtoEntry::where('type', 'earned')->where('status', 'approved');
        $earnedData = $earned->selectRaw('user_id, SUM(hours) as total')->groupBy('user_id')->pluck('total', 'user_id');

        $used = clone CtoEntry::where('type', 'used')->where('status', 'approved');
        $usedData = $used->selectRaw('user_id, SUM(hours) as total')->groupBy('user_id')->pluck('total', 'user_id');

        $users = \App\Models\User::orderBy('first_name')->get();
        $overview = [];

        foreach ($users as $u) {
            $e = $earnedData[$u->id] ?? 0;
            $us = $usedData[$u->id] ?? 0;
            $overview[] = [
                'id' => $u->id,
                'name' => $u->first_name . ' ' . $u->last_name,
                'available_balance' => $e - $us,
            ];
        }

        return $this->successResponse($overview, 'Company CTO overview retrieved');
    }

    public function store(StoreCtoRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'pending';

        $entry = CtoEntry::create($data);

        return $this->successResponse($entry, 'CTO request submitted successfully', 201);
    }

    public function updateStatus(UpdateCtoStatusRequest $request, string $id)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isManager()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $entry = CtoEntry::findOrFail($id);
        $data = $request->validated();

        $entry->status = $data['status'];
        $entry->notes = $data['notes'] ?? $entry->notes;
        $entry->approved_by = $request->user()->id;
        $entry->save();

        return $this->successResponse($entry, 'CTO request status updated successfully');
    }

    public function bulkUpdateStatus(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isManager()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:cto_entries,id',
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        CtoEntry::whereIn('id', $validated['ids'])->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'approved_by' => $request->user()->id,
        ]);

        return $this->successResponse(null, 'CTO requests updated successfully');
    }
}
