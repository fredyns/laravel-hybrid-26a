import { useMemo, useState } from 'react';
import { useNavigate, useParams } from '@tanstack/react-router';
import { Loader2, ShieldCheck, SquarePen, Trash2 } from 'lucide-react';
import { useCan } from '@/features/auth/useAuth';
import { useRole } from '@/features/roles/useRoles';
import { RoleFormDialog } from '@/features/roles/RoleFormDialog';
import { DeleteRoleDialog } from '@/features/roles/DeleteRoleDialog';
import { getErrorMessage } from '@/lib/api';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

/** Role detail page, ported from the Livewire roles/{role} show page. */
export function RoleShowPage() {
  const { roleId } = useParams({ from: '/authenticated/roles/$roleId' });
  const navigate = useNavigate();
  const can = useCan();
  const { data, isPending, isError, error } = useRole(roleId);
  const [formOpen, setFormOpen] = useState(false);
  const [deleteOpen, setDeleteOpen] = useState(false);

  const role = data?.data ?? null;

  /** Permissions grouped by resource prefix, mirroring the Livewire page. */
  const permissionGroups = useMemo(() => {
    const groups: Record<string, string[]> = {};
    (role?.permissions ?? []).forEach((permission) => {
      const group = permission.split('.')[0] ?? permission;
      (groups[group] ??= []).push(permission);
    });
    return groups;
  }, [role]);

  if (isPending) {
    return (
      <div className="flex justify-center py-16">
        <Loader2 className="size-6 animate-spin text-zinc-400" />
      </div>
    );
  }

  if (isError || !role) {
    return (
      <p className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
        {getErrorMessage(error, 'Unable to load this role.')}
      </p>
    );
  }

  return (
    <div className="space-y-8">
      <div className="flex items-start justify-between gap-4">
        <div>
          <div className="flex items-center gap-3">
            <h1 className="text-2xl font-semibold tracking-tight">{role.name}</h1>
            <Badge>{role.guard_name}</Badge>
            {role.protected && <Badge variant="amber">Protected</Badge>}
          </div>
          <p className="mt-1 text-sm text-muted-foreground">
            {role.permissions_count ?? role.permissions?.length ?? 0} permissions ·{' '}
            {role.users_count ?? 0} users
          </p>
        </div>
        <div className="flex gap-2">
          {can('rbac.update') && (
            <Button variant="outline" onClick={() => setFormOpen(true)}>
              <SquarePen /> Edit
            </Button>
          )}
          {can('rbac.delete') && !role.protected && (
            <Button variant="destructive" onClick={() => setDeleteOpen(true)}>
              <Trash2 /> Delete
            </Button>
          )}
        </div>
      </div>

      <section className="space-y-4">
        <h2 className="font-semibold">Permissions</h2>
        {Object.keys(permissionGroups).length === 0 ? (
          <p className="text-sm text-muted-foreground">This role has no permissions.</p>
        ) : (
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {Object.entries(permissionGroups).map(([group, permissions]) => (
              <div
                key={group}
                className="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700"
              >
                <p className="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">
                  {group}
                </p>
                <ul className="space-y-2">
                  {permissions.map((permission) => (
                    <li key={permission} className="flex items-center gap-2 text-sm">
                      <ShieldCheck className="size-4 text-emerald-500" />
                      {permission}
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        )}
      </section>

      <RoleFormDialog open={formOpen} onOpenChange={setFormOpen} role={role} />
      <DeleteRoleDialog
        open={deleteOpen}
        onOpenChange={setDeleteOpen}
        role={role}
        onDeleted={() => void navigate({ to: '/roles' })}
      />
    </div>
  );
}
