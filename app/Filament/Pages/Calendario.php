<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Calendario extends Page
{
    protected static ?string $navigationGroup = 'Agenda';
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Calendario';
    protected static ?string $title           = 'Calendario';

    protected static string $view = 'filament.pages.calendario';

    public ?int $doctorId = null;

    protected function doctorOptions(): Collection
    {
        $query = User::query()
            ->select(['id', 'name', 'last_name', 'email'])
            ->whereHas('roles', fn ($q) => $q->where('name', 'Doctor'))
            ->orderBy('name')
            ->orderBy('last_name');

        if (! $this->canViewAllDoctors()) {
            $currentDoctorId = Auth::id();
            if ($currentDoctorId) {
                $query->whereKey($currentDoctorId);
            }
        }

        return $query
            ->get()
            ->mapWithKeys(fn (User $user) => [$user->id => $user->display_name]);
    }

    protected function getViewData(): array
    {
        $options = $this->doctorOptions();

        if (! $this->canViewAllDoctors()) {
            $this->doctorId = Auth::id();
        } elseif ($this->doctorId !== null && $options->doesntContain(fn ($_, $key) => (int) $key === (int) $this->doctorId)) {
            // Si el doctor seleccionado ya no existe en las opciones, volver a "Todos"
            $this->doctorId = null;
        }

        return [
            'doctorOptions' => $options,
            'canViewAllDoctors' => $this->canViewAllDoctors(),
            'currentDoctorName' => Auth::user()?->display_name,
        ];
    }

    public function updatedDoctorId($value): void
    {
        if (! $this->canViewAllDoctors()) {
            $this->doctorId = Auth::id();

            return;
        }

        $this->doctorId = ($value !== null && $value !== '') ? (int) $value : null;
    }

    protected function canViewAllDoctors(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('Administrator') || $user->hasRole('Receptionist');
    }
}
