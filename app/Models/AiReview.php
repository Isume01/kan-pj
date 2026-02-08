<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiReview extends Model
{
    protected $fillable = ['pull_request_id','coding_convention_id','review_result','status',];

    public function pullRequest()
    {
        return $this->belongsTo(PullRequest::class);
    }

    public function codingConvention()
    {
        return $this->belongsTo(CodingConvention::class);
    }
}
