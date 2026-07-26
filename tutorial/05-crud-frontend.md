# 5. CRUD part 4 — React frontend (SPA side)

A SPA feature is one folder under `frontend/features/<name>/` with a fixed shape — typed fetchers,
react-query hooks, a page, and dialogs — plus a route and a nav entry. The fuller reference is
`frontend/features/users/*`; Tasks below is the same pattern condensed.

Everything talks to the API through the shared fetch wrapper `frontend/lib/api.ts` (bearer token
injection, 401 handling, 422 helpers). The API base defaults to same-origin `/api`.

## 5.1 Typed API layer

`frontend/features/tasks/tasks.api.ts`:

```ts
import { api } from '@/lib/api';

export interface Task {
  id: string;
  title: string;
  notes: string | null;
  done: boolean;
  created_at: string | null;
  updated_at: string | null;
}

export interface TasksListParams {
  search?: string;
  sort_by?: 'title' | 'done' | 'created_at' | 'updated_at';
  sort_dir?: 'asc' | 'desc';
  per_page?: number;
  page?: number;
}

/** Laravel paginator envelope produced by TaskResource::collection(paginate). */
export interface PaginatedTasks {
  data: Task[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
  };
}

export interface TaskInput {
  title: string;
  notes: string;
  done: boolean;
}

export function fetchTasks(params: TasksListParams): Promise<PaginatedTasks> {
  const query = new URLSearchParams();
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== '') {
      query.set(key, String(value));
    }
  });
  const queryString = query.toString();
  return api.get<PaginatedTasks>(`/tasks${queryString ? `?${queryString}` : ''}`);
}

export function createTask(input: TaskInput): Promise<{ message: string; task: Task }> {
  return api.post<{ message: string; task: Task }>('/tasks', input);
}

export function updateTask(id: string, input: TaskInput): Promise<{ message: string; task: Task }> {
  return api.put<{ message: string; task: Task }>(`/tasks/${id}`, input);
}

export function deleteTask(id: string): Promise<{ message: string }> {
  return api.delete<{ message: string }>(`/tasks/${id}`);
}
```

## 5.2 React-query hooks

`frontend/features/tasks/useTasks.ts` — the list query is keyed by its params; every mutation
invalidates the feature key so lists refetch:

```ts
import { keepPreviousData, useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import {
  createTask,
  deleteTask,
  fetchTasks,
  updateTask,
  type TaskInput,
  type TasksListParams,
} from '@/features/tasks/tasks.api';

const TASKS_KEY = 'tasks';

export function useTasksList(params: TasksListParams) {
  return useQuery({
    queryKey: [TASKS_KEY, params],
    queryFn: () => fetchTasks(params),
    placeholderData: keepPreviousData,
  });
}

function useInvalidateTasks() {
  const queryClient = useQueryClient();
  return () => queryClient.invalidateQueries({ queryKey: [TASKS_KEY] });
}

export function useCreateTask() {
  const invalidate = useInvalidateTasks();
  return useMutation({ mutationFn: (input: TaskInput) => createTask(input), onSuccess: invalidate });
}

export function useUpdateTask() {
  const invalidate = useInvalidateTasks();
  return useMutation({
    mutationFn: ({ id, input }: { id: string; input: TaskInput }) => updateTask(id, input),
    onSuccess: invalidate,
  });
}

export function useDeleteTask() {
  const invalidate = useInvalidateTasks();
  return useMutation({ mutationFn: (id: string) => deleteTask(id), onSuccess: invalidate });
}
```

## 5.3 Form dialog

`frontend/features/tasks/TaskFormDialog.tsx` — plain controlled inputs are fine for a small form
(for zod + `@tanstack/react-form` wiring see `frontend/features/users/UserFormDialog.tsx`):

