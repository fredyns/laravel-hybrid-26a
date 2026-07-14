import { useState } from 'react';
import { Link } from '@tanstack/react-router';
import {
  ArrowRight,
  BarChart3,
  Check,
  Cloud,
  Code2,
  Mail,
  Menu,
  Phone,
  Server,
  Shield,
  Wrench,
  X,
} from 'lucide-react';
import { AppLogoIcon, APP_NAME } from '@/components/app-logo';
import { isAuthenticated } from '@/features/auth/useAuth';

const gradientButton =
  'inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:brightness-110 transition-all';

const services = [
  {
    icon: Cloud,
    title: 'Cloud Solutions',
    desc: 'Scalable cloud architecture on AWS, Azure, and GCP. Migration, optimization, and managed cloud services.',
    gradient: 'from-sky-500 to-blue-600',
    text: 'text-sky-600 dark:text-sky-400',
    img: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=400&h=240&fit=crop&q=80',
  },
  {
    icon: Shield,
    title: 'Cybersecurity',
    desc: 'Protect your assets with threat monitoring, penetration testing, compliance audits, and incident response.',
    gradient: 'from-red-500 to-rose-600',
    text: 'text-rose-600 dark:text-rose-400',
    img: 'https://images.unsplash.com/photo-1563986768609-322da13575f2?w=400&h=240&fit=crop&q=80',
  },
  {
    icon: Code2,
    title: 'Software Development',
    desc: 'Custom web and mobile applications built with modern frameworks, APIs, and agile methodology.',
    gradient: 'from-violet-500 to-purple-600',
    text: 'text-violet-600 dark:text-violet-400',
    img: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&h=240&fit=crop&q=80',
  },
  {
    icon: Server,
    title: 'Infrastructure',
    desc: 'Network design, server management, virtualization, and disaster recovery planning.',
    gradient: 'from-emerald-500 to-teal-600',
    text: 'text-emerald-600 dark:text-emerald-400',
    img: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=400&h=240&fit=crop&q=80',
  },
  {
    icon: Wrench,
    title: 'Managed IT',
    desc: 'Proactive monitoring, helpdesk support, patch management, and hardware lifecycle management.',
    gradient: 'from-amber-500 to-orange-600',
    text: 'text-amber-600 dark:text-amber-400',
    img: 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=400&h=240&fit=crop&q=80',
  },
  {
    icon: BarChart3,
    title: 'Data & Analytics',
    desc: 'Business intelligence, data pipelines, real-time dashboards, and AI/ML integration.',
    gradient: 'from-fuchsia-500 to-pink-600',
    text: 'text-fuchsia-600 dark:text-fuchsia-400',
    img: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=240&fit=crop&q=80',
  },
];

const features = [
  {
    gradient: 'from-emerald-500 to-teal-600 shadow-emerald-500/30',
    title: 'Proactive, not reactive',
    desc: 'We monitor and resolve issues before they impact your business, keeping your systems running smoothly around the clock.',
  },
  {
    gradient: 'from-blue-500 to-indigo-600 shadow-blue-500/30',
    title: 'Industry-certified team',
    desc: 'Our engineers hold certifications across AWS, Azure, Cisco, and CompTIA, bringing deep expertise to every engagement.',
  },
  {
    gradient: 'from-violet-500 to-purple-600 shadow-violet-500/30',
    title: 'Transparent pricing',
    desc: 'No hidden fees or surprise charges. Fixed monthly plans with clear scope so you can budget with confidence.',
  },
];

