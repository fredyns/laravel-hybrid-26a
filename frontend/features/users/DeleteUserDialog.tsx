import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import type { User } from '@/features/auth/auth.api';
import { useDeleteUser } from '@/features/users/useUsers';
import { getErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';

interface DeleteUserDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  user: User | null;
  onDeleted?: () => void;
}

export function DeleteUserDialog({ open, onOpenChange, user, onDeleted }: DeleteUserDialogProps) {
  const deleteUser = useDeleteUser();

  async function handleDelete() {
    if (!user) {
      return;
    }
    try {
      await deleteUser.mutateAsync(user.id);
      toast.success('User deleted.');
      onOpenChange(false);
      onDeleted?.();
    } catch (error) {
      toast.error(getErrorMessage(error, 'Unable to delete this user.'));
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent>
        <DialogTitle>Delete user</DialogTitle>
        <DialogDescription>
          Are you sure you want to delete <span className="font-medium">{user?.name}</span>? This action
          cannot be undone.
        </DialogDescription>

        <div className="mt-6 flex justify-end gap-2">
          <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
            Cancel
          </Button>
          <Button type="button" variant="destructive" disabled={deleteUser.isPending} onClick={handleDelete}>
            {deleteUser.isPending && <Loader2 className="animate-spin" />}
            Delete user
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
