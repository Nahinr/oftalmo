<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

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
        return User::query()
            ->select(['id', 'name', 'last_name', 'email'])
            ->whereHas('roles', fn ($q) => $q->where('name', 'Doctor'))
            ->orderBy('name')
            ->orderBy('last_name')
            ->get()
            ->mapWithKeys(fn (User $user) => [$user->id => $user->display_name]);
    }

    protected function getViewData(): array
    {
        $options = $this->doctorOptions();

        if ($this->doctorId === null && $options->isNotEmpty()) {
            $this->doctorId = (int) $options->keys()->first();
        }

        return [
            'doctorOptions' => $options,
        ];
    }

    public function updatedDoctorId($value): void
    {
        $this->doctorId = ($value !== null && $value !== '') ? (int) $value : null;
    }
}
