<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AllReservationsResource\Pages;
use App\Filament\Resources\AllReservationsResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use RyanChandler\FilamentProgressColumn\ProgressColumn;
use Illuminate\Database\Eloquent\Model;

class AllReservationsResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'fas-address-book';
    
    protected static ?string $navigationLabel = 'Reservations';
    
    protected static ?string $modelLabel = 'Reservations';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'user.name';
    
    public static function getGlobalSearchResultTitle(Model $record): string
    {
      return $record->user->name;
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'service.service_name', 'payment.amount', 'reservation_date', 'status', 'payment.payment_status'];
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Reservation Date' => $record->reservation_date,
            'Vehicle Type' => $record->vehicleTypeName, 
        ];
    }
    
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'service', 'payment', 'vehicle.vehicleType']);
    }
    
    public static function getNavigationBadge(): ?string
    {
      return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
      return static::getModel()::count() > 10 ? 'warning' : 'success';
    }
    
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(Reservation::query()->whereNotIn('status', ['decline', 'cancelled', 'pending']))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Reservation ID')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('User Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('service.service_name')->label('Service')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vehicle_type_name')->label('Vehicle Type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('payment.amount')->label('Amount')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reservation_date')->label('Reservation Date')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Reservation Status')->sortable(),
                Tables\Columns\TextColumn::make('payment.payment_status')->label('Payment Status')->sortable()
                ->color(fn (string $state): string => match ($state) {
                    'not_paid' => 'gray',
                    'partialy_paid' => 'warning',
                    'fully_paid' => 'success',
                }),
            ])
            ->filters([
            
          ])
            ->actions([
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
            'index' => Pages\ListAllReservations::route('/'),
            'create' => Pages\CreateAllReservations::route('/create'),
            'edit' => Pages\EditAllReservations::route('/{record}/edit'),
        ];
    }
}
