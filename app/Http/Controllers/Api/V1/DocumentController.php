<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends BaseApiController
{
    public function index(Request $request)
    {
        $parentId = $request->query('parent_id');
        $query = Document::with('owner')->orderBy('type', 'desc')->orderBy('name', 'asc');

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $documents = $query->get()->map(function ($doc) {
            $data = $doc->toArray();
            if ($doc->file_path) {
                $data['url'] = Storage::url($doc->file_path);
            }
            return $data;
        });

        return $this->successResponse($documents, 'Documents retrieved');
    }

    public function store(Request $request)
    {
        $isFolder = filter_var($request->input('is_folder'), FILTER_VALIDATE_BOOLEAN);
        $parentId = $request->input('parent_id');

        if ($isFolder) {
            $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|uuid|exists:documents,id',
            ]);
            $doc = Document::create([
                'name' => $request->name,
                'type' => 'folder',
                'parent_id' => $parentId,
                'owner_id' => $request->user()->id,
            ]);
            return $this->successResponse($doc, 'Folder created', 201);
        }

        if ($request->has('file_data')) {
            $request->validate([
                'file_name' => 'required|string',
                'file_data' => 'required|string',
                'file_size' => 'required|integer|max:10485760', // 10MB max
                'parent_id' => 'nullable|uuid|exists:documents,id',
            ]);

            $originalName = $request->input('file_name');
            $size = $request->input('file_size');
            $base64Data = $request->input('file_data');
            
            // Extract mime and data
            list($type, $data) = explode(';', $base64Data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            
            $mime = str_replace('data:', '', $type);
            $docType = 'other';
            if (str_contains($mime, 'pdf')) $docType = 'pdf';
            elseif (str_contains($mime, 'image')) $docType = 'image';
            elseif (str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel') || str_contains($mime, 'csv')) $docType = 'sheet';
            elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) $docType = 'doc';

            // Generate a unique filename
            $filename = Str::uuid() . '_' . $originalName;
            $path = 'documents/' . $filename;
            
            Storage::disk('public')->put($path, $data);

            $doc = Document::create([
                'name' => $originalName,
                'type' => $docType,
                'file_path' => $path,
                'size' => $size,
                'parent_id' => $parentId,
                'owner_id' => $request->user()->id,
            ]);
            
            $docData = $doc->toArray();
            $docData['url'] = Storage::url($path);

            return $this->successResponse($docData, 'File uploaded', 201);
        }

        return $this->errorResponse('No file provided', 400);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $doc = Document::findOrFail($id);
        $doc->update(['name' => $request->name]);

        return $this->successResponse($doc, 'Document renamed');
    }

    public function destroy(string $id)
    {
        $doc = Document::findOrFail($id);
        $this->deleteRecursive($doc);
        return $this->successResponse(null, 'Document deleted');
    }

    private function deleteRecursive(Document $doc)
    {
        if ($doc->type === 'folder') {
            $children = Document::where('parent_id', $doc->id)->get();
            foreach ($children as $child) {
                $this->deleteRecursive($child);
            }
        } else {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }
        $doc->delete();
    }
}

