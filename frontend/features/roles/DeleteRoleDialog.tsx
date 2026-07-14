import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import { useDeleteRole } from '@/features/roles/useRoles';
import type { Role } from '@/features/roles/roles.api';
import { getErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';

interface DeleteRoleDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  role: Role | null;
  onDeleted?: () => void;
}

export function DeleteRoleDialog({ open, onOpenChange, role, onDeleted }: DeleteRoleDialogProps) {
  const deleteRole = useDeleteRole();

  async function handleDelete() {
    if (!role) {
      return;
    }
    try {
      await deleteRole.mutateAsync(role.id);
      toast.success('Role deleted.');
      onOpenChange(false);
      onDeleted?.();
    } catch (error) {
      toast.error(getErrorMessage(error, 'Unable to delete this role.'));
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent>
        <DialogTitle>Delete role</DialogTitle>
        <DialogDescription>
          Are you sure you want to delete <span className="font-medium">{role?.name}</span> (
          {role?.guard_name})? This action cannot be undone.
        </DialogDescription>

        <div className="mt-6 flex justify-end gap-2">
          <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
            Cancel
          </Button>
          <Button type="button" variant="destructive" disabled={deleteRole.isPending} onClick={handleDelete}>
            {deleteRole.isPending && <Loader2 className="animate-spin" />}
            Delete role
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
