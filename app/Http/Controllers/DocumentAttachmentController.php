<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\DocumentHistory;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DocumentAttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => [
                'required',
                'file',
                'max:10240', 
                'mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx',
            ],
        ], [
            'files.*.max' => 'Each file must not exceed 10MB.',
            'files.*.mimes' => 'Allowed file types: PDF, DOC, DOCX, JPG, JPEG, PNG, XLS, XLSX.',
        ]);

        try {
            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(32) . '.' . $extension;
                
                // Store file in public disk
                $path = $file->storeAs('documents/' . date('Y/m'), $filename, 'public');

                // Create attachment record
                $attachment = DocumentAttachment::create([
                    'document_id' => $document->id,
                    'original_name' => $originalName,
                    'file_name' => $filename,
                    'file_path' => $path,
                    'file_type' => $extension,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => Auth::id(),
                    'is_active' => true,
                ]);

                $uploadedFiles[] = [
                    'id' => $attachment->id,
                    'original_name' => $attachment->original_name,
                    'file_size' => $attachment->getFileSizeHumanAttribute(),
                    'file_type' => $attachment->file_type,
                    'download_url' => route('attachments.download', $attachment),
                ];
            }

            // Log the attachment upload
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'action' => 'attachments_added',
                'description' => 'Added ' . count($uploadedFiles) . ' file(s): ' . implode(', ', array_column($uploadedFiles, 'original_name')),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Get fresh count after upload
            $document->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully.',
                'files' => $uploadedFiles,
                'total_count' => $document->attachments_count,
            ]);

        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error uploading files: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function view(DocumentAttachment $attachment)
    {
        $this->authorize('view', $attachment->document);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        // Get file path
        $filePath = Storage::disk('public')->path($attachment->file_path);
        
        // Special handling for Excel files
        if (in_array(strtolower($attachment->file_type), ['xls', 'xlsx'])) {
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type',
            ];
        } else {
            // Your existing headers logic
            $headers = [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
            ];
        }

        return response()->file($filePath, $headers);
    }

    public function destroy(Document $document, DocumentAttachment $attachment)
    {
        if ($attachment->document_id !== $document->id) {
            abort(403, 'Unauthorized');
        }

        $this->authorize('update', $document);

        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Log the deletion
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'action' => 'attachment_deleted',
                'description' => "Deleted file: {$attachment->original_name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Delete record
            $attachment->delete();

            // Get fresh count after deletion
            $document->refresh();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
                'total_count' => $document->attachments_count,
            ]);

        } catch (\Exception $e) {
            Log::error('File delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting file: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(DocumentAttachment $attachment)
    {
        $this->authorize('view', $attachment->document);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }
}