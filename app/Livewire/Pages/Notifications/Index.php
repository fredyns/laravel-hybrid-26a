<?php

namespace App\Livewire\Pages\Notifications;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Notifications')]
class Index extends Component
{
    use WithPagination;

    public int $perPage = 15;

    #[Computed]
    public function notifications(): LengthAwarePaginator
    {
        return auth()->user()->notifications()->latest()->paginate($this->perPage);
    }

    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        unset($this->notifications, $this->unreadCount);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        unset($this->notifications, $this->unreadCount);
    }

    public function deleteNotification(string $notificationId): void
    {
        auth()->user()->notifications()->where('id', $notificationId)->delete();

        unset($this->notifications, $this->unreadCount);
    }

    #[On('notification-received')]
    public function refresh(): void
    {
        unset($this->notifications, $this->unreadCount);
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function variantColor(string $variant): string
    {
        return match ($variant) {
            'success' => 'green',
            'warning' => 'amber',
            'danger' => 'red',
            'info' => 'blue',
            default => 'zinc',
        };
    }

    public function render()
    {
        return view('livewire.pages.notifications.index');
    }
}
