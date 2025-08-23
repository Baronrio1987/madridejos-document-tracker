<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class DocumentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'original_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'uploaded_by',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute()
    {
        return route('attachments.download', $this);
    }

    public function getFullPathAttribute()
    {
        return Storage::path($this->file_path);
    }

    public function fileExists()
    {
        return Storage::exists($this->file_path);
    }

    public function getFileIconAttribute()
    {
        $iconMap = [
            'pdf' => 'bi-file-earmark-pdf',
            'doc' => 'bi-file-earmark-word',
            'docx' => 'bi-file-earmark-word',
            'xls' => 'bi-file-earmark-excel',
            'xlsx' => 'bi-file-earmark-excel',
            'ppt' => 'bi-file-earmark-ppt',
            'pptx' => 'bi-file-earmark-ppt',
            'jpg' => 'bi-file-earmark-image',
            'jpeg' => 'bi-file-earmark-image',
            'png' => 'bi-file-earmark-image',
            'gif' => 'bi-file-earmark-image',
            'txt' => 'bi-file-earmark-text',
            'zip' => 'bi-file-earmark-zip',
            'rar' => 'bi-file-earmark-zip',
        ];

        return $iconMap[strtolower($this->file_type)] ?? 'bi-file-earmark';
    }

    public function isImage()
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array(strtolower($this->file_type), $imageTypes);
    }

    public function isDocument()
    {
        $documentTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        return in_array(strtolower($this->file_type), $documentTypes);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('file_type', $type);
    }

    public function scopeUploadedBy($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }
    public function deleteFile()
    {
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
        
        return $this->delete();
    }

    public function softDelete()
    {
        return $this->update(['is_active' => false]);
    }

    public function restore()
    {
        return $this->update(['is_active' => true]);
    }

    public static function findByDocumentAndFilename($documentId, $filename)
    {
        return static::where('document_id', $documentId)
                    ->where('file_name', $filename)
                    ->first();
    }

    public static function getTotalSizeForDocument($documentId)
    {
        return static::where('document_id', $documentId)
                    ->active()
                    ->sum('file_size');
    }

    public static function getCountForDocument($documentId)
    {
        return static::where('document_id', $documentId)
                    ->active()
                    ->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($attachment) {
            \App\Models\DocumentHistory::create([
                'document_id' => $attachment->document_id,
                'user_id' => $attachment->uploaded_by,
                'action' => 'attachment_added',
                'description' => "File '{$attachment->original_name}' was uploaded",
                'new_values' => $attachment->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::deleted(function ($attachment) {
            \App\Models\DocumentHistory::create([
                'document_id' => $attachment->document_id,
                'user_id' => auth()->id(),
                'action' => 'attachment_deleted',
                'description' => "File '{$attachment->original_name}' was deleted",
                'old_values' => $attachment->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}