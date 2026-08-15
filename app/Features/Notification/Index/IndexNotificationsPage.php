<?php

namespace App\Features\Notification\Index;

use App\Features\Notification\Delete\DeleteNotification;
use App\Features\Notification\MarkAllRead\MarkAllNotificationsRead;
use App\Features\Notification\MarkRead\MarkNotificationRead;
use App\Features\Notification\Simulate\SimulateNotification;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Notifications')]
class IndexNotificationsPage extends Component
{
    use WithPagination;

    #[Url(as: 'per_page')]
    public int $perPage = 15;

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * The id comes from the browser; the action scopes it to the owner,
     * so another user's id resolves to 404 rather than being touched.
     */
    public function markAsRead(string $notificationId, MarkNotificationRead $action): void
    {
        $action->execute($this->user(), $notificationId);

        $this->dispatch('notification-received');
    }

    public function markAllRead(MarkAllNotificationsRead $action): void
    {
        $action->execute($this->user());

        $this->dispatch('notification-received');
    }

    public function delete(string $notificationId, DeleteNotification $action): void
    {
        $action->execute($this->user(), $notificationId);

        $this->dispatch('notification-received');
    }

    /**
     * Demo button: create a notification without waiting for a real event.
     */
    public function simulate(SimulateNotification $action): void
    {
        $action->execute($this->user());

        $this->resetPage();

        Flux::toast(variant: 'success', text: __('Simulated notification created.'));
    }

    private function user(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(401);
        }

        return $user;
    }

    public function render(): View
    {
        $user = $this->user();

        return view('features::Notification.Index.index-notifications-page', [
            'notifications' => $user->notifications()->latest()->paginate(max(1, min(100, $this->perPage))),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }
}
