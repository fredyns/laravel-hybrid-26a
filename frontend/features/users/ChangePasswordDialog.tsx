import { useEffect, useState } from 'react';
import { useForm } from '@tanstack/react-form';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import type { User } from '@/features/auth/auth.api';
import { useChangeUserPassword } from '@/features/users/useUsers';
import { changePasswordSchema } from '@/features/users/users.schemas';
import { getFormErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Field, FieldError, FormError } from '@/components/ui/field';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';

interface ChangePasswordDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  user: User | null;
}

export function ChangePasswordDialog({ open, onOpenChange, user }: ChangePasswordDialogProps) {
  const changePassword = useChangeUserPassword();
  const [serverError, setServerError] = useState<string | null>(null);

  const form = useForm({
    defaultValues: { password: '', password_confirmation: '' },
    validators: { onSubmit: changePasswordSchema },
    onSubmit: async ({ value }) => {
      if (!user) {
        return;
      }
      setServerError(null);
      try {
        await changePassword.mutateAsync({ id: user.id, input: value });
        toast.success('Password updated.');
        onOpenChange(false);
      } catch (error) {
        setServerError(getFormErrorMessage(error));
      }
    },
  });

  useEffect(() => {
    if (open) {
      form.reset({ password: '', password_confirmation: '' });
      setServerError(null);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open, user]);

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent>
        <DialogTitle>Change password</DialogTitle>
        <DialogDescription>Set a new password for {user?.name}.</DialogDescription>

        <form
          className="mt-5 space-y-4"
          onSubmit={(e) => {
            e.preventDefault();
            e.stopPropagation();
            void form.handleSubmit();
          }}
        >
          <FormError message={serverError} />

          <form.Field name="password">
            {(field) => (
              <Field>
                <Label htmlFor="change-password-new">New password</Label>
                <PasswordInput
                  id="change-password-new"
                  value={field.state.value}
                  onBlur={field.handleBlur}
                  onChange={(e) => field.handleChange(e.target.value)}
                  aria-invalid={field.state.meta.errors.length > 0}
                />
                <FieldError errors={field.state.meta.errors} />
              </Field>
            )}
          </form.Field>

          <form.Field name="password_confirmation">
            {(field) => (
              <Field>
                <Label htmlFor="change-password-confirm">Confirm password</Label>
                <PasswordInput
                  id="change-password-confirm"
                  value={field.state.value}
                  onBlur={field.handleBlur}
                  onChange={(e) => field.handleChange(e.target.value)}
                  aria-invalid={field.state.meta.errors.length > 0}
                />
                <FieldError errors={field.state.meta.errors} />
              </Field>
            )}
          </form.Field>

          <div className="flex justify-end gap-2 pt-2">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Cancel
            </Button>
            <form.Subscribe selector={(state) => state.isSubmitting}>
              {(isSubmitting) => (
                <Button type="submit" disabled={isSubmitting}>
                  {isSubmitting && <Loader2 className="animate-spin" />}
                  Update password
                </Button>
              )}
            </form.Subscribe>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
