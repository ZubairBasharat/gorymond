<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\UserResource;


use App\Models\Player;
use Dompdf\FrameDecorator\Text;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')->required(),
                TextInput::make('last_name')->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('phone'),
                TextInput::make('pin')
                    ->label('Numeric PIN')
                    ->numeric()
                    ->minLength(4)
                    ->maxLength(10),

                FileUpload::make('avatar')
                    ->disk('public')
                    ->acceptedFileTypes(['audio/mpeg'])
                    ->directory('avatars')
                    ->visibility('public'),


                Select::make('coach_id')
                    ->relationship(
                        name: 'users',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('first_name')->orderBy('last_name')
                    )
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name} ({$record->email})")
                    ->searchable(['first_name', 'last_name', 'email'])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),

                ImageColumn::make('avatar'),

                TextColumn::make('users.email')
                    ->label('Coach')  // this is the relationship
                    ->searchable()
                    ->sortable(),



            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

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
            'index' => Pages\ListPlayers::route('/'),
            'create' => Pages\CreatePlayer::route('/create'),
            'edit' => Pages\EditPlayer::route('/{record}/edit'),
        ];
    }
}
