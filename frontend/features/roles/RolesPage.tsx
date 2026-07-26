import { useMemo, useState } from 'react';
import { useNavigate } from '@tanstack/react-router';
import {
  Eye,
  Plus,
  Search,
  ShieldCheck,
  SquarePen,
  Trash2,
} from 'lucide-react';
import { useCan } from '@/features/auth/useAuth';
import { useRolesList } from '@/features/roles/useRoles';
import type { GuardName, Role, RolesListParams } from '@/features/roles/roles.api';
import { RoleFormDialog } from '@/features/roles/RoleFormDialog';
import { DeleteRoleDialog } from '@/features/roles/DeleteRoleDialog';
import { fetchRoleById } from '@/features/roles/roles.api';
import { getErrorMessage } from '@/lib/api';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { TableSkeleton } from '@/components/ui/table-skeleton';
import { cn } from '@/lib/utils';

const guardFilters = ['All', 'web', 'sanctum'] as const;

/** Roles index backed by GET /api/roles with guard filter and pagination. */
export function RolesPage() {
  const navigate = useNavigate();
  const can = useCan();
  const [search, setSearch] = useState('');
  const [guard, setGuard] = useState<(typeof guardFilters)[number]>('All');
  const [page, setPage] = useState(1);

  const params = useMemo<RolesListParams>(
    () => ({
      search: search || undefined,
      guard: guard === 'All' ? undefined : (guard as GuardName),
      per_page: 10,
      page,
    }),
    [search, guard, page],
  );

  const { data, isPending, isError, error } = useRolesList(params);

  const [formOpen, setFormOpen] = useState(false);
  const [deleteOpen, setDeleteOpen] = useState(false);
  const [selectedRole, setSelectedRole] = useState<Role | null>(null);

  async function openEdit(role: Role) {
    // Fetch the full role so the form has its current permissions.
    const { data: fullRole } = await fetchRoleById(role.id);
    setSelectedRole(fullRole);
    setFormOpen(true);
  }

  const roles = data?.data ?? [];
  const meta = data?.meta;

  return (
    <div className="space-y-6">
      <div className="flex items-start justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight">Roles</h1>
          <p className="mt-1 text-sm text-muted-foreground">Manage roles and their permissions.</p>
        </div>
        {can('rbac.create') && (
          <Button
            onClick={() => {
              setSelectedRole(null);
              setFormOpen(true);
            }}
          >
            <Plus /> New role
          </Button>
        )}
      </div>

      <div className="flex flex-wrap items-center gap-3">
        <div className="relative w-full max-w-xs">
          <Search className="absolute start-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400" />
          <Input
            value={search}
            onChange={(e) => {
              setSearch(e.target.value);
              setPage(1);
            }}
            placeholder="Search roles..."
            className="ps-9"
          />
        </div>

        <div className="flex items-center gap-1.5">
          {guardFilters.map((value) => (
            <button
              key={value}
              type="button"
              onClick={() => {
                setGuard(value);
                setPage(1);
              }}
              className={cn(
                'cursor-pointer rounded-lg px-3.5 py-1.5 text-sm font-medium transition-colors',
                guard === value
                  ? 'bg-indigo-600 text-white'
                  : 'border border-zinc-200 text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-700/50',
              )}
            >
              {value}
            </button>
          ))}
        </div>
      </div>

      {isError ? (
        <p className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
          {getErrorMessage(error, 'Unable to load roles.')}
        </p>
      ) : isPending ? (
        <TableSkeleton rows={5} columns={6} />
      ) : (
        <div className="space-y-4">
          <div className="w-full overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
            <table className="w-full text-sm">
              <thead className="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <tr>
                  <th className="px-4 py-3 text-start text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    Name
                  </th>
                  <th className="px-4 py-3 text-start text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    Guard
                  </th>
                  <th className="px-4 py-3 text-start text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    Permissions
                  </th>
                  <th className="px-4 py-3 text-start text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    Users
                  </th>
                  <th className="px-4 py-3 text-start text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    Type
                  </th>
                  <th className="px-4 py-3 text-end text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-100 dark:divide-zinc-700/60">
                {roles.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="py-12">
                      <p className="text-center text-sm text-muted-foreground">No roles found.</p>
                    </td>
                  </tr>
                ) : (
                  roles.map((role) => (
                    <tr key={role.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-700/30">
                      <td className="px-4 py-4 font-medium">{role.name}</td>
                      <td className="px-4 py-4">
                        <Badge>{role.guard_name}</Badge>
                      </td>
                      <td className="px-4 py-4">
                        <span className="inline-flex items-center gap-1.5 text-sm text-muted-foreground">
                          <ShieldCheck className="size-4" />
                          {role.permissions_count ?? 0}
                        </span>
                      </td>
                      <td className="px-4 py-4 text-sm text-muted-foreground">
                        {role.users_count ?? 0}
                      </td>
                      <td className="px-4 py-4">
                        {role.protected && <Badge variant="amber">Protected</Badge>}
                      </td>
                      <td className="px-4 py-4 text-end">
                        <div className="flex justify-end gap-1">
                          {can('rbac.show') && (
                            <Button
                              variant="ghost"
                              size="icon"
                              className="size-8"
                              aria-label="View"
                              onClick={() => navigate({ to: '/roles/$roleId', params: { roleId: role.id } })}
                            >
                              <Eye />
                            </Button>
                          )}
                          {can('rbac.update') && (
                            <Button
                              variant="ghost"
                              size="icon"
                              className="size-8"
                              aria-label="Edit"
                              onClick={() => void openEdit(role)}
                            >
                              <SquarePen />
                            </Button>
                          )}
                          {can('rbac.delete') && !role.protected && (
                            <Button
                              variant="ghost"
                              size="icon"
                              className="size-8 text-red-500 hover:text-red-600"
                              aria-label="Delete"
                              onClick={() => {
                                setSelectedRole(role);
                                setDeleteOpen(true);
                              }}
                            >
                              <Trash2 />
                            </Button>
                          )}
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>

          {meta && (
            <div className="flex items-center justify-between gap-4">
              <p className="text-sm text-muted-foreground">
                Showing {meta.from ?? 0} to {meta.to ?? 0} of {meta.total} results
              </p>
              {meta.last_page > 1 && (
                <div className="flex items-center gap-2">
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setPage((value) => value - 1)}
                    disabled={meta.current_page <= 1}
                  >
                    Previous
                  </Button>
                  <span className="px-2 text-sm text-muted-foreground">
                    Page {meta.current_page} of {meta.last_page}
                  </span>
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setPage((value) => value + 1)}
                    disabled={meta.current_page >= meta.last_page}
                  >
                    Next
                  </Button>
                </div>
              )}
            </div>
          )}
        </div>
      )}

      <RoleFormDialog open={formOpen} onOpenChange={setFormOpen} role={selectedRole} />
      <DeleteRoleDialog open={deleteOpen} onOpenChange={setDeleteOpen} role={selectedRole} />
    </div>
  );
}
