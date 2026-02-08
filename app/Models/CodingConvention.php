<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodingConvention extends Model
{

    protected $fillable = ['repository_id','name','content','is_active',];

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }

    public function aiReviews()
    {
        return $this->hasMany(AiReview::class);
    }
}
