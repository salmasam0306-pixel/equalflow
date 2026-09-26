<?php
// app/Models/ProjectDocument.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasHashid;

class ProjectDocument extends Model
{
    use HasHashid;
    
    protected $fillable = [
        'project_id',
        'uploaded_by',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/project_documents/' . $this->file_path);
    }
}