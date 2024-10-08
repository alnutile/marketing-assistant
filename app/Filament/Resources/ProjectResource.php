<?php

namespace App\Filament\Resources;

use App\Domains\Campaigns\StatusEnum;
use App\Filament\Resources\ProjectResource\Pages;
use App\Jobs\SchedulerProjectJob;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project ')
                    ->columns(6)
                    ->schema([
                        Forms\Components\Section::make('Project Details')
                            ->description('Project Details')
                            ->columnSpan(4)
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->columnSpanFull()
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('start_date'),
                                Forms\Components\DatePicker::make('end_date'),
                                Forms\Components\TextInput::make('budget')
                                    ->numeric(),
                                Forms\Components\MarkdownEditor::make('target_audience')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Section::make('System Level Information')
                            ->label('System Level Information')
                            ->description('General System Level Information')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options(StatusEnum::class)
                                    ->default('draft'),
                                Forms\Components\Hidden::make('user_id')
                                    ->default(auth()->user()->id),
                                Forms\Components\Select::make('team_id')
                                    ->default(auth()->user()->current_team_id)
                                    ->relationship('team', 'name'),
                            ]),
                        Forms\Components\Section::make('Prompts')
                            ->description('Prompts for the LLM to use')
                            ->columnSpan(4)
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
                        }),
                    Tables\Actions\Action::make('run scheduler')
                        ->color('secondary')
                        ->icon('heroicon-m-clock')
                        ->action(function (Project $project) {
                            Notification::make()
                                ->title('Running scheduler')
                                ->success()
                                ->send();
                            SchedulerProjectJob::dispatchSync($project);
                            Notification::make()
                                ->title('Done')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