/** Ported from resources/views/welcome.blade.php. */
export function LandingPage() {
  const [mobileOpen, setMobileOpen] = useState(false);
  const authed = isAuthenticated();

  return (
    <div className="scroll-smooth bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
      {/* ===== NAVBAR ===== */}
      <nav className="fixed inset-x-0 top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur-lg dark:border-zinc-800 dark:bg-zinc-950/80">
        <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 lg:px-8">
          <Link to="/" className="flex items-center gap-2.5">
            <span className="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600">
              <AppLogoIcon className="size-5 fill-current text-white" />
            </span>
            <span className="text-lg font-bold tracking-tight">{APP_NAME}</span>
          </Link>

          <div className="hidden items-center gap-8 text-sm font-medium text-zinc-600 md:flex dark:text-zinc-400">
            <a href="#services" className="transition hover:text-blue-600 dark:hover:text-blue-400">Services</a>
            <a href="#features" className="transition hover:text-blue-600 dark:hover:text-blue-400">Why Us</a>
            <a href="#contact" className="transition hover:text-blue-600 dark:hover:text-blue-400">Contact</a>
          </div>

          <div className="flex items-center gap-3">
            {authed ? (
              <Link to="/dashboard" className={`hidden px-5 py-2.5 md:inline-flex ${gradientButton}`}>
                Dashboard
              </Link>
            ) : (
              <>
                <Link
                  to="/login"
                  className="hidden text-sm font-medium text-zinc-600 transition hover:text-blue-600 md:inline-flex dark:text-zinc-400 dark:hover:text-blue-400"
                >
                  Log in
                </Link>
                <Link to="/register" className={`hidden px-5 py-2.5 md:inline-flex ${gradientButton}`}>
                  Get started
                </Link>
              </>
            )}

            <button
              type="button"
              onClick={() => setMobileOpen((open) => !open)}
              className="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 md:hidden dark:hover:bg-zinc-800"
              aria-label="Toggle menu"
            >
              {mobileOpen ? <X className="size-5" /> : <Menu className="size-5" />}
            </button>
          </div>
        </div>

        {mobileOpen && (
          <div className="border-t border-zinc-200 bg-white md:hidden dark:border-zinc-800 dark:bg-zinc-950">
            <div className="space-y-3 px-6 py-4 text-sm font-medium">
              <a href="#services" onClick={() => setMobileOpen(false)} className="block text-zinc-600 dark:text-zinc-400">Services</a>
              <a href="#features" onClick={() => setMobileOpen(false)} className="block text-zinc-600 dark:text-zinc-400">Why Us</a>
              <a href="#contact" onClick={() => setMobileOpen(false)} className="block text-zinc-600 dark:text-zinc-400">Contact</a>
              <div className="flex flex-col gap-2 border-t border-zinc-200 pt-3 dark:border-zinc-800">
                {authed ? (
                  <Link to="/dashboard" className={`px-4 py-2.5 ${gradientButton}`}>Dashboard</Link>
                ) : (
                  <>
                    <Link to="/login" className="py-2 text-center text-zinc-600 dark:text-zinc-400">Log in</Link>
                    <Link to="/register" className={`px-4 py-2.5 ${gradientButton}`}>Get started</Link>
                  </>
                )}
              </div>
            </div>
          </div>
        )}
      </nav>

      {/* ===== HERO ===== */}
      <section className="relative overflow-hidden pt-32 pb-20 lg:pt-40 lg:pb-28">
        <div className="absolute inset-0 -z-10 overflow-hidden">
          <div className="absolute -top-32 -left-32 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-blue-400/30 to-cyan-300/20 blur-3xl dark:from-blue-600/15 dark:to-cyan-500/10" />
          <div className="absolute top-20 right-0 h-[400px] w-[400px] rounded-full bg-gradient-to-bl from-violet-400/25 to-fuchsia-300/15 blur-3xl dark:from-violet-600/10 dark:to-fuchsia-500/5" />
          <div className="absolute -bottom-20 left-1/3 h-[350px] w-[350px] rounded-full bg-gradient-to-tr from-amber-300/20 to-orange-300/15 blur-3xl dark:from-amber-500/10 dark:to-orange-500/5" />
        </div>

        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            <div>
              <div className="mb-8 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-1.5 text-sm font-semibold text-blue-700 ring-1 ring-blue-200/60 dark:from-blue-950/60 dark:to-indigo-950/60 dark:text-blue-300 dark:ring-blue-700/40">
                <span className="relative flex size-2">
                  <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75" />
                  <span className="relative inline-flex size-2 rounded-full bg-emerald-500" />
                </span>
                Trusted by 200+ businesses
              </div>

              <h1 className="text-4xl font-extrabold leading-[1.08] tracking-tight sm:text-5xl lg:text-6xl">
                Modern IT{' '}
                <span className="bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent dark:from-blue-400 dark:via-violet-400 dark:to-fuchsia-400">
                  solutions
                </span>
                <br />
                for growing{' '}
                <span className="bg-gradient-to-r from-emerald-500 to-cyan-500 bg-clip-text text-transparent dark:from-emerald-400 dark:to-cyan-400">
                  businesses
                </span>
              </h1>

              <p className="mt-6 max-w-xl text-lg leading-relaxed text-zinc-600 dark:text-zinc-400">
                We design, build, and manage the technology infrastructure that powers your business. From
                cloud architecture to cybersecurity, we have you covered.
              </p>

              <div className="mt-10 flex flex-col items-start gap-4 sm:flex-row">
                <a href="#contact" className={`w-full px-8 py-3.5 sm:w-auto ${gradientButton}`}>
                  Request a consultation
                  <ArrowRight className="ml-2 size-4" />
                </a>
                <a
                  href="#services"
                  className="inline-flex w-full items-center justify-center rounded-xl px-8 py-3.5 text-sm font-semibold text-zinc-700 ring-1 ring-zinc-300 transition-all hover:text-blue-600 hover:ring-blue-400 sm:w-auto dark:text-zinc-300 dark:ring-zinc-700 dark:hover:text-blue-400 dark:hover:ring-blue-500"
                >
                  Our services
                </a>
              </div>
            </div>

            <div className="relative">
              <div className="absolute -inset-4 rounded-3xl bg-gradient-to-tr from-blue-500/20 via-violet-500/20 to-fuchsia-500/20 blur-2xl dark:from-blue-500/10 dark:via-violet-500/10 dark:to-fuchsia-500/10" />
              <div className="relative overflow-hidden rounded-2xl shadow-2xl shadow-blue-500/20 ring-1 ring-zinc-200 dark:shadow-blue-500/10 dark:ring-zinc-800">
                <img
                  src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop&q=80"
                  alt="Team working on IT solutions"
                  className="aspect-[4/3] h-auto w-full object-cover"
                  loading="eager"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-zinc-900/20 to-transparent" />
              </div>

              <div className="absolute -bottom-4 -left-4 rounded-xl bg-white p-4 shadow-lg shadow-zinc-200/50 ring-1 ring-zinc-100 sm:-left-6 dark:bg-zinc-800 dark:shadow-zinc-950/50 dark:ring-zinc-700">
                <div className="flex items-center gap-3">
                  <div className="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                    <Check className="size-5 text-emerald-600 dark:text-emerald-400" />
                  </div>
                  <div>
                    <div className="text-xs text-zinc-500 dark:text-zinc-400">Uptime</div>
                    <div className="text-lg font-bold text-emerald-600 dark:text-emerald-400">99.9%</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ===== STATS ===== */}
      <section className="relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600" />
        <div className="relative mx-auto max-w-7xl px-6 py-14 lg:px-8">
          <div className="grid grid-cols-2 gap-8 lg:grid-cols-4 lg:gap-12">
            {[
              ['99.9%', 'Uptime SLA'],
              ['200+', 'Clients served'],
              ['24/7', 'Support coverage'],
              ['15+', 'Years experience'],
            ].map(([value, label]) => (
              <div key={label} className="text-center">
                <div className="text-3xl font-extrabold tracking-tight text-white lg:text-5xl">{value}</div>
                <div className="mt-2 text-sm font-medium text-blue-100">{label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ===== SERVICES ===== */}
      <section id="services" className="scroll-mt-20 py-20 lg:py-28">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="mx-auto mb-16 max-w-2xl text-center">
            <p className="bg-gradient-to-r from-blue-600 to-violet-600 bg-clip-text text-sm font-bold uppercase tracking-widest text-transparent dark:from-blue-400 dark:to-violet-400">
              What we do
            </p>
            <h2 className="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Comprehensive IT services</h2>
            <p className="mt-4 leading-relaxed text-zinc-600 dark:text-zinc-400">
              End-to-end technology solutions tailored to your business needs, from strategy to execution.
            </p>
          </div>

          <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            {services.map((service) => (
              <div
                key={service.title}
                className="group overflow-hidden rounded-2xl border border-zinc-200 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-zinc-200/50 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:shadow-zinc-900/50"
              >
                <div className="relative h-40 overflow-hidden">
                  <img
                    src={service.img}
                    alt={service.title}
                    className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    loading="lazy"
                  />
                  <div
                    className={`absolute inset-0 bg-gradient-to-t ${service.gradient} opacity-60 mix-blend-multiply dark:opacity-70 dark:mix-blend-normal`}
                  />
                  <div className="absolute top-4 left-4">
                    <div className="flex size-10 items-center justify-center rounded-lg bg-white/90 shadow backdrop-blur-sm dark:bg-zinc-900/80">
                      <service.icon className={`size-5 ${service.text}`} />
                    </div>
                  </div>
                </div>

                <div className="p-6">
                  <h3 className="mb-2 text-lg font-bold">{service.title}</h3>
                  <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{service.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ===== WHY US ===== */}
      <section id="features" className="relative scroll-mt-20 overflow-hidden py-20 lg:py-28">
        <div className="absolute inset-0 -z-10 bg-gradient-to-b from-zinc-50 via-blue-50/30 to-violet-50/30 dark:from-zinc-900/50 dark:via-blue-950/20 dark:to-violet-950/20" />

        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="grid items-center gap-16 lg:grid-cols-2">
            <div>
              <p className="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-sm font-bold uppercase tracking-widest text-transparent dark:from-emerald-400 dark:to-teal-400">
                Why choose us
              </p>
              <h2 className="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                Technology partner you can rely on
              </h2>
              <p className="mt-4 leading-relaxed text-zinc-600 dark:text-zinc-400">
                We go beyond fixing problems. We build strategic technology roadmaps that align with your
                business goals and scale with your growth.
              </p>

              <div className="mt-10 space-y-6">
                {features.map((feature) => (
                  <div key={feature.title} className="flex gap-4">
                    <div className="mt-1 shrink-0">
                      <div
                        className={`flex size-8 items-center justify-center rounded-lg bg-gradient-to-br ${feature.gradient} shadow-lg`}
                      >
                        <Check className="size-4 text-white" strokeWidth={2.5} />
                      </div>
                    </div>
                    <div>
                      <h3 className="font-semibold">{feature.title}</h3>
                      <p className="mt-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        {feature.desc}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="relative">
              <div className="absolute -inset-4 rounded-3xl bg-gradient-to-br from-emerald-500/15 via-blue-500/15 to-violet-500/15 blur-2xl dark:from-emerald-500/5 dark:via-blue-500/5 dark:to-violet-500/5" />

              <div className="relative overflow-hidden rounded-2xl shadow-2xl shadow-zinc-300/50 ring-1 ring-zinc-200 dark:shadow-zinc-950/50 dark:ring-zinc-800">
                <img
                  src="https://images.unsplash.com/photo-1504384764586-bb4cdc1707b0?w=800&h=500&fit=crop&q=80"
                  alt="IT monitoring dashboard"
                  className="aspect-[4/3] w-full object-cover"
                  loading="lazy"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-zinc-900/80 via-zinc-900/30 to-transparent" />

                <div className="absolute inset-x-0 bottom-0 p-6">
                  <div className="grid grid-cols-2 gap-3">
                    {[
                      ['bg-emerald-500/90', 'System Health', '100%'],
                      ['bg-blue-500/90', 'Response Time', '< 15min'],
                      ['bg-violet-500/90', 'Resolved', '847'],
                      ['bg-amber-500/90', 'Satisfaction', '4.9/5'],
                    ].map(([bg, label, value]) => (
                      <div key={label} className={`rounded-xl ${bg} p-3 text-white backdrop-blur-sm`}>
                        <div className="text-[11px] font-medium opacity-80">{label}</div>
                        <div className="text-lg font-bold">{value}</div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ===== CONTACT CTA ===== */}
      <section id="contact" className="scroll-mt-20 py-20 lg:py-28">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="relative overflow-hidden rounded-3xl">
            <div className="absolute inset-0">
              <img
                src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1400&h=600&fit=crop&q=80"
                alt=""
                className="h-full w-full object-cover"
                loading="lazy"
              />
              <div className="absolute inset-0 bg-gradient-to-r from-blue-900/95 via-indigo-900/90 to-violet-900/95" />
            </div>

            <div className="absolute inset-0 overflow-hidden">
              <div className="absolute top-0 right-0 h-96 w-96 rounded-full bg-cyan-400/15 blur-3xl" />
              <div className="absolute bottom-0 left-0 h-72 w-72 rounded-full bg-fuchsia-400/15 blur-3xl" />
              <div className="absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-amber-400/10 blur-3xl" />
            </div>

            <div className="relative px-10 py-16 sm:px-16 lg:px-20 lg:py-24">
              <div className="mx-auto max-w-2xl text-center">
                <h2 className="text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                  Ready to transform{' '}
                  <span className="bg-gradient-to-r from-cyan-300 via-blue-300 to-violet-300 bg-clip-text text-transparent">
                    your IT?
                  </span>
                </h2>
                <p className="mt-5 text-lg leading-relaxed text-blue-100/80">
                  Schedule a free consultation with our team. We'll assess your current infrastructure and
                  create a tailored roadmap for your business.
                </p>

                <div className="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                  <a
                    href="mailto:hello@example.com"
                    className="inline-flex w-full items-center justify-center rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-indigo-900 shadow-lg transition hover:bg-blue-50 sm:w-auto"
                  >
                    <Mail className="mr-2 size-4" />
                    Email us
                  </a>
                  <a
                    href="tel:+1234567890"
                    className="inline-flex w-full items-center justify-center rounded-xl px-8 py-3.5 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/10 sm:w-auto"
                  >
                    <Phone className="mr-2 size-4" />
                    Call us
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ===== FOOTER ===== */}
      <footer className="border-t border-zinc-200 dark:border-zinc-800">
        <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8">
          <div className="flex flex-col items-center justify-between gap-6 md:flex-row">
            <div className="flex items-center gap-2.5">
              <span className="flex size-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600">
                <AppLogoIcon className="size-4 fill-current text-white" />
              </span>
              <span className="text-sm font-bold">{APP_NAME}</span>
            </div>

            <div className="flex items-center gap-6 text-sm text-zinc-500 dark:text-zinc-400">
              <a href="#services" className="transition hover:text-blue-600 dark:hover:text-blue-400">Services</a>
              <a href="#features" className="transition hover:text-blue-600 dark:hover:text-blue-400">Why Us</a>
              <a href="#contact" className="transition hover:text-blue-600 dark:hover:text-blue-400">Contact</a>
            </div>

            <p className="text-sm text-zinc-400 dark:text-zinc-500">
              &copy; {new Date().getFullYear()} {APP_NAME}. All rights reserved.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}
