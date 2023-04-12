<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'name',
        'path'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
