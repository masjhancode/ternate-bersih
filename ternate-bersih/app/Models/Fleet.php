<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fleet extends Model
{
    protected $guarded = ['id'];

    public function reportProgresses(): HasMany
    {
        return $this->hasMany(ReportProgress::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
