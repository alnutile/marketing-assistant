<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Rawilk\FilamentPasswordInput\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                                Password::make('password')
                                    ->label('Password')
                                    ->copyable(color: 'warning')
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->regeneratePassword(color: 'primary')
                                    ->inlineSuffix(),
                            ]),
                    ]),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Meta Data')
                        ->schema([Forms\Components\Toggle::make('is_admin')
                            ->required()
                            ->default(1)
                            ->label('Is Admin'),
                        ]),
                    Forms\Components\Select::make('teams')
                        ->relationship('teams', 'name')
                        ->preload()
                        ->required()
                        ->live(onBlur: true)
                        ->pivotData([
                            'role' => 'admin',
                        ])
                        ->afterStateUpdated(function (User $user, Forms\Set $set, ?string $state) {
                            Log::info('UserResource::afterStateUpdated', [
                                'set' => $set,
                                'user' => $user?->id,
                                'state' => $state,
                            ]);
                            $user->forceFill([
                                'current_team_id' => $state,
                            ])->save();
                        })
                        ->label('Teams'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('teams.name'),
                Tables\Columns\ToggleColumn::make('is_admin')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
