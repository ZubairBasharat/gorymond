<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ToneResource\Pages;
use App\Filament\Resources\ToneResource\RelationManagers;
use App\Models\Tone;
use App\Tables\Columns\ToneColumn;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ToneResource extends Resource
{
    protected static ?string $model = Tone::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),

                FileUpload::make('file')
                    ->disk('public')
                    ->directory('tones')
                    ->visibility('public'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('file')
                //     ->searchable()
                //     ->sortable(),


                ToneColumn::make('file')


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

    public static function columns(Columns $columns): Column
    {
        return $columns
            ->add('file')
            ->label('Audio')
            ->template(function (Tone $record) {
                return view('filament.resources.tone.custom-audio-column', ['record' => $record]);
            })
            ->emptyValue('-');
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
            'index' => Pages\ListTones::route('/'),
            'create' => Pages\CreateTone::route('/create'),
            'edit' => Pages\EditTone::route('/{record}/edit'),
        ];
    }
}
