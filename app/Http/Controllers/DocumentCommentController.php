<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Document $document)
    {
        try {
            $this->authorize('view', $document);

            $request->validate([
                'comment' => 'required|string|max:1000',
                'type' => 'required|in:general,instruction,feedback,approval,rejection',
                'is_internal' => 'boolean',
                'parent_id' => 'nullable|exists:document_comments,id',
            ]);

            $comment = DocumentComment::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'comment' => $request->comment,
                'type' => $request->type,
                'is_internal' => $request->boolean('is_internal', true),
                'parent_id' => $request->parent_id,
            ]);

            $comment->load('user', 'replies.user');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully.',
                'comment' => $comment,
                'html' => view('documents.partials.comment', compact('comment'))->render(),
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error adding comment: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding comment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Document $document, DocumentComment $comment)
    {
        try {
            // Verify the comment belongs to this document
            if ($comment->document_id !== $document->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment does not belong to this document.',
                ], 404);
            }

            $this->authorize('update', $comment);

            $request->validate([
                'comment' => 'required|string|max:1000',
            ]);

            $comment->update([
                'comment' => $request->comment,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully.',
                'comment' => $comment->fresh(),
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this comment.',
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating comment: ' . $e->getMessage(), [
                'comment_id' => $comment->id,
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating comment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Document $document, DocumentComment $comment)
    {
        try {
            // Verify the comment belongs to this document
            if ($comment->document_id !== $document->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment does not belong to this document.',
                ], 404);
            }

            $this->authorize('delete', $comment);

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this comment.',
            ], 403);
        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage(), [
                'comment_id' => $comment->id,
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting comment: ' . $e->getMessage(),
            ], 500);
        }
    }
}