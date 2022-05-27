<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspaceName',
        'workspaceStatus',
        'workspace_userId',
        'created_at',
        'updated_at'
    ];
}
