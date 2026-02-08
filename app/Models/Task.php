<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatus;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
    ];

        protected $appends = [
            'status_label',
        ];
    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label() ?? '';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
