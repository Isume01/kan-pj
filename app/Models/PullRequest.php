<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PullRequest extends Model
{
    protected $fillable = [
        'repository_id', 'github_pr_id', 'number', 'title',
        'body', 'user_login', 'state', 'diff_url', 'html_url','is_closed',
    ];

    public function aiSummary()
    {
        return $this->hasOne(AISummary::class);
    }

    public function aiReviews()
    {
        return $this->hasMany(AiReview::class);
    }
}
