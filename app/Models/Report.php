<?php

namespace App\Models;

use App\Domains\Reports\ReportTypes;
use App\Domains\Reports\StatusEnum;
use App\Domains\Reports\TemplatePrompts\StandardsCheckingPromptTemplate;
use Filament\Forms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => StatusEnum::class,
        'report_type' => ReportTypes::class,
    ];

    public static function getForm(): array
    {
        return [
            Forms\Components\Section::make('Report Details')
                ->description(function (Report $report) {
                    return new HtmlString(sprintf('Report for file: <span class="font-bold">%s</span>', $report->file_name));
                })
                ->schema([
                    Forms\Components\FileUpload::make('file_name')
                        ->disk('reports')
                        ->preserveFilenames()
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\MarkdownEditor::make('prompt')
                        ->default(StandardsCheckingPromptTemplate::getPrompt())
                        ->columnSpanFull(),
                    Forms\Components\Select::make('report_type')
                        ->required()
                        ->options(ReportTypes::class)
                        ->default(ReportTypes::StandardsChecking->value),
                    Forms\Components\Select::make('project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->required(),
                ]),
            Forms\Components\Section::make('Report Results')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->default(StatusEnum::Pending->value)
                        ->options(StatusEnum::class),
                    Forms\Components\MarkdownEditor::make('summary_of_results')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('overall_score')
                        ->numeric(),
                ]),
        ];
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function report_page(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReportPage::class);
    }
}
