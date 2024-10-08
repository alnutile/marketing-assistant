<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Automation;
use App\Models\Feedback;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feedback')
                    ->columns(2)
                    ->columnSpan(4)
                    ->schema([
                        Forms\Components\Textarea::make('comment')
                            ->rows(10)
                            ->columnSpanFull(),
                        Forms\Components\MorphToSelect::make('feedbackable')
                            ->columnSpanFull()
                            ->types([
                                MorphToSelect\Type::make(Automation::class)
                                    ->titleAttribute('name'),
                                MorphToSelect\Type::make(Message::class)
                                    ->titleAttribute('content'),
                            ]),
                        Forms\Components\Toggle::make('rating')
                            ->helperText('Rate the feedback on for positive')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comment')
                    ->limit(100)
                    ->searchable(),
                Tables\Columns\TextColumn::make('feedbackable.name')
                    ->label('Feedbackable')
                    ->searchable(),
                Tables\Columns\BooleanColumn::make('rating')
                    ->label('Rating')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created At')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListFeedback::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'view' => Pages\ViewFeedback::route('/{record}'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}
