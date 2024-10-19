<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportPageResource\Pages;
use App\Filament\Resources\ReportPageResource\RelationManagers;
use App\Models\ReportPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportPageResource extends Resource
{
    protected static ?string $model = ReportPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make('Review')
                   ->columns(2)
                   ->schema([
                       Forms\Components\Select::make('report_id')
                           ->relationship('report', 'id')
                           ->required(),
                       Forms\Components\TextInput::make('sort')
                           ->required()
                           ->numeric()
                           ->default(0),
                       Forms\Components\TextInput::make('score')
                           ->required()
                           ->numeric()
                           ->default(0),
                       Forms\Components\TextInput::make('status')
                           ->maxLength(255),
                       Forms\Components\MarkdownEditor::make('review')
                           ->columnSpanFull(),
                       Forms\Components\MarkdownEditor::make('content')
                           ->columnSpanFull(),
                       ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('review')
                        ->wrap()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('id')
                        ->label('ID')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('sort')
                        ->label('Page')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('score')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('status')
                        ->searchable(),
                ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 2,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('report.id')
                    ->searchable()
                    ->preload()
                    ->relationship('report', 'file_name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListReportPages::route('/'),
            'create' => Pages\CreateReportPage::route('/create'),
            'view' => Pages\ViewReportPage::route('/{record}'),
            'edit' => Pages\EditReportPage::route('/{record}/edit'),
        ];
    }
}
