<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Appearance settings')]
class Appearance extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.appearance');
    }
}
