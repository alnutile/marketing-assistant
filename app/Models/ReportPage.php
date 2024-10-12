<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPage extends Model
{
    /** @use HasFactory<\Database\Factories\ReportPageFactory> */
    use HasFactory;

    public $guarded = [];

    public function report(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
