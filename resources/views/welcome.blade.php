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
                    <span class="flex size-8 items-center justify-center rounded-lg bg-zinc-900 dark:bg-white">
                        <x-app-logo-icon class="size-5 fill-current text-white dark:text-zinc-900" />
                    </span>
                    <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                    <a href="#services" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('Services') }}</a>
                    <a href="#features" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('Why Us') }}</a>
                    <a href="#contact" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('Contact') }}</a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden md:inline-flex items-center gap-1.5 rounded-lg bg-zinc-900 dark:bg-white px-4 py-2 text-sm font-medium text-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:inline-flex text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                            {{ __('Log in') }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden md:inline-flex items-center rounded-lg bg-zinc-900 dark:bg-white px-4 py-2 text-sm font-medium text-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition">
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

            {{-- Mobile menu --}}
            <div x-show="mobileOpen" x-collapse x-cloak class="md:hidden border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                <div class="px-6 py-4 space-y-3 text-sm font-medium">
                    <a href="#services" @click="mobileOpen = false" class="block text-zinc-600 dark:text-zinc-400">{{ __('Services') }}</a>
                    <a href="#features" @click="mobileOpen = false" class="block text-zinc-600 dark:text-zinc-400">{{ __('Why Us') }}</a>
                    <a href="#contact" @click="mobileOpen = false" class="block text-zinc-600 dark:text-zinc-400">{{ __('Contact') }}</a>
                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex flex-col gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-zinc-900 dark:bg-white px-4 py-2 text-center text-sm font-medium text-white dark:text-zinc-900">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="text-center text-zinc-600 dark:text-zinc-400 py-2">{{ __('Log in') }}</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg bg-zinc-900 dark:bg-white px-4 py-2 text-center text-sm font-medium text-white dark:text-zinc-900">{{ __('Get started') }}</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{-- ===== HERO ===== --}}
        <section class="relative pt-32 pb-20 lg:pt-44 lg:pb-32 overflow-hidden">
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-gradient-to-br from-blue-500/10 via-indigo-500/10 to-violet-500/10 dark:from-blue-500/5 dark:via-indigo-500/5 dark:to-violet-500/5 rounded-full blur-3xl"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 dark:bg-blue-950/50 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-300 ring-1 ring-blue-200/60 dark:ring-blue-800/60 mb-8">
                        <span class="relative flex size-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-blue-500"></span>
                        </span>
                        {{ __('Trusted by 200+ businesses') }}
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.1]">
                        {{ __('Modern IT solutions') }}
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 dark:from-blue-400 dark:via-indigo-400 dark:to-violet-400">
                            {{ __('for growing businesses') }}
                        </span>
                    </h1>

                    <p class="mt-6 text-lg lg:text-xl text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                        {{ __('We design, build, and manage the technology infrastructure that powers your business. From cloud architecture to cybersecurity, we have you covered.') }}
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="#contact" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-zinc-900 dark:bg-white px-8 py-3.5 text-sm font-semibold text-white dark:text-zinc-900 shadow-lg shadow-zinc-900/20 dark:shadow-white/10 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition">
                            {{ __('Request a consultation') }}
                            <svg class="ml-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="#services" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-8 py-3.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300 ring-1 ring-zinc-300 dark:ring-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">
                            {{ __('Our services') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== STATS ===== --}}
        <section class="border-y border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach ([
                        ['99.9%', __('Uptime SLA')],
                        ['200+', __('Clients served')],
                        ['24/7', __('Support coverage')],
                        ['15+', __('Years experience')],
                    ] as [$value, $label])
                        <div class="text-center">
                            <div class="text-3xl lg:text-4xl font-bold tracking-tight">{{ $value }}</div>
                            <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== SERVICES ===== --}}
        <section id="services" class="py-20 lg:py-28 scroll-mt-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center mb-16">
                    <p class="text-sm font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400">{{ __('What we do') }}</p>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight">{{ __('Comprehensive IT services') }}</h2>
                    <p class="mt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('End-to-end technology solutions tailored to your business needs, from strategy to execution.') }}</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @php
                        $services = [
                            ['icon' => 'cloud', 'title' => __('Cloud Solutions'), 'desc' => __('Scalable cloud architecture on AWS, Azure, and GCP. Migration, optimization, and managed cloud services.')],
                            ['icon' => 'shield', 'title' => __('Cybersecurity'), 'desc' => __('Protect your assets with threat monitoring, penetration testing, compliance audits, and incident response.')],
                            ['icon' => 'code', 'title' => __('Software Development'), 'desc' => __('Custom web and mobile applications built with modern frameworks, APIs, and agile methodology.')],
                            ['icon' => 'server', 'title' => __('Infrastructure'), 'desc' => __('Network design, server management, virtualization, and disaster recovery planning.')],
                            ['icon' => 'wrench', 'title' => __('Managed IT'), 'desc' => __('Proactive monitoring, helpdesk support, patch management, and hardware lifecycle management.')],
                            ['icon' => 'chart', 'title' => __('Data & Analytics'), 'desc' => __('Business intelligence, data pipelines, real-time dashboards, and AI/ML integration.')],
                        ];
                    @endphp

                    @foreach ($services as $service)
                        <div class="group relative rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 hover:border-zinc-300 dark:hover:border-zinc-700 hover:shadow-lg hover:shadow-zinc-200/50 dark:hover:shadow-zinc-900/50 transition-all duration-300">
                            <div class="flex size-12 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 ring-1 ring-blue-200/50 dark:ring-blue-800/50 mb-5">
                                @if ($service['icon'] === 'cloud')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 3.752 3.752 0 013.287 5.845A3.75 3.75 0 0118 19.5H6.75z" /></svg>
                                @elseif ($service['icon'] === 'shield')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                @elseif ($service['icon'] === 'code')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" /></svg>
                                @elseif ($service['icon'] === 'server')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" /></svg>
                                @elseif ($service['icon'] === 'wrench')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.645 5.645a1.575 1.575 0 11-2.228-2.228l5.645-5.645m2.228 2.228L16.5 7.5m-5.08 7.67L7.5 16.5m4.92-4.92L16.5 7.5m0 0l3-3m-3 3l-3 3m3-3l3 3" /></svg>
                                @elseif ($service['icon'] === 'chart')
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                                @endif
                            </div>

                            <h3 class="text-lg font-semibold mb-2">{{ $service['title'] }}</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $service['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== WHY US ===== --}}
        <section id="features" class="py-20 lg:py-28 bg-zinc-50 dark:bg-zinc-900/50 border-y border-zinc-200 dark:border-zinc-800 scroll-mt-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400">{{ __('Why choose us') }}</p>
                        <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight">{{ __('Technology partner you can rely on') }}</h2>
                        <p class="mt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            {{ __('We go beyond fixing problems. We build strategic technology roadmaps that align with your business goals and scale with your growth.') }}
                        </p>

                        <div class="mt-10 space-y-6">
                            @foreach ([
                                [__('Proactive, not reactive'), __('We monitor and resolve issues before they impact your business, keeping your systems running smoothly around the clock.')],
                                [__('Industry-certified team'), __('Our engineers hold certifications across AWS, Azure, Cisco, and CompTIA, bringing deep expertise to every engagement.')],
                                [__('Transparent pricing'), __('No hidden fees or surprise charges. Fixed monthly plans with clear scope so you can budget with confidence.')],
                            ] as [$featureTitle, $featureDescription])
                                <div class="flex gap-4">
                                    <div class="shrink-0 mt-1">
                                        <div class="flex size-6 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/50">
                                            <svg class="size-3.5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold">{{ $featureTitle }}</h3>
                                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $featureDescription }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative">
                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 shadow-xl shadow-zinc-200/50 dark:shadow-zinc-950/50">
                            <div class="space-y-4">
                                @php
                                    $metrics = [
                                        ['bg' => 'bg-green-50 dark:bg-green-950/30', 'text' => 'text-green-700 dark:text-green-400', 'title' => __('System Health'), 'label' => __('All systems operational'), 'value' => '100%'],
                                        ['bg' => 'bg-blue-50 dark:bg-blue-950/30', 'text' => 'text-blue-700 dark:text-blue-400', 'title' => __('Response Time'), 'label' => __('Average first response'), 'value' => '< 15 min'],
                                        ['bg' => 'bg-violet-50 dark:bg-violet-950/30', 'text' => 'text-violet-700 dark:text-violet-400', 'title' => __('Tickets Resolved'), 'label' => __('Last 30 days'), 'value' => '847'],
                                        ['bg' => 'bg-amber-50 dark:bg-amber-950/30', 'text' => 'text-amber-700 dark:text-amber-400', 'title' => __('Client Satisfaction'), 'label' => __('Based on survey results'), 'value' => '4.9/5'],
                                    ];
                                @endphp

                                @foreach ($metrics as $metric)
                                    <div class="flex items-center justify-between p-4 rounded-xl {{ $metric['bg'] }}">
                                        <div class="{{ $metric['text'] }}">
                                            <div class="text-xs font-medium opacity-80">{{ $metric['label'] }}</div>
                                            <div class="font-semibold">{{ $metric['title'] }}</div>
                                        </div>
                                        <div class="text-2xl font-bold {{ $metric['text'] }}">{{ $metric['value'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="absolute -z-10 -top-4 -right-4 w-full h-full rounded-2xl bg-gradient-to-br from-blue-100 to-violet-100 dark:from-blue-950/30 dark:to-violet-950/30"></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== CONTACT CTA ===== --}}
        <section id="contact" class="py-20 lg:py-28 scroll-mt-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="relative rounded-3xl bg-zinc-900 dark:bg-white p-10 sm:p-16 lg:p-20 overflow-hidden">
                    <div class="absolute inset-0 -z-10">
                        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 w-72 h-72 bg-violet-500/20 rounded-full blur-3xl"></div>
                    </div>

                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white dark:text-zinc-900">
                            {{ __('Ready to transform your IT?') }}
                        </h2>
                        <p class="mt-4 text-lg text-zinc-400 dark:text-zinc-600 leading-relaxed">
                            {{ __('Schedule a free consultation with our team. We\'ll assess your current infrastructure and create a tailored roadmap for your business.') }}
                        </p>

                        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="mailto:{{ config('mail.from.address', 'hello@example.com') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-white dark:bg-zinc-900 px-8 py-3.5 text-sm font-semibold text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                                <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                {{ __('Email us') }}
                            </a>
                            <a href="tel:+1234567890" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-8 py-3.5 text-sm font-semibold text-white dark:text-zinc-900 ring-1 ring-zinc-700 dark:ring-zinc-300 hover:ring-zinc-500 dark:hover:ring-zinc-500 transition">
                                <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                {{ __('Call us') }}
                            </a>
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
                        <span class="flex size-7 items-center justify-center rounded-lg bg-zinc-900 dark:bg-white">
                            <x-app-logo-icon class="size-4 fill-current text-white dark:text-zinc-900" />
                        </span>
                        <span class="text-sm font-semibold">{{ config('app.name', 'Laravel') }}</span>
                    </div>

                    <div class="flex items-center gap-6 text-sm text-zinc-500 dark:text-zinc-400">
                        <a href="#services" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('Services') }}</a>
                        <a href="#features" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('Why Us') }}</a>
                        <a href="#contact" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('Contact') }}</a>
                    </div>

                    <p class="text-sm text-zinc-400 dark:text-zinc-500">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}
                    </p>
                </div>
            </div>
        </footer>

    </body>
</html>
