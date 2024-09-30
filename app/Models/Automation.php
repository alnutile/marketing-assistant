<?php

namespace App\Models;

use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Automation extends Model
{
    use HasFactory;
    use HasSlug;

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'scheduled' => 'boolean',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function () {
                return Str::random(12);
            })
            ->saveSlugsTo('slug');
    }

    public function run(): void
    {
        //create automation_runs but in a moment
        if ($this->enabled) {

            Orchestrate::handle(
                project: $this->project,
                prompt: $this->prompt
            );

        } else {
            Log::info('Automation not enabled', [
                'automation' => $this->name,
            ]);
        }
    }
}
