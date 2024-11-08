<?php

namespace App\Filament\Resources\AllReservationsResource\Pages;

use App\Filament\Resources\AllReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Reservation;

class ListAllReservations extends ListRecords
{
    protected static string $resource = AllReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    public function getTabs(): array 
    {
      return [
        null => ListRecords\Tab::make('All')
        ->badge($this->getTotalCount()),
        'approved' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'approve'))
        ->badge($this->getCountByStatus('approve')),
        'declined' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'decline'))
        ->badge($this->getCountByStatus('decline')),
        'cancelled' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'cancelled'))
        ->badge($this->getCountByStatus('cancelled')),
        'ongoing' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'ongoing'))
        ->badge($this->getCountByStatus('ongoing')),
        'done' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'done'))
        ->badge($this->getCountByStatus('done')),
        'not appeared' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'not_appeared'))
        ->badge($this->getCountByStatus('not_appeared')),
        ];
    }
    protected function getTotalCount(): int
    {
        return Reservation::count(); 
    }

    protected function getCountByStatus(string $status): int
    {
        return Reservation::where('status', $status)->count(); 
    }
}
