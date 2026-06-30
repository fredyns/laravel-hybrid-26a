<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-950 antialiased">
        <div class="relative grid h-dvh flex-col items-center justify-center lg:max-w-none lg:grid-cols-2 lg:px-0">

            {{-- ===== LEFT PANEL — branding ===== --}}
            <div class="relative hidden h-full flex-col overflow-hidden lg:flex">
                {{-- Background image + gradient overlay --}}
                <div class="absolute inset-0">
                    <img
                        src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1000&h=1200&fit=crop&q=80"
                        alt=""
                        class="h-full w-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/95 via-indigo-900/90 to-violet-900/90"></div>
                </div>

                {{-- Decorative blobs --}}
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-80 h-80 bg-violet-400/20 rounded-full blur-3xl"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-cyan-400/10 rounded-full blur-3xl"></div>
                </div>

                {{-- Content --}}
                <div class="relative z-10 flex h-full flex-col p-10">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                        <span class="flex size-10 items-center justify-center rounded-xl bg-white/10 backdrop-blur-sm ring-1 ring-white/20">
                            <x-app-logo-icon class="size-6 fill-current text-white" />
                        </span>
                        <span class="text-lg font-bold text-white">{{ config('app.name', 'Laravel') }}</span>
                    </a>

                    {{-- Stats --}}
                    <div class="mt-16 grid grid-cols-2 gap-4">
                        @foreach ([
                            ['99.9%', __('Uptime SLA'), 'from-blue-400/20 to-blue-500/10'],
                            ['200+', __('Clients served'), 'from-violet-400/20 to-violet-500/10'],
                            ['24/7', __('Support'), 'from-cyan-400/20 to-cyan-500/10'],
                            ['15+', __('Years exp.'), 'from-fuchsia-400/20 to-fuchsia-500/10'],
                        ] as [$val, $lbl, $grad])
                            <div class="rounded-xl bg-gradient-to-br {{ $grad }} backdrop-blur-sm ring-1 ring-white/10 p-4">
                                <div class="text-2xl font-extrabold text-white">{{ $val }}</div>
                                <div class="text-xs font-medium text-blue-200">{{ $lbl }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Feature list --}}
                    <div class="mt-10 space-y-4">
                        @foreach ([
                            ['from-emerald-500 to-teal-500', __('Proactive monitoring & 24/7 support')],
                            ['from-blue-500 to-indigo-500', __('Industry-certified engineers')],
                            ['from-violet-500 to-purple-500', __('Transparent, fixed-price plans')],
                        ] as [$grad, $text])
                            <div class="flex items-center gap-3">
                                <span class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $grad }} shadow-lg">
                                    <svg class="size-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                </span>
                                <span class="text-sm font-medium text-blue-100">{{ $text }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Quote --}}
                    @php
                        [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                    @endphp
                    <div class="mt-auto rounded-2xl bg-white/5 backdrop-blur-sm ring-1 ring-white/10 p-6">
                        <svg class="mb-3 size-6 text-blue-300 opacity-60" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        <p class="text-sm italic leading-relaxed text-blue-100/80">&ldquo;{{ trim($message) }}&rdquo;</p>
                        <p class="mt-3 text-xs font-semibold text-blue-300">— {{ trim($author) }}</p>
                    </div>
                </div>
            </div>

            {{-- ===== RIGHT PANEL — form ===== --}}
            <div class="flex items-center justify-center w-full px-8 py-12 sm:px-12 lg:px-16">
                <div class="w-full max-w-sm">
                    {{-- Mobile logo --}}
                    <a href="{{ route('home') }}" class="mb-8 flex flex-col items-center gap-3 lg:hidden" wire:navigate>
                        <span class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/30">
                            <x-app-logo-icon class="size-7 fill-current text-white" />
                        </span>
                        <span class="text-xl font-bold">{{ config('app.name', 'Laravel') }}</span>
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
