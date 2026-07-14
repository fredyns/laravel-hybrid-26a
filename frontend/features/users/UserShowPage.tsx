import { useState } from 'react';
import { useNavigate, useParams } from '@tanstack/react-router';
import { useQuery } from '@tanstack/react-query';
import { KeyRound, Loader2, SquarePen, Trash2 } from 'lucide-react';
import { useCan } from '@/features/auth/useAuth';
import { fetchUserById } from '@/features/users/users.api';
import { UserFormDialog } from '@/features/users/UserFormDialog';
import { ChangePasswordDialog } from '@/features/users/ChangePasswordDialog';
import { DeleteUserDialog } from '@/features/users/DeleteUserDialog';
import { getErrorMessage } from '@/lib/api';
import { Avatar } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

function formatDate(value: string | null): string {
  return value ? new Date(value).toLocaleString() : '—';
}

/** User detail page, ported from the Livewire users/{user} show page. */
export function UserShowPage() {
  const { userId } = useParams({ from: '/authenticated/users/$userId' });
  const navigate = useNavigate();
  const can = useCan();
  const [formOpen, setFormOpen] = useState(false);
  const [passwordOpen, setPasswordOpen] = useState(false);
  const [deleteOpen, setDeleteOpen] = useState(false);

  const { data, isPending, isError, error } = useQuery({
    queryKey: ['users', 'detail', userId],
    queryFn: () => fetchUserById(userId),
  });

  const user = data?.data ?? null;

  if (isPending) {
    return (
      <div className="flex justify-center py-16">
        <Loader2 className="size-6 animate-spin text-zinc-400" />
      </div>
    );
  }

  if (isError || !user) {
    return (
      <p className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
        {getErrorMessage(error, 'Unable to load this user.')}
      </p>
    );
  }

  return (
    <div className="space-y-8">
      <div className="flex items-start justify-between gap-4">
        <div className="flex items-center gap-4">
          <Avatar name={user.name} className="size-14 text-lg" />
          <div>
            <h1 className="text-2xl font-semibold tracking-tight">{user.name}</h1>
            <p className="mt-0.5 text-sm text-muted-foreground">{user.email}</p>
          </div>
        </div>
        <div className="flex gap-2">
          {can('users.update') && (
            <Button variant="outline" onClick={() => setFormOpen(true)}>
              <SquarePen /> Edit
            </Button>
          )}
          {can('users.change-password') && (
            <Button variant="outline" onClick={() => setPasswordOpen(true)}>
              <KeyRound /> Change password
            </Button>
          )}
          {can('users.delete') && (
            <Button variant="destructive" onClick={() => setDeleteOpen(true)}>
              <Trash2 /> Delete
            </Button>
          )}
        </div>
      </div>

      <dl className="max-w-lg space-y-3 rounded-xl border border-zinc-200 p-6 text-sm dark:border-zinc-700">
        <div className="flex justify-between gap-4">
          <dt className="text-muted-foreground">Verified</dt>
          <dd>
            {user.email_verified_at ? (
              <Badge variant="emerald">Verified</Badge>
            ) : (
              <Badge variant="amber">Unverified</Badge>
            )}
          </dd>
        </div>
        <div className="flex justify-between gap-4">
          <dt className="text-muted-foreground">Created</dt>
          <dd>{formatDate(user.created_at)}</dd>
        </div>
        <div className="flex justify-between gap-4">
          <dt className="text-muted-foreground">Updated</dt>
          <dd>{formatDate(user.updated_at)}</dd>
        </div>
      </dl>

      <UserFormDialog open={formOpen} onOpenChange={setFormOpen} user={user} />
      <ChangePasswordDialog open={passwordOpen} onOpenChange={setPasswordOpen} user={user} />
      <DeleteUserDialog
        open={deleteOpen}
        onOpenChange={setDeleteOpen}
        user={user}
        onDeleted={() => void navigate({ to: '/users' })}
      />
    </div>
  );
}
