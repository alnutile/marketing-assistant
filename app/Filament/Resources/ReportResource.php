<?php

namespace App\Filament\Resources;

use App\Domains\Reports\ReportTypes;
use App\Domains\Reports\StatusEnum;
use App\Domains\Reports\TemplatePrompts\StandardsCheckingPromptTemplate;
use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Details')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Section::make('Report Details')
                            ->columnSpan(2)
                            ->description(function (Report $report) {
                                return new HtmlString(sprintf('Report for file: <span class="font-bold">%s</span>', $report->file_name));
                            })
                            ->schema([
                                Forms\Components\MarkdownEditor::make('prompt')
                                    ->helperText("Focus on the goal of the assistant. What to look for etc. The tool will take care of formatting and scoring")
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
                                Forms\Components\Select::make('user')
                                    ->label('Owner')
                                    ->relationship('user', 'name')
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Report Assets and Results')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\FileUpload::make('file_name')
                                    ->disk('reports')
                                    ->preserveFilenames()
                                    ->columnSpanFull()
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->default(StatusEnum::Pending->value)
                                    ->options(StatusEnum::class),
                                Forms\Components\MarkdownEditor::make('summary_of_results')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('overall_score')
                                    ->numeric(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('summary_of_results')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}
