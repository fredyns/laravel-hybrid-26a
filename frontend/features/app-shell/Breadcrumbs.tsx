import { Fragment } from 'react';
import { Link, useLocation } from '@tanstack/react-router';
import { ChevronRight, Home } from 'lucide-react';
import { breadcrumbsFor } from '@/features/app-shell/nav';
import { cn } from '@/lib/utils';

export function Breadcrumbs({ className }: { className?: string }) {
  const { pathname } = useLocation();
  const crumbs = breadcrumbsFor(pathname);

  return (
    <nav aria-label="Breadcrumb" className={cn('flex items-center gap-2 text-sm', className)}>
      <Link to="/dashboard" className="text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200">
        <Home className="size-4" />
      </Link>
      {crumbs.map((crumb, index) => {
        const isLast = index === crumbs.length - 1;
        return (
          <Fragment key={crumb.label}>
            <ChevronRight className="size-3.5 text-zinc-300 dark:text-zinc-600" />
            {crumb.to && !isLast ? (
              <Link to={crumb.to} className="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">
                {crumb.label}
              </Link>
            ) : (
              <span className="font-medium text-zinc-800 dark:text-zinc-200">{crumb.label}</span>
            )}
          </Fragment>
        );
      })}
    </nav>
  );
}