```tsx
import { useEffect, useState } from 'react';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import type { Task } from '@/features/tasks/tasks.api';
import { useCreateTask, useUpdateTask } from '@/features/tasks/useTasks';
import { getFormErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Field, FormError } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

interface TaskFormDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  /** When set, the dialog edits this task; otherwise it creates a new one. */
  task: Task | null;
}

export function TaskFormDialog({ open, onOpenChange, task }: TaskFormDialogProps) {
  const isEditing = task !== null;
  const createTask = useCreateTask();
  const updateTask = useUpdateTask();

  const [title, setTitle] = useState('');
  const [notes, setNotes] = useState('');
  const [done, setDone] = useState(false);
  const [serverError, setServerError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  // Re-seed the form whenever the dialog opens for a different task.
  useEffect(() => {
    if (open) {
      setTitle(task?.title ?? '');
      setNotes(task?.notes ?? '');
      setDone(task?.done ?? false);
      setServerError(null);
    }
  }, [open, task]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setServerError(null);
    setSubmitting(true);
    try {
      const input = { title, notes, done };
      if (isEditing) {
        await updateTask.mutateAsync({ id: task.id, input });
        toast.success('Task updated.');
      } else {
        await createTask.mutateAsync(input);
        toast.success('Task created.');
      }
      onOpenChange(false);
    } catch (error) {
      setServerError(getFormErrorMessage(error));
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent>
        <DialogTitle>{isEditing ? 'Edit task' : 'New task'}</DialogTitle>
        <DialogDescription>{isEditing ? 'Update this task.' : 'Add a new task.'}</DialogDescription>

        <form className="mt-5 space-y-4" onSubmit={handleSubmit}>
          <FormError message={serverError} />

          <Field>
            <Label htmlFor="task-form-title">Title</Label>
            <Input id="task-form-title" value={title} onChange={(e) => setTitle(e.target.value)} />
          </Field>

          <Field>
            <Label htmlFor="task-form-notes">Notes</Label>
            <textarea
              id="task-form-notes"
              rows={4}
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              className="w-full rounded-lg border border-input bg-transparent px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring"
            />
          </Field>

          <label className="flex cursor-pointer items-center gap-2 text-sm">
            <span
              onClick={() => setDone((value) => !value)}
              className={cn(
                'flex size-4 items-center justify-center rounded border text-[10px]',
                done ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-zinc-300 dark:border-zinc-600',
              )}
            >
              {done && '✓'}
            </span>
            Done
          </label>

          <div className="flex justify-end gap-2 pt-2">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Cancel
            </Button>
            <Button type="submit" disabled={submitting}>
              {submitting && <Loader2 className="animate-spin" />}
              {isEditing ? 'Save changes' : 'Create task'}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
```

## 5.4 List page

`frontend/features/tasks/TasksPage.tsx` — search (debounced), pagination, permission-gated actions
(delete confirm dialog omitted for brevity; copy `frontend/features/users/DeleteUserDialog.tsx` if
you want one instead of the inline confirm below):

```tsx
import { useEffect, useMemo, useState } from 'react';
import { toast } from 'sonner';
import { ChevronLeft, ChevronRight, Loader2, Plus, Search, SquarePen, Trash2 } from 'lucide-react';
import { useCan } from '@/features/auth/useAuth';
import type { Task, TasksListParams } from '@/features/tasks/tasks.api';
import { useDeleteTask, useTasksList } from '@/features/tasks/useTasks';
import { TaskFormDialog } from '@/features/tasks/TaskFormDialog';
import { getErrorMessage } from '@/lib/api';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

function useDebouncedValue<T>(value: T, delayMs: number): T {
  const [debounced, setDebounced] = useState(value);
  useEffect(() => {
    const timer = setTimeout(() => setDebounced(value), delayMs);
    return () => clearTimeout(timer);
  }, [value, delayMs]);
  return debounced;
}

/** Tasks index backed by GET /api/tasks. */
export function TasksPage() {
  const can = useCan();
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const debouncedSearch = useDebouncedValue(search, 300);

  const params = useMemo<TasksListParams>(
    () => ({ search: debouncedSearch || undefined, per_page: 10, page }),
    [debouncedSearch, page],
  );

  const { data, isPending, isError, error } = useTasksList(params);
  const deleteTask = useDeleteTask();

  const [formOpen, setFormOpen] = useState(false);
  const [selectedTask, setSelectedTask] = useState<Task | null>(null);

  async function handleDelete(task: Task) {
    if (!window.confirm(`Delete "${task.title}"?`)) {
      return;
    }
    try {
      await deleteTask.mutateAsync(task.id);
      toast.success('Task deleted.');
    } catch (err) {
      toast.error(getErrorMessage(err, 'Unable to delete this task.'));
    }
  }

  const tasks = data?.data ?? [];
  const meta = data?.meta;

  return (
    <div className="space-y-6">
      <div className="flex items-start justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight">Tasks</h1>
          <p className="mt-1 text-sm text-muted-foreground">Track things to do.</p>
        </div>
        {can('tasks.create') && (
          <Button
            onClick={() => {
              setSelectedTask(null);
              setFormOpen(true);
            }}
          >
            <Plus /> New task
          </Button>
        )}
      </div>

      <div className="relative w-full max-w-xs">
        <Search className="absolute start-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400" />
        <Input
          value={search}
          onChange={(e) => {
            setSearch(e.target.value);
            setPage(1);
          }}
          placeholder="Search tasks..."
          className="ps-9"
        />
      </div>

      {isError ? (
        <p className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
          {getErrorMessage(error, 'Unable to load tasks.')}
        </p>
      ) : isPending ? (
        <div className="flex justify-center py-16">
          <Loader2 className="size-6 animate-spin text-zinc-400" />
        </div>
      ) : (
        <>
          <Table>
            <TableHeader>
              <TableRow className="hover:bg-transparent dark:hover:bg-transparent">
                <TableHead>Title</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-end">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {tasks.map((task) => (
                <TableRow key={task.id}>
                  <TableCell className="font-medium">{task.title}</TableCell>
                  <TableCell>
                    {task.done ? <Badge variant="emerald">Done</Badge> : <Badge>Open</Badge>}
                  </TableCell>
                  <TableCell className="text-end">
                    <div className="flex justify-end gap-1">
                      {can('tasks.update') && (
                        <Button
                          variant="ghost"
                          size="icon"
                          className="size-8"
                          aria-label="Edit"
                          onClick={() => {
                            setSelectedTask(task);
                            setFormOpen(true);
                          }}
                        >
                          <SquarePen />
                        </Button>
                      )}
                      {can('tasks.delete') && (
                        <Button
                          variant="ghost"
                          size="icon"
                          className="size-8 text-red-500 hover:text-red-600"
                          aria-label="Delete"
                          onClick={() => void handleDelete(task)}
                        >
                          <Trash2 />
                        </Button>
                      )}
                    </div>
                  </TableCell>
                </TableRow>
              ))}
              {tasks.length === 0 && (
                <TableRow>
                  <TableCell colSpan={3} className="py-12 text-center text-muted-foreground">
                    No tasks found.
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>

          {meta && meta.last_page > 1 && (
            <div className="flex items-center justify-between gap-4">
              <p className="text-sm text-muted-foreground">
                Showing {meta.from ?? 0} to {meta.to ?? 0} of {meta.total} results
              </p>
              <div className="flex items-center gap-1">
                <Button
                  variant="outline"
                  size="icon"
                  className="size-8"
                  aria-label="Previous page"
                  disabled={meta.current_page <= 1}
                  onClick={() => setPage((value) => value - 1)}
                >
                  <ChevronLeft />
                </Button>
                <span className="px-2 text-sm text-muted-foreground">
                  Page {meta.current_page} of {meta.last_page}
                </span>
                <Button
                  variant="outline"
                  size="icon"
                  className="size-8"
                  aria-label="Next page"
                  disabled={meta.current_page >= meta.last_page}
                  onClick={() => setPage((value) => value + 1)}
                >
                  <ChevronRight />
                </Button>
              </div>
            </div>
          )}
        </>
      )}

      <TaskFormDialog open={formOpen} onOpenChange={setFormOpen} task={selectedTask} />
    </div>
  );
}
```

