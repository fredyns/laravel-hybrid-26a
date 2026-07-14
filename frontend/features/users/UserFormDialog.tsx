import { useEffect, useState } from 'react';
import { useForm } from '@tanstack/react-form';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import type { User } from '@/features/auth/auth.api';
import { useCreateUser, useUpdateUser } from '@/features/users/useUsers';
import { createUserSchema, editUserFormSchema } from '@/features/users/users.schemas';
import { getFormErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Field, FieldError, FormError } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';

interface UserFormDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  /** When set, the dialog edits this user; otherwise it creates a new one. */
  user: User | null;
}

export function UserFormDialog({ open, onOpenChange, user }: UserFormDialogProps) {
  const isEditing = user !== null;
  const createUser = useCreateUser();
  const updateUser = useUpdateUser();
  const [serverError, setServerError] = useState<string | null>(null);

  const form = useForm({
    defaultValues: {
      name: user?.name ?? '',
      email: user?.email ?? '',
      password: '',
      password_confirmation: '',
    },
    validators: { onSubmit: isEditing ? editUserFormSchema : createUserSchema },
    onSubmit: async ({ value }) => {
      setServerError(null);
      try {
        if (isEditing) {
          await updateUser.mutateAsync({ id: user.id, input: { name: value.name, email: value.email } });
          toast.success('User updated.');
        } else {
          await createUser.mutateAsync(value);
          toast.success('User created.');
        }
        onOpenChange(false);
      } catch (error) {
        setServerError(getFormErrorMessage(error));
      }
    },
  });

  // Re-seed the form whenever the dialog opens for a different user.
  useEffect(() => {
    if (open) {
      form.reset({
        name: user?.name ?? '',
        email: user?.email ?? '',
        password: '',
        password_confirmation: '',
      });
      setServerError(null);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open, user]);

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent>
        <DialogTitle>{isEditing ? 'Edit user' : 'New user'}</DialogTitle>
        <DialogDescription>
          {isEditing ? 'Update the user details.' : 'Create a new application user.'}
        </DialogDescription>

        <form
          className="mt-5 space-y-4"
          onSubmit={(e) => {
            e.preventDefault();
            e.stopPropagation();
            void form.handleSubmit();
          }}
        >
          <FormError message={serverError} />

          <form.Field name="name">
            {(field) => (
              <Field>
                <Label htmlFor="user-form-name">Name</Label>
                <Input
                  id="user-form-name"
                  value={field.state.value}
                  onBlur={field.handleBlur}
                  onChange={(e) => field.handleChange(e.target.value)}
                  aria-invalid={field.state.meta.errors.length > 0}
                />
                <FieldError errors={field.state.meta.errors} />
              </Field>
            )}
          </form.Field>

          <form.Field name="email">
            {(field) => (
              <Field>
                <Label htmlFor="user-form-email">Email</Label>
                <Input
                  id="user-form-email"
                  type="email"
                  value={field.state.value}
                  onBlur={field.handleBlur}
                  onChange={(e) => field.handleChange(e.target.value)}
                  aria-invalid={field.state.meta.errors.length > 0}
                />
                <FieldError errors={field.state.meta.errors} />
              </Field>
            )}
          </form.Field>

          {!isEditing && (
            <>
              <form.Field name="password">
                {(field) => (
                  <Field>
                    <Label htmlFor="user-form-password">Password</Label>
                    <PasswordInput
                      id="user-form-password"
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
                    <Label htmlFor="user-form-password-confirmation">Confirm password</Label>
                    <PasswordInput
                      id="user-form-password-confirmation"
                      value={field.state.value}
                      onBlur={field.handleBlur}
                      onChange={(e) => field.handleChange(e.target.value)}
                      aria-invalid={field.state.meta.errors.length > 0}
                    />
                    <FieldError errors={field.state.meta.errors} />
                  </Field>
                )}
              </form.Field>
            </>
          )}

          <div className="flex justify-end gap-2 pt-2">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Cancel
            </Button>
            <form.Subscribe selector={(state) => state.isSubmitting}>
              {(isSubmitting) => (
                <Button type="submit" disabled={isSubmitting}>
                  {isSubmitting && <Loader2 className="animate-spin" />}
                  {isEditing ? 'Save changes' : 'Create user'}
                </Button>
              )}
            </form.Subscribe>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
