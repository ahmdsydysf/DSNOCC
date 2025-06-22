<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id', 'folder_id', 'name', 'path', 'created_by',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
