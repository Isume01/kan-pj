<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AISummary extends Model
{
    protected $table = 'ai_summaries';

    protected $fillable = [
        'pull_request_id', 'summary', 'risk_score', 'model_name'
    ];
}
