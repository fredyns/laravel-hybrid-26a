import { Link, Outlet } from '@tanstack/react-router';
import { Check, Quote } from 'lucide-react';
import { AppLogoIcon, APP_NAME } from '@/components/app-logo';

const stats = [
  ['99.9%', 'Uptime SLA', 'from-blue-400/20 to-blue-500/10'],
  ['200+', 'Clients served', 'from-violet-400/20 to-violet-500/10'],
  ['24/7', 'Support', 'from-cyan-400/20 to-cyan-500/10'],
  ['15+', 'Years exp.', 'from-fuchsia-400/20 to-fuchsia-500/10'],
];

const features = [
  ['from-emerald-500 to-teal-500', 'Proactive monitoring & 24/7 support'],
  ['from-blue-500 to-indigo-500', 'Industry-certified engineers'],
  ['from-violet-500 to-purple-500', 'Transparent, fixed-price plans'],
];

/** Split auth layout, ported from resources/views/layouts/auth/split.blade.php. */
export function AuthLayout() {
  return (
    <div className="grid min-h-dvh bg-white antialiased lg:grid-cols-2 dark:bg-zinc-950">
      {/* ===== LEFT PANEL — branding ===== */}
      <div className="relative hidden h-full flex-col overflow-hidden lg:flex">
        <div className="absolute inset-0">
          <img
            src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1000&h=1200&fit=crop&q=80"
            alt=""
            className="h-full w-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-br from-blue-900/95 via-indigo-900/90 to-violet-900/90" />
        </div>

        <div className="absolute inset-0 overflow-hidden">
          <div className="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-blue-400/20 blur-3xl" />
          <div className="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-violet-400/20 blur-3xl" />
          <div className="absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-cyan-400/10 blur-3xl" />
        </div>

        <div className="relative z-10 flex h-full flex-col p-10">
          <Link to="/" className="flex items-center gap-3">
            <span className="flex size-10 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20 backdrop-blur-sm">
              <AppLogoIcon className="size-6 fill-current text-white" />
            </span>
            <span className="text-lg font-bold text-white">{APP_NAME}</span>
          </Link>

          <div className="mt-16 grid grid-cols-2 gap-4">
            {stats.map(([value, label, gradient]) => (
              <div
                key={label}
                className={`rounded-xl bg-gradient-to-br ${gradient} p-4 ring-1 ring-white/10 backdrop-blur-sm`}
              >
                <div className="text-2xl font-extrabold text-white">{value}</div>
                <div className="text-xs font-medium text-blue-200">{label}</div>
              </div>
            ))}
          </div>

          <div className="mt-10 space-y-4">
            {features.map(([gradient, text]) => (
              <div key={text} className="flex items-center gap-3">
                <span
                  className={`flex size-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br ${gradient} shadow-lg`}
                >
                  <Check className="size-3.5 text-white" strokeWidth={2.5} />
                </span>
                <span className="text-sm font-medium text-blue-100">{text}</span>
              </div>
            ))}
          </div>

          <div className="mt-auto rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 backdrop-blur-sm">
            <Quote className="mb-3 size-6 text-blue-300 opacity-60" />
            <p className="text-sm italic leading-relaxed text-blue-100/80">
              &ldquo;Walk as if you are kissing the Earth with your feet.&rdquo;
            </p>
            <p className="mt-3 text-xs font-semibold text-blue-300">— Thich Nhat Hanh</p>
          </div>
        </div>
      </div>

      {/* ===== RIGHT PANEL — form ===== */}
      <div className="flex w-full items-center justify-center px-8 py-12 sm:px-12 lg:px-16">
        <div className="w-full max-w-sm">
          <Link to="/" className="mb-8 flex flex-col items-center gap-3 lg:hidden">
            <span className="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/30">
              <AppLogoIcon className="size-7 fill-current text-white" />
            </span>
            <span className="text-xl font-bold">{APP_NAME}</span>
          </Link>

          <Outlet />
        </div>
      </div>
    </div>
  );
}
