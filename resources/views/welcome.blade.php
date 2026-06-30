<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} — IT Services</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased" x-data="{ mobileOpen: false }">

        {{-- ===== NAVBAR ===== --}}
        <nav class="fixed top-0 inset-x-0 z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-lg border-b border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl flex items-center justify-between px-6 lg:px-8 h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600">
                        <x-app-logo-icon class="size-5 fill-current text-white" />
                    </span>
                    <span class="text-lg font-bold tracking-tight">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                    <a href="#services" class="hover:text-blue-600 dark:hover:text-blue-400 transition">{{ __('Services') }}</a>
                    <a href="#features" class="hover:text-blue-600 dark:hover:text-blue-400 transition">{{ __('Why Us') }}</a>
                    <a href="#contact" class="hover:text-blue-600 dark:hover:text-blue-400 transition">{{ __('Contact') }}</a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden md:inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:brightness-110 transition-all">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:inline-flex text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            {{ __('Log in') }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden md:inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:brightness-110 transition-all">
                                {{ __('Get started') }}
                            </a>
                        @endif
                    @endauth

                    <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" aria-label="Toggle menu">
                        <svg x-show="!mobileOpen" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileOpen" x-cloak class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div x-show="mobileOpen" x-collapse x-cloak class="md:hidden border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                <div class="px-6 py-4 space-y-3 text-sm font-medium">
                    <a href="#services" @click="mobileOpen = false" class="block text-zinc-600 dark:text-zinc-400">{{ __('Services') }}</a>
                    <a href="#features" @click="mobileOpen = false" class="block text-zinc-600 dark:text-zinc-400">{{ __('Why Us') }}</a>
                    <a href="#contact" @click="mobileOpen = false" class="block text-zinc-600 dark:text-zinc-400">{{ __('Contact') }}</a>
                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex flex-col gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="text-center text-zinc-600 dark:text-zinc-400 py-2">{{ __('Log in') }}</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white">{{ __('Get started') }}</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{-- ===== HERO ===== --}}
        <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
            {{-- Colorful background blobs --}}
            <div class="absolute inset-0 -z-10 overflow-hidden">
                <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-gradient-to-br from-blue-400/30 to-cyan-300/20 dark:from-blue-600/15 dark:to-cyan-500/10 rounded-full blur-3xl"></div>
                <div class="absolute top-20 right-0 w-[400px] h-[400px] bg-gradient-to-bl from-violet-400/25 to-fuchsia-300/15 dark:from-violet-600/10 dark:to-fuchsia-500/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 left-1/3 w-[350px] h-[350px] bg-gradient-to-tr from-amber-300/20 to-orange-300/15 dark:from-amber-500/10 dark:to-orange-500/5 rounded-full blur-3xl"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 px-4 py-1.5 text-sm font-semibold text-blue-700 dark:text-blue-300 ring-1 ring-blue-200/60 dark:ring-blue-700/40 mb-8">
                            <span class="relative flex size-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
                            </span>
                            {{ __('Trusted by 200+ businesses') }}
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.08]">
                            {{ __('Modern IT') }}
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 dark:from-blue-400 dark:via-violet-400 dark:to-fuchsia-400">
                                {{ __('solutions') }}
                            </span>
                            <br>
                            {{ __('for growing') }}
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-cyan-500 dark:from-emerald-400 dark:to-cyan-400">
                                {{ __('businesses') }}
                            </span>
                        </h1>

                        <p class="mt-6 text-lg text-zinc-600 dark:text-zinc-400 max-w-xl leading-relaxed">
                            {{ __('We design, build, and manage the technology infrastructure that powers your business. From cloud architecture to cybersecurity, we have you covered.') }}
                        </p>

                        <div class="mt-10 flex flex-col sm:flex-row items-start gap-4">
                            <a href="#contact" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:brightness-110 transition-all">
                                {{ __('Request a consultation') }}
                                <svg class="ml-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="#services" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-8 py-3.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300 ring-1 ring-zinc-300 dark:ring-zinc-700 hover:ring-blue-400 dark:hover:ring-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all">
                                {{ __('Our services') }}
                            </a>
                        </div>
                    </div>

                    {{-- Hero image --}}
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-tr from-blue-500/20 via-violet-500/20 to-fuchsia-500/20 dark:from-blue-500/10 dark:via-violet-500/10 dark:to-fuchsia-500/10 rounded-3xl blur-2xl"></div>
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-blue-500/20 dark:shadow-blue-500/10 ring-1 ring-zinc-200 dark:ring-zinc-800">
                            <img
                                src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop&q=80"
                                alt="{{ __('Team working on IT solutions') }}"
                                class="w-full h-auto aspect-[4/3] object-cover"
                                loading="eager"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/20 to-transparent"></div>
                        </div>

                        {{-- Floating badge --}}
                        <div class="absolute -bottom-4 -left-4 sm:-left-6 bg-white dark:bg-zinc-800 rounded-xl shadow-lg shadow-zinc-200/50 dark:shadow-zinc-950/50 p-4 ring-1 ring-zinc-100 dark:ring-zinc-700">
                            <div class="flex items-center gap-3">
                                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                                    <svg class="size-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Uptime') }}</div>
                                    <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">99.9%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== STATS ===== --}}
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djItSDI0di0yaDEyem0wLTI4djJIMjRWNmgxMnoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-30"></div>

            <div class="relative mx-auto max-w-7xl px-6 lg:px-8 py-14">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                    @foreach ([
                        ['99.9%', __('Uptime SLA'), 'bg-white/20'],
                        ['200+', __('Clients served'), 'bg-white/20'],
                        ['24/7', __('Support coverage'), 'bg-white/20'],
                        ['15+', __('Years experience'), 'bg-white/20'],
                    ] as [$value, $label, $bg])
                        <div class="text-center">
                            <div class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight">{{ $value }}</div>
                            <div class="mt-2 text-sm font-medium text-blue-100">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== SERVICES ===== --}}
        <section id="services" class="py-20 lg:py-28 scroll-mt-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center mb-16">
                    <p class="text-sm font-bold uppercase tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-violet-600 dark:from-blue-400 dark:to-violet-400">{{ __('What we do') }}</p>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight">{{ __('Comprehensive IT services') }}</h2>
                    <p class="mt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('End-to-end technology solutions tailored to your business needs, from strategy to execution.') }}</p>
                </div>

                @php
                    $services = [
                        [
                            'icon' => 'cloud',
                            'title' => __('Cloud Solutions'),
                            'desc' => __('Scalable cloud architecture on AWS, Azure, and GCP. Migration, optimization, and managed cloud services.'),
                            'gradient' => 'from-sky-500 to-blue-600',
                            'bg' => 'bg-sky-50 dark:bg-sky-950/40',
                            'ring' => 'ring-sky-200/50 dark:ring-sky-800/50',
                            'text' => 'text-sky-600 dark:text-sky-400',
                            'img' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=400&h=240&fit=crop&q=80',
                        ],
                        [
                            'icon' => 'shield',
                            'title' => __('Cybersecurity'),
                            'desc' => __('Protect your assets with threat monitoring, penetration testing, compliance audits, and incident response.'),
                            'gradient' => 'from-red-500 to-rose-600',
                            'bg' => 'bg-rose-50 dark:bg-rose-950/40',
                            'ring' => 'ring-rose-200/50 dark:ring-rose-800/50',
                            'text' => 'text-rose-600 dark:text-rose-400',
                            'img' => 'https://images.unsplash.com/photo-1563986768609-322da13575f2?w=400&h=240&fit=crop&q=80',
                        ],
                        [
                            'icon' => 'code',
                            'title' => __('Software Development'),
                            'desc' => __('Custom web and mobile applications built with modern frameworks, APIs, and agile methodology.'),
                            'gradient' => 'from-violet-500 to-purple-600',
                            'bg' => 'bg-violet-50 dark:bg-violet-950/40',
                            'ring' => 'ring-violet-200/50 dark:ring-violet-800/50',
                            'text' => 'text-violet-600 dark:text-violet-400',
                            'img' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&h=240&fit=crop&q=80',
                        ],
                        [
                            'icon' => 'server',
                            'title' => __('Infrastructure'),
                            'desc' => __('Network design, server management, virtualization, and disaster recovery planning.'),
                            'gradient' => 'from-emerald-500 to-teal-600',
                            'bg' => 'bg-emerald-50 dark:bg-emerald-950/40',
                            'ring' => 'ring-emerald-200/50 dark:ring-emerald-800/50',
                            'text' => 'text-emerald-600 dark:text-emerald-400',
                            'img' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=400&h=240&fit=crop&q=80',
                        ],
                        [
                            'icon' => 'wrench',
                            'title' => __('Managed IT'),
                            'desc' => __('Proactive monitoring, helpdesk support, patch management, and hardware lifecycle management.'),
                            'gradient' => 'from-amber-500 to-orange-600',
                            'bg' => 'bg-amber-50 dark:bg-amber-950/40',
                            'ring' => 'ring-amber-200/50 dark:ring-amber-800/50',
                            'text' => 'text-amber-600 dark:text-amber-400',
                            'img' => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=400&h=240&fit=crop&q=80',
                        ],
                        [
                            'icon' => 'chart',
                            'title' => __('Data & Analytics'),
                            'desc' => __('Business intelligence, data pipelines, real-time dashboards, and AI/ML integration.'),
                            'gradient' => 'from-fuchsia-500 to-pink-600',
                            'bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/40',
                            'ring' => 'ring-fuchsia-200/50 dark:ring-fuchsia-800/50',
                            'text' => 'text-fuchsia-600 dark:text-fuchsia-400',
                            'img' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=240&fit=crop&q=80',
                        ],
                    ];
                @endphp

                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <div class="group rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden hover:shadow-xl hover:shadow-zinc-200/50 dark:hover:shadow-zinc-900/50 hover:-translate-y-1 transition-all duration-300">
                            <div class="relative h-40 overflow-hidden">
                                <img
                                    src="{{ $service['img'] }}"
                                    alt="{{ $service['title'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t {{ $service['gradient'] }} opacity-60 mix-blend-multiply dark:mix-blend-normal dark:opacity-70"></div>
                                <div class="absolute top-4 left-4">
                                    <div class="flex size-10 items-center justify-center rounded-lg bg-white/90 dark:bg-zinc-900/80 backdrop-blur-sm shadow">
                                        @if ($service['icon'] === 'cloud')
                                            <svg class="size-5 {{ $service['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 3.752 3.752 0 013.287 5.845A3.75 3.75 0 0118 19.5H6.75z" /></svg>
                                        @elseif ($service['icon'] === 'shield')
                                            <svg class="size-5 {{ $service['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                        @elseif ($service['icon'] === 'code')
                                            <svg class="size-5 {{ $service['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" /></svg>
                                        @elseif ($service['icon'] === 'server')
                                            <svg class="size-5 {{ $service['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" /></svg>
                                        @elseif ($service['icon'] === 'wrench')
                                            <svg class="size-5 {{ $service['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.645 5.645a1.575 1.575 0 11-2.228-2.228l5.645-5.645m2.228 2.228L16.5 7.5m-5.08 7.67L7.5 16.5m4.92-4.92L16.5 7.5m0 0l3-3m-3 3l-3 3m3-3l3 3" /></svg>
                                        @elseif ($service['icon'] === 'chart')
                                            <svg class="size-5 {{ $service['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <h3 class="text-lg font-bold mb-2">{{ $service['title'] }}</h3>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $service['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== WHY US ===== --}}
        <section id="features" class="py-20 lg:py-28 scroll-mt-20 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-zinc-50 via-blue-50/30 to-violet-50/30 dark:from-zinc-900/50 dark:via-blue-950/20 dark:to-violet-950/20"></div>

            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400">{{ __('Why choose us') }}</p>
                        <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight">{{ __('Technology partner you can rely on') }}</h2>
                        <p class="mt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            {{ __('We go beyond fixing problems. We build strategic technology roadmaps that align with your business goals and scale with your growth.') }}
                        </p>

                        <div class="mt-10 space-y-6">
                            @php
                                $features = [
                                    ['color' => 'emerald', 'title' => __('Proactive, not reactive'), 'desc' => __('We monitor and resolve issues before they impact your business, keeping your systems running smoothly around the clock.')],
                                    ['color' => 'blue', 'title' => __('Industry-certified team'), 'desc' => __('Our engineers hold certifications across AWS, Azure, Cisco, and CompTIA, bringing deep expertise to every engagement.')],
                                    ['color' => 'violet', 'title' => __('Transparent pricing'), 'desc' => __('No hidden fees or surprise charges. Fixed monthly plans with clear scope so you can budget with confidence.')],
                                ];
                            @endphp

                            @foreach ($features as $feature)
                                <div class="flex gap-4">
                                    <div class="shrink-0 mt-1">
                                        @if ($feature['color'] === 'emerald')
                                            <div class="flex size-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                                                <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            </div>
                                        @elseif ($feature['color'] === 'blue')
                                            <div class="flex size-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30">
                                                <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            </div>
                                        @else
                                            <div class="flex size-8 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/30">
                                                <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-semibold">{{ $feature['title'] }}</h3>
                                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $feature['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Dashboard card with image --}}
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-br from-emerald-500/15 via-blue-500/15 to-violet-500/15 dark:from-emerald-500/5 dark:via-blue-500/5 dark:to-violet-500/5 rounded-3xl blur-2xl"></div>

                        <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-zinc-300/50 dark:shadow-zinc-950/50 ring-1 ring-zinc-200 dark:ring-zinc-800">
                            <img
                                src="https://images.unsplash.com/photo-1504384764586-bb4cdc1707b0?w=800&h=500&fit=crop&q=80"
                                alt="{{ __('IT monitoring dashboard') }}"
                                class="w-full aspect-[4/3] object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 via-zinc-900/30 to-transparent"></div>

                            {{-- Overlay metrics --}}
                            <div class="absolute bottom-0 inset-x-0 p-6">
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach ([
                                        ['bg-emerald-500/90', __('System Health'), '100%'],
                                        ['bg-blue-500/90', __('Response Time'), '< 15min'],
                                        ['bg-violet-500/90', __('Resolved'), '847'],
                                        ['bg-amber-500/90', __('Satisfaction'), '4.9/5'],
                                    ] as [$bg, $label, $value])
                                        <div class="rounded-xl {{ $bg }} backdrop-blur-sm p-3 text-white">
                                            <div class="text-[11px] font-medium opacity-80">{{ $label }}</div>
                                            <div class="text-lg font-bold">{{ $value }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== CONTACT CTA ===== --}}
        <section id="contact" class="py-20 lg:py-28 scroll-mt-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="relative rounded-3xl overflow-hidden">
                    {{-- Background image with overlay --}}
                    <div class="absolute inset-0">
                        <img
                            src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1400&h=600&fit=crop&q=80"
                            alt=""
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 via-indigo-900/90 to-violet-900/95"></div>
                    </div>

                    {{-- Decorative blobs --}}
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-400/15 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 w-72 h-72 bg-fuchsia-400/15 rounded-full blur-3xl"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-amber-400/10 rounded-full blur-3xl"></div>
                    </div>

                    <div class="relative px-10 sm:px-16 lg:px-20 py-16 lg:py-24">
                        <div class="mx-auto max-w-2xl text-center">
                            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
                                {{ __('Ready to transform') }}
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 via-blue-300 to-violet-300">{{ __('your IT?') }}</span>
                            </h2>
                            <p class="mt-5 text-lg text-blue-100/80 leading-relaxed">
                                {{ __('Schedule a free consultation with our team. We\'ll assess your current infrastructure and create a tailored roadmap for your business.') }}
                            </p>

                            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                                <a href="mailto:{{ config('mail.from.address', 'hello@example.com') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-indigo-900 shadow-lg hover:bg-blue-50 transition">
                                    <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                    {{ __('Email us') }}
                                </a>
                                <a href="tel:+1234567890" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-8 py-3.5 text-sm font-semibold text-white ring-1 ring-white/30 hover:bg-white/10 transition">
                                    <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                    {{ __('Call us') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== FOOTER ===== --}}
        <footer class="border-t border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-2.5">
                        <span class="flex size-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600">
                            <x-app-logo-icon class="size-4 fill-current text-white" />
                        </span>
                        <span class="text-sm font-bold">{{ config('app.name', 'Laravel') }}</span>
                    </div>

                    <div class="flex items-center gap-6 text-sm text-zinc-500 dark:text-zinc-400">
                        <a href="#services" class="hover:text-blue-600 dark:hover:text-blue-400 transition">{{ __('Services') }}</a>
                        <a href="#features" class="hover:text-blue-600 dark:hover:text-blue-400 transition">{{ __('Why Us') }}</a>
                        <a href="#contact" class="hover:text-blue-600 dark:hover:text-blue-400 transition">{{ __('Contact') }}</a>
                    </div>

                    <p class="text-sm text-zinc-400 dark:text-zinc-500">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}
                    </p>
                </div>
            </div>
        </footer>

    </body>
</html>
