<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    protected $fillable = ['full_name', 'is_active', 'last_synced_at'];

    public function pullRequests()
        {
            return $this->hasMany(PullRequest::class);
        }
}
