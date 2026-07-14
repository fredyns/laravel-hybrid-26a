import { useEffect, useState } from 'react';
import { useNavigate } from '@tanstack/react-router';
import { useQueryClient } from '@tanstack/react-query';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import { userQueryKey, useUser } from '@/features/auth/useAuth';
import { deleteAccount, updateProfile } from '@/features/settings/settings.api';
import { SettingsSection } from '@/features/settings/SettingsLayout';
import { clearToken } from '@/lib/auth-storage';
import { getFormErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Field, FormError } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';

/** Ported from the Livewire Profile settings page, wired to PUT /api/settings/profile. */
export function ProfilePage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { data: user } = useUser();

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [saving, setSaving] = useState(false);
  const [profileError, setProfileError] = useState<string | null>(null);

  const [deleteOpen, setDeleteOpen] = useState(false);
  const [deletePassword, setDeletePassword] = useState('');
  const [deleting, setDeleting] = useState(false);
  const [deleteError, setDeleteError] = useState<string | null>(null);

  useEffect(() => {
    if (user) {
      setName(user.name);
      setEmail(user.email);
    }
  }, [user]);

  async function handleSave(e: React.FormEvent) {
    e.preventDefault();
    setProfileError(null);
    setSaving(true);
    try {
      await updateProfile({ name, email });
      await queryClient.invalidateQueries({ queryKey: userQueryKey });
      toast.success('Profile updated.');
    } catch (error) {
      setProfileError(getFormErrorMessage(error));
    } finally {
      setSaving(false);
    }
  }

  async function handleDelete() {
    setDeleteError(null);
    setDeleting(true);
    try {
      await deleteAccount(deletePassword);
      clearToken();
      queryClient.clear();
      toast.success('Account deleted.');
      await navigate({ to: '/' });
    } catch (error) {
      setDeleteError(getFormErrorMessage(error));
    } finally {
      setDeleting(false);
    }
  }

  return (
    <div className="space-y-12">
      <SettingsSection title="Profile" description="Update your name and email address">
        <form className="space-y-4" onSubmit={handleSave}>
          <FormError message={profileError} />
          <Field>
            <Label htmlFor="name">Name</Label>
            <Input id="name" value={name} onChange={(e) => setName(e.target.value)} />
          </Field>
          <Field>
            <Label htmlFor="email">Email</Label>
            <Input id="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
          </Field>
          <Button type="submit" disabled={saving}>
            {saving && <Loader2 className="animate-spin" />}
            Save
          </Button>
        </form>
      </SettingsSection>

      <SettingsSection title="Delete account" description="Delete your account and all of its resources">
        <Button variant="destructive" onClick={() => setDeleteOpen(true)}>
          Delete account
        </Button>
      </SettingsSection>

      <Dialog open={deleteOpen} onOpenChange={setDeleteOpen}>
        <DialogContent>
          <DialogTitle>Delete account</DialogTitle>
          <DialogDescription>
            This will permanently delete your account and all of its resources. Enter your password to
            confirm.
          </DialogDescription>

          <div className="mt-5 space-y-4">
            <FormError message={deleteError} />
            <Field>
              <Label htmlFor="delete-account-password">Password</Label>
              <PasswordInput
                id="delete-account-password"
                value={deletePassword}
                onChange={(e) => setDeletePassword(e.target.value)}
              />
            </Field>
            <div className="flex justify-end gap-2">
              <Button type="button" variant="outline" onClick={() => setDeleteOpen(false)}>
                Cancel
              </Button>
              <Button
                type="button"
                variant="destructive"
                disabled={deleting || deletePassword === ''}
                onClick={handleDelete}
              >
                {deleting && <Loader2 className="animate-spin" />}
                Delete account
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
