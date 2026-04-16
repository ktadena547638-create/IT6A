<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'uploaded_by',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'file_path',
    ];

    /**
     * Attachment belongs to a task
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Attachment was uploaded by a user
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file URL for download
     */
    public function getFileUrlAttribute(): string
    {
        return route('tasks.attachments.download', ['attachment' => $this->id]);
    }

    /**
     * Get file icon class based on mime type
     */
    public function getIconClassAttribute(): string
    {
        return match ($this->mime_type) {
            'application/pdf' => 'bi-file-pdf text-danger',
            'image/jpeg', 'image/png', 'image/gif', 'image/webp' => 'bi-image text-info',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'bi-file-word text-primary',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'bi-file-spreadsheet text-success',
            'application/zip', 'application/x-zip-compressed' => 'bi-file-zip text-warning',
            default => 'bi-file text-secondary',
        };
    }

    /**
     * Get human-readable file size
     */
    public function getHumanFileSizeAttribute(): string
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($i < count($units) - 1 && $size >= 1024) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . ($units[$i] ?? 'B');
    }
}