## 5.5 Route

In `frontend/router.tsx`:

```tsx
import { TasksPage } from '@/features/tasks/TasksPage';

const tasksRoute = createRoute({
  getParentRoute: () => authRoute,
  path: '/tasks',
  component: TasksPage,
});
```

…and add `tasksRoute` to the `authRoute.addChildren([...])` list.

## 5.6 Navigation + breadcrumbs (RBAC-gated)

`frontend/features/app-shell/nav.ts` — declare the item with its permission:

```ts
import { ClipboardList } from 'lucide-react';

export const tasksItem: NavItem = {
  label: 'Tasks',
  to: '/tasks',
  icon: ClipboardList,
  match: '/tasks',
  permission: 'tasks.index',
};
```

Render it in both shells, wrapped in the same `useCan()` check the Auth group uses:

- `frontend/features/app-shell/Sidebar.tsx` — after `<SidebarLink item={dashboardItem} />`:

  ```tsx
  {(!tasksItem.permission || can(tasksItem.permission)) && <SidebarLink item={tasksItem} />}
  ```

- `frontend/features/app-shell/AppShell.tsx` (`HeaderShell` navbar) — a plain link next to the Auth
  dropdown, styled like the dropdown trigger, gated the same way.

Breadcrumbs — extend `breadcrumbsFor()` in `nav.ts`:

```ts
if (pathname.startsWith('/tasks')) {
  return [{ label: 'Tasks', to: '/tasks' }];
}
```

The permission comes from `GET /api/user` (roles + permissions for the sanctum guard), so users
without `tasks.index` never see the nav item — and the API middleware still enforces it server-side.

## 5.7 Build & verify

```bash
npm run build        # typecheck + blade assets + SPA
```

Then at http://localhost:9900:

1. `/#/login` as `admin@admin.com` / `admin` → **Tasks** appears in the sidebar.
2. `/#/tasks` — create, edit, and delete a task; search and paginate.
3. Log in as a user without `tasks.index` → the nav item is hidden and `GET /api/tasks` returns 403.
4. DevTools console — no errors.

For fast iteration during development use `npm run dev:frontend` (SPA on `:5173` with HMR; set
`VITE_API_URL=http://localhost:9900/api` in the env for cross-origin API calls).

---

That's the full stack: one resource, five permissions, two frontends, one API — all following the
patterns already living in this repo. When in doubt, read the **users** feature at each layer; it is
the canonical example.
