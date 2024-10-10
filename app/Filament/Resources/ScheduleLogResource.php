<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleLogResource\Pages;
use App\Models\Project;
use App\Models\ScheduleLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScheduleLogResource extends Resource
{
    protected static ?string $model = ScheduleLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\KeyValue::make('log_content')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('loggable_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('loggable_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loggable.name')
                    ->label('View Loggable')
                    ->url(function ($record) {
                        if (get_class($record->loggable) === Project::class) {
                            return route('projects.show', $record->loggable);
                        }
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListScheduleLogs::route('/'),
            'create' => Pages\CreateScheduleLog::route('/create'),
            'view' => Pages\ViewScheduleLog::route('/{record}'),
        ];
    }
}
