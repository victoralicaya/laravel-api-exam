<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'tags',
        'file',
        'due_date',
        'priority',
        'status',
        'date_completed',
        'archive'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files()
    {
        return $this->hasMany(FileUpload::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
}
