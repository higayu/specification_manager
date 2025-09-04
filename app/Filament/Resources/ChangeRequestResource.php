<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChangeRequestResource\Pages;
use App\Filament\Resources\ChangeRequestResource\RelationManagers;
use App\Models\ChangeRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class ChangeRequestResource extends Resource
{
    protected static ?string $model = ChangeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('requirement_id')
                    ->relationship('requirement', 'title')
                    ->required(),

                Forms\Components\Select::make('old_version_id')
                    ->relationship('oldVersion', 'id')
                    ->label('Old Version')
                    ->required(),

                Forms\Components\Select::make('new_version_id')
                    ->relationship('newVersion', 'id')
                    ->label('New Version')
                    ->nullable(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('requirement.title')->label('Requirement'),
                Tables\Columns\TextColumn::make('oldVersion.version')->label('Old Ver'),
                Tables\Columns\TextColumn::make('newVersion.version')->label('New Ver'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListChangeRequests::route('/'),
            'create' => Pages\CreateChangeRequest::route('/create'),
            'edit' => Pages\EditChangeRequest::route('/{record}/edit'),
        ];
    }

}
