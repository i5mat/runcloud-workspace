<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'taskName',
        'taskDurationFrom',
        'taskDurationTo',
        'taskStatus ',
        'task_userId',
        'task_workspaceId',
        'created_at',
        'updated_at'
    ];
}
