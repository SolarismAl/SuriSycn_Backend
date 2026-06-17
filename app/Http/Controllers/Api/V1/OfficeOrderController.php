<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\OfficeOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfficeOrderController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = OfficeOrder::with('users:id,first_name,last_name,role')->orderBy('date_issued', 'desc');

        if (!$request->user()->isAdmin() && !$request->user()->isManager()) {
            $query->whereRaw('is_active = true')
                  ->whereHas('users', function($q) use ($request) {
                      $q->where('office_order_user.user_id', $request->user()->id);
                  });
        }

        return $this->successResponse($query->get(), 'Office orders retrieved successfully');
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isManager()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'memo_number' => 'nullable|string|max:255',
            'subject' => 'required|string',
            'description' => 'nullable|string',
            'date_issued' => 'required|date',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $order = DB::transaction(function () use ($validated, $request) {
            $order = OfficeOrder::create([
                'memo_number' => $validated['memo_number'] ?? null,
                'subject' => $validated['subject'],
                'description' => $validated['description'] ?? null,
                'date_issued' => $validated['date_issued'],
                'valid_from' => $validated['valid_from'],
                'valid_until' => $validated['valid_until'],
                'is_active' => $validated['is_active'] ?? true,
                'created_by' => $request->user()->id,
            ]);

            $order->users()->sync($validated['user_ids']);

            return $order;
        });

        return $this->successResponse($order->load('users:id,first_name,last_name,role'), 'Office order created successfully', 201);
    }

    public function update(Request $request, string $id)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isManager()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $order = OfficeOrder::findOrFail($id);

        $validated = $request->validate([
            'memo_number' => 'nullable|string|max:255',
            'subject' => 'sometimes|string',
            'description' => 'nullable|string',
            'date_issued' => 'sometimes|date',
            'valid_from' => 'sometimes|date',
            'valid_until' => 'sometimes|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'user_ids' => 'sometimes|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($order, $validated) {
            $order->update($validated);

            if (isset($validated['user_ids'])) {
                $order->users()->sync($validated['user_ids']);
            }
        });

        return $this->successResponse($order->fresh('users:id,first_name,last_name,role'), 'Office order updated successfully');
    }

    public function destroy(Request $request, string $id)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isManager()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $order = OfficeOrder::findOrFail($id);
        $order->delete();

        return $this->successResponse(null, 'Office order deleted successfully');
    }
}
