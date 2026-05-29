<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends BaseApiController
{
    public function index()
    {
        return $this->successResponse(Department::all(), 'Departments retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department = Department::create($validated);
        return $this->successResponse($department, 'Department created successfully', 201);
    }
}
