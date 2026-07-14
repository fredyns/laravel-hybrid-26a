import { toast } from 'sonner';
import { BellRing } from 'lucide-react';
import { PlaceholderPattern } from '@/components/placeholder-pattern';
import { Button } from '@/components/ui/button';

/** Ported from resources/views/dashboard.blade.php. */
export function DashboardPage() {
  return (
    <div className="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-semibold tracking-tight">Dashboard</h1>
        <Button onClick={() => toast.info('This is a simulated notification.')}>
          <BellRing /> Simulate notification
        </Button>
      </div>

      <div className="grid auto-rows-min gap-4 md:grid-cols-3">
        {[0, 1, 2].map((index) => (
          <div
            key={index}
            className="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700"
          >
            <PlaceholderPattern className="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
          </div>
        ))}
      </div>

      <div className="relative min-h-96 flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <PlaceholderPattern className="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
      </div>
    </div>
  );
}
