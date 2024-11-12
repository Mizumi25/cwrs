<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CancelledAndDeclinedReservationsResource\Pages;
use App\Filament\Resources\CancelledAndDeclinedReservationsResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class CancelledAndDeclinedReservationsResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'fas-spinner';
    
    protected static ?string $navigationLabel = 'Archive & History';
    
    protected static ?string $modelLabel = 'Archived Reservations';
    
    protected static ?string $navigationParentItem = 'Reservations';
    
    protected static ?int $navigationSort = 2;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['decline', 'cancelled'])->withTrashed()->count(); 
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
      return static::getModel()::whereIn('status', ['decline', 'cancelled'])->withTrashed()->count() > 10 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Reservation::query()->whereIn('status', ['decline', 'cancelled'])->withTrashed())
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Reservation ID')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('User Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('service.service_name')->label('Service')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vehicle_type_name')->label('Vehicle Type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('payment.amount')->label('Amount')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reservation_date')->label('Reservation Date')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Reservation Status')->sortable()
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\RestoreAction::make()
                ->after(function ($record) {
                    $record->update(['status' => 'pending', 'decline_message' => null,]);
                })
                  ->successRedirectUrl(route('filament.admin.resources.pending-reservations.index'))
                  ->successNotification(
                   Notification::make()
                        ->success()
                        ->title('User restored')
                        ->body('The user has been restored successfully.'),
                ),
            ])
            ->bulkActions([
                
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
            'index' => Pages\ListCancelledAndDeclinedReservations::route('/'),
            'create' => Pages\CreateCancelledAndDeclinedReservations::route('/create'),
            'view' => Pages\ViewCancelledAndDeclinedReservations::route('/{record}'),
            'edit' => Pages\EditCancelledAndDeclinedReservations::route('/{record}/edit'),
        ];
    }
}
