import { useEffect, useState } from 'react';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import {
  useCreateRole,
  usePermissionGroups,
  useUpdateRole,
} from '@/features/roles/useRoles';
import type { GuardName, Role } from '@/features/roles/roles.api';
import { getFormErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Field, FormError } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

interface RoleFormDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  /** When set, the dialog edits this role; otherwise it creates a new one. */
  role: Role | null;
}

const guards: GuardName[] = ['web', 'sanctum'];

export function RoleFormDialog({ open, onOpenChange, role }: RoleFormDialogProps) {
  const isEditing = role !== null;
  const locked = role?.protected ?? false;
  const createRole = useCreateRole();
  const updateRole = useUpdateRole();

  const [name, setName] = useState('');
  const [guard, setGuard] = useState<GuardName>('web');
  const [selected, setSelected] = useState<string[]>([]);
  const [serverError, setServerError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const { data: permissionGroups } = usePermissionGroups(guard);

  // Re-seed the form whenever the dialog opens for a different role.
  useEffect(() => {
    if (open) {
      setName(role?.name ?? '');
      setGuard(role?.guard_name ?? 'web');
      setSelected(role?.permissions ?? []);
      setServerError(null);
    }
  }, [open, role]);

  function togglePermission(permission: string) {
    setSelected((value) =>
      value.includes(permission) ? value.filter((name) => name !== permission) : [...value, permission],
    );
  }

  function switchGuard(next: GuardName) {
    if (next !== guard) {
      setGuard(next);
      // Permissions are guard-specific, so a guard switch clears the selection.
      setSelected([]);
    }
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (name.trim() === '') {
      setServerError('Name is required.');
      return;
    }
    setServerError(null);
    setSubmitting(true);
    try {
      const input = { name: name.trim(), guard, permissions: selected };
      if (isEditing) {
        await updateRole.mutateAsync({ id: role.id, input });
        toast.success('Role updated.');
      } else {
        await createRole.mutateAsync(input);
        toast.success('Role created.');
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
      <DialogContent className="max-w-lg">
        <DialogTitle>{isEditing ? 'Edit role' : 'New role'}</DialogTitle>
        <DialogDescription>
          {isEditing ? 'Update the role and its permissions.' : 'Create a new role with permissions.'}
        </DialogDescription>

        <form className="mt-5 space-y-4" onSubmit={handleSubmit}>
          <FormError message={serverError} />

          <Field>
            <Label htmlFor="role-form-name">Name</Label>
            <Input
              id="role-form-name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              disabled={locked}
            />
            {locked && (
              <p className="text-xs text-muted-foreground">
                Protected roles keep their name and guard locked.
              </p>
            )}
          </Field>

          <Field>
            <Label>Guard</Label>
            <div className="flex items-center gap-1.5">
              {guards.map((value) => (
                <button
                  key={value}
                  type="button"
                  disabled={locked}
                  onClick={() => switchGuard(value)}
                  className={cn(
                    'cursor-pointer rounded-lg px-3.5 py-1.5 text-sm font-medium transition-colors disabled:cursor-not-allowed disabled:opacity-60',
                    guard === value
                      ? 'bg-indigo-600 text-white'
                      : 'border border-zinc-200 text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-700/50',
                  )}
                >
                  {value}
                </button>
              ))}
            </div>
          </Field>

          <Field>
            <Label>Permissions</Label>
            <div className="max-h-64 space-y-4 overflow-y-auto rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
              {Object.entries(permissionGroups?.data ?? {}).map(([group, permissions]) => (
                <div key={group}>
                  <p className="mb-2 text-xs font-semibold uppercase tracking-widest text-zinc-400">
                    {group}
                  </p>
                  <div className="grid gap-2 sm:grid-cols-2">
                    {permissions.map((permission) => (
                      <label
                        key={permission}
                        className="flex cursor-pointer items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300"
                      >
                        <span
                          onClick={() => togglePermission(permission)}
                          className={cn(
                            'flex size-4 shrink-0 items-center justify-center rounded border text-[10px]',
                            selected.includes(permission)
                              ? 'border-indigo-600 bg-indigo-600 text-white'
                              : 'border-zinc-300 dark:border-zinc-600',
                          )}
                        >
                          {selected.includes(permission) && '✓'}
                        </span>
                        <span onClick={() => togglePermission(permission)}>{permission}</span>
                      </label>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          </Field>

          <div className="flex justify-end gap-2 pt-2">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Cancel
            </Button>
            <Button type="submit" disabled={submitting}>
              {submitting && <Loader2 className="animate-spin" />}
              {isEditing ? 'Save changes' : 'Create role'}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
