<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;



    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("Project Details")
                    ->description("Project Details")
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\DatePicker::make('end_date'),
                        Forms\Components\TextInput::make('product_or_service')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('budget')
                            ->numeric(),
                        Forms\Components\Textarea::make('target_audience')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make("Prompts")
                    ->description("Prompts for the LLM to use")
                    ->columns(2)
                    ->schema([
                        Forms\Components\MarkdownEditor::make('system_prompt')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\MarkdownEditor::make('content')
                            ->required()
                            ->label('Context Prompt')
                            ->columnSpanFull(),
                        Forms\Components\MarkdownEditor::make('scheduler_prompt')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make("System Level Information")
                    ->label("System Level Information")
                    ->description("General System Level Information")
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('status')
                            ->required()
                            ->maxLength(255)
                            ->default('draft'),
                        Forms\Components\TextInput::make('chat_status')
                            ->required()
                            ->maxLength(255)
                            ->default('complete'),
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->user()->id),
                        Forms\Components\Select::make('team_id')
                            ->relationship('team', 'name'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->limit(35)
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                        Tables\Actions\ActionGroup::make([
                            Tables\Actions\ViewAction::make(),
                            Tables\Actions\EditAction::make(),
                            Tables\Actions\Action::make('chat with project')
                                ->color('secondary')
                                ->icon('heroicon-m-chat-bubble-bottom-center-text')
                                ->action(function (Project $project) {
                                    return to_route('projects.show', $project);
                                })
                        ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort("created_at", "desc");
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
