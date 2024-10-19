<?php

namespace App\Models;

use App\Domains\Reports\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPage extends Model
{
    /** @use HasFactory<\Database\Factories\ReportPageFactory> */
    use HasFactory;

    public $guarded = [];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function report(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
