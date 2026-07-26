import { useEffect, useMemo, useState } from 'react';
import {
  ChevronDown,
  Columns3,
  Eye,
  KeyRound,
  Plus,
  Search,
  SquarePen,
  Trash2,
} from 'lucide-react';
import { useNavigate } from '@tanstack/react-router';
import type { User } from '@/features/auth/auth.api';
import { useCan } from '@/features/auth/useAuth';
import { useUsersList } from '@/features/users/useUsers';
import type { UsersListParams } from '@/features/users/users.api';
import { UserFormDialog } from '@/features/users/UserFormDialog';
import { ChangePasswordDialog } from '@/features/users/ChangePasswordDialog';
import { DeleteUserDialog } from '@/features/users/DeleteUserDialog';
import { getErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { TableSkeleton } from '@/components/ui/table-skeleton';
import { cn } from '@/lib/utils';

type ColumnKey = 'name' | 'email' | 'created_at';
type SortColumn = NonNullable<UsersListParams['sort_by']>;

const columnLabels: Record<ColumnKey, string> = {
  name: 'Name',
  email: 'Email',
  created_at: 'Created',
};

type DialogKind = 'form' | 'password' | 'delete';

function useDebouncedValue<T>(value: T, delayMs: number): T {
  const [debounced, setDebounced] = useState(value);
  useEffect(() => {
    const timer = setTimeout(() => setDebounced(value), delayMs);
    return () => clearTimeout(timer);
  }, [value, delayMs]);
  return debounced;
}

function formatDateTime(value: string | null): string {
  if (!value) {
    return '—';
  }
  const date = new Date(value);
  const pad = (n: number) => String(n).padStart(2, '0');
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

/** Users index backed by GET /api/users with server-side search, sort and pagination. */
export function UsersPage() {
  const navigate = useNavigate();
  const can = useCan();
  const [search, setSearch] = useState('');
  const [perPage, setPerPage] = useState(10);
  const [page, setPage] = useState(1);
  const [sortBy, setSortBy] = useState<SortColumn>('created_at');
  const [sortDir, setSortDir] = useState<'asc' | 'desc'>('desc');
  const [visible, setVisible] = useState<Record<ColumnKey, boolean>>({
    name: true,
    email: true,
    created_at: true,
  });

  const debouncedSearch = useDebouncedValue(search, 300);

  const params = useMemo<UsersListParams>(
    () => ({
      search: debouncedSearch || undefined,
      sort_by: sortBy,
      sort_dir: sortDir,
      per_page: perPage,
      page,
    }),
    [debouncedSearch, sortBy, sortDir, perPage, page],
  );

  const { data, isPending, isError, error } = useUsersList(params);

  const [dialog, setDialog] = useState<DialogKind | null>(null);
  const [selectedUser, setSelectedUser] = useState<User | null>(null);

  function openDialog(kind: DialogKind, user: User | null) {
    setSelectedUser(user);
    setDialog(kind);
  }

  function sortByColumn(column: SortColumn) {
    if (sortBy === column) {
      setSortDir((dir) => (dir === 'asc' ? 'desc' : 'asc'));
    } else {
      setSortBy(column);
      setSortDir('asc');
    }
    setPage(1);
  }

  const users = data?.data ?? [];
  const meta = data?.meta;

  return (
    <div className="space-y-6">
      <div className="flex items-start justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight">Users</h1>
          <p className="mt-1 text-sm text-muted-foreground">Manage application users.</p>
        </div>
        {can('users.create') && (
          <Button onClick={() => openDialog('form', null)}>
            <Plus /> New user
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
            placeholder="Search name or email..."
            className="ps-9"
          />
        </div>

        <div className="ms-auto flex items-center gap-3">
          <Input
            type="number"
            min={1}
            max={100}
            value={perPage}
            onChange={(e) => {
              setPerPage(Math.min(100, Math.max(1, Number(e.target.value) || 1)));
              setPage(1);
            }}
            className="w-24"
            aria-label="Per page"
          />

          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline">
                <Columns3 /> Columns <ChevronDown className="size-3.5" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              {(Object.keys(columnLabels) as ColumnKey[]).map((key) => (
                <DropdownMenuItem
                  key={key}
                  onSelect={(e) => {
                    e.preventDefault();
                    setVisible((value) => ({ ...value, [key]: !value[key] }));
                  }}
                >
                  <span
                    className={cn(
                      'flex size-4 items-center justify-center rounded border text-[10px]',
                      visible[key]
                        ? 'border-indigo-600 bg-indigo-600 text-white'
                        : 'border-zinc-300 dark:border-zinc-600',
                    )}
                  >
                    {visible[key] && '✓'}
                  </span>
                  {columnLabels[key]}
                </DropdownMenuItem>
              ))}
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>

      {isError ? (
        <p className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
          {getErrorMessage(error, 'Unable to load users.')}
        </p>
      ) : isPending ? (
        <TableSkeleton rows={5} columns={4} />
      ) : (
        <div className="space-y-4">
          <div className="w-full overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
            <table className="w-full text-sm">
              <thead className="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <tr>
                  {visible.name && (
                    <th className="px-4 py-3 text-start text-sm font-medium text-zinc-800 dark:text-zinc-200">
                      <button
                        type="button"
                        onClick={() => sortByColumn('name')}
                        className={cn(
                          'inline-flex cursor-pointer items-center gap-1.5 hover:opacity-60',
                        )}
                      >
                        Name
                        {sortBy === 'name' && (
                          <ChevronDown className={cn('size-3.5 text-zinc-400', sortDir === 'asc' && 'rotate-180')} />
                        )}
                      </button>
                    </th>
                  )}
                  {visible.email && (
                    <th className="px-4 py-3 text-start text-sm font-medium text-zinc-800 dark:text-zinc-200">
                      <button
                        type="button"
                        onClick={() => sortByColumn('email')}
                        className={cn(
                          'inline-flex cursor-pointer items-center gap-1.5 hover:opacity-60',
                        )}
                      >
                        Email
                        {sortBy === 'email' && (
                          <ChevronDown className={cn('size-3.5 text-zinc-400', sortDir === 'asc' && 'rotate-180')} />
                        )}
                      </button>
                    </th>
                  )}
                  {visible.created_at && (
                    <th className="px-4 py-3 text-start text-sm font-medium text-zinc-800 dark:text-zinc-200">
                      <button
                        type="button"
                        onClick={() => sortByColumn('created_at')}
                        className={cn(
                          'inline-flex cursor-pointer items-center gap-1.5 hover:opacity-60',
                        )}
                      >
                        Created
                        {sortBy === 'created_at' && (
                          <ChevronDown className={cn('size-3.5 text-zinc-400', sortDir === 'asc' && 'rotate-180')} />
                        )}
                      </button>
                    </th>
                  )}
                  <th className="px-4 py-3 text-end text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-100 dark:divide-zinc-700/60">
                {users.length === 0 ? (
                  <tr>
                    <td
                      colSpan={Object.values(visible).filter(Boolean).length + 1}
                      className="py-12"
                    >
                      <p className="text-center text-sm text-muted-foreground">No users found.</p>
                    </td>
                  </tr>
                ) : (
                  users.map((user) => (
                    <tr key={user.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-700/30">
                      {visible.name && <td className="px-4 py-4 font-medium">{user.name}</td>}
                      {visible.email && <td className="px-4 py-4 text-sm text-muted-foreground">{user.email}</td>}
                      {visible.created_at && (
                        <td className="px-4 py-4 text-sm text-muted-foreground">
                          {formatDateTime(user.created_at)}
                        </td>
                      )}
                      <td className="px-4 py-4 text-end">
                        <div className="flex justify-end gap-1">
                          {can('users.show') && (
                            <Button
                              variant="ghost"
                              size="icon"
                              className="size-8"
                              aria-label="View"
                              onClick={() => navigate({ to: '/users/$userId', params: { userId: user.id } })}
                            >
                              <Eye />
                            </Button>
                          )}
                          {can('users.update') && (
                            <Button
                              variant="ghost"
                              size="icon"
                              className="size-8"
                              aria-label="Edit"
                              onClick={() => openDialog('form', user)}
                            >
                              <SquarePen />
                            </Button>
                          )}
                          {can('users.change-password') && (
                            <Button
                              variant="ghost"
                              size="icon"
                              className="size-8"
                              aria-label="Change password"
                              onClick={() => openDialog('password', user)}
                            >
                              <KeyRound />
                            </Button>
                          )}
                          {can('users.delete') && (
                            <Button
                              variant="ghost"
                              size="icon"
                              className="size-8 text-red-500 hover:text-red-600"
                              aria-label="Delete"
                              onClick={() => openDialog('delete', user)}
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

      <UserFormDialog open={dialog === 'form'} onOpenChange={(open) => !open && setDialog(null)} user={selectedUser} />
      <ChangePasswordDialog
        open={dialog === 'password'}
        onOpenChange={(open) => !open && setDialog(null)}
        user={selectedUser}
      />
      <DeleteUserDialog open={dialog === 'delete'} onOpenChange={(open) => !open && setDialog(null)} user={selectedUser} />
    </div>
  );
}
