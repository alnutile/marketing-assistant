<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutomationResource\Pages;
use App\Models\Automation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AutomationResource extends Resource
{
    protected static ?string $model = Automation::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Automation Prompt')
                    ->columns(6)
                    ->schema([
                        Forms\Components\Section::make('Details')
                            ->description(function (Automation $automation) {
                                /** @phpstan-ignore-next-line */
                                if (! $automation?->id) {
                                    return 'Will show link here for webhook once saved';
                                } else {

                                    return new HtmlString(sprintf('The webhook for this automation is <br>%s</br> it takes a POST request.',
                                        route('webhooks.show', $automation)));
                                }
                            })
                            ->columns(2)
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\MarkdownEditor::make('prompt')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Section::make('Settings')
                            ->columns(2)
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\Hidden::make('user_id')
                                    ->default(fn () => auth()->user()->id)
                                    ->required(),
                                Forms\Components\Toggle::make('feedback_required')
                                    ->columnSpanFull()
                                    ->default(fn () => false)
                                    ->helperText(sprintf('Require %s positive feedback before it really runs!', config('assistant.feedback_count'))),
                                Forms\Components\Select::make('project_id')
                                    ->columnSpanFull()
                                    ->relationship('project', 'name')->required(),
                                Forms\Components\Toggle::make('enabled')
                                    ->helperText('Hide from system automations, Pause')
                                    ->required(),
                                Forms\Components\Toggle::make('scheduled')
                                    ->helperText('Should the system run this every hour?')
                                    ->required(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->url(fn (Automation $automation) => route('projects.show', $automation->project))
                    ->formatStateUsing(function (Automation $automation) {
                        return new HtmlString(sprintf("<a class='underline' href='%s'>%s</a>",
                            route('projects.show', $automation->project),
                            $automation->project->name
                        ));
                    })
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->copyable()
                    ->copyMessage('Copied to clipboard!')
                    ->searchable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
                Tables\Columns\IconColumn::make('scheduled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('id', 'desc');
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
            'index' => Pages\ListAutomations::route('/'),
            'create' => Pages\CreateAutomation::route('/create'),
            'view' => Pages\ViewAutomation::route('/{record}'),
            'edit' => Pages\EditAutomation::route('/{record}/edit'),
        ];
    }
}
