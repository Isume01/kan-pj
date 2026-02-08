<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiReview extends Model
{
    public function pullRequest()
    {
        return $this->belongsTo(PullRequest::class);
    }

    public function codingConvention()
    {
        return $this->belongsTo(CodingConvention::class);
    }
}
