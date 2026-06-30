import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

const userId = document.querySelector('meta[name="user-id"]')?.content;

if (userId && window.Echo) {
    window.Echo.private(`App.Models.User.${userId}`)
        .notification((payload) => {
            window.Flux?.toast({
                heading: payload.title,
                text: payload.body,
                variant: payload.variant ?? 'success',
                duration: 6000,
            });

            window.Livewire?.dispatch('notification-received');
        });
}
