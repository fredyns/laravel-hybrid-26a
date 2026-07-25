<?php

use App\Notifications\ResourceNotification;
use Livewire\Component;

new class extends Component
{
    public function simulate(): void
    {
        $titles = [
            'User registered',
            'Role updated',
            'Password changed',
            'Profile updated',
            'Export completed',
        ];

        $bodies = [
            'A new user has joined the platform.',
            'The admin role permissions were modified.',
            'A user changed their password successfully.',
            'User profile information was updated.',
            'Your data export is ready for download.',
        ];

        $variants = ['success', 'info', 'warning'];

        $index = array_rand($titles);

        auth()->user()->notify(new ResourceNotification(
            title: $titles[$index],
            body: $bodies[$index],
            url: route('notifications.index'),
            variant: $variants[array_rand($variants)],
        ));

        Flux::toast(variant: 'success', heading: __('Notification sent'), text: __('Check the bell icon or notifications page.'));
    }
}; ?>

<flux:button variant="primary" icon="bell-alert" wire:click="simulate">
    {{ __('Simulate notification') }}
</flux:button>
