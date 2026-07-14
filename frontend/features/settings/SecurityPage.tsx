import { useState } from 'react';
import { toast } from 'sonner';
import { KeyRound, Loader2 } from 'lucide-react';
import { updateOwnPassword } from '@/features/settings/settings.api';
import { SettingsSection } from '@/features/settings/SettingsLayout';
import { getFormErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Field, FormError } from '@/components/ui/field';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';

const notWired = () => toast.info('Two-factor and passkeys are managed in the web app.');

/** Ported from the Livewire Security settings page; password form wired to the API. */
export function SecurityPage() {
  const [currentPassword, setCurrentPassword] = useState('');
  const [password, setPassword] = useState('');
  const [confirmation, setConfirmation] = useState('');
  const [saving, setSaving] = useState(false);
  const [serverError, setServerError] = useState<string | null>(null);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setServerError(null);
    if (password !== confirmation) {
      setServerError('Passwords do not match.');
      return;
    }
    setSaving(true);
    try {
      await updateOwnPassword({
        current_password: currentPassword,
        password,
        password_confirmation: confirmation,
      });
      setCurrentPassword('');
      setPassword('');
      setConfirmation('');
      toast.success('Password updated.');
    } catch (error) {
      setServerError(getFormErrorMessage(error));
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="space-y-12">
      <SettingsSection
        title="Update password"
        description="Ensure your account is using a long, random password to stay secure"
      >
        <form className="space-y-4" onSubmit={handleSubmit}>
          <FormError message={serverError} />
          <Field>
            <Label htmlFor="current_password">Current password</Label>
            <PasswordInput
              id="current_password"
              autoComplete="current-password"
              value={currentPassword}
              onChange={(e) => setCurrentPassword(e.target.value)}
            />
          </Field>
          <Field>
            <Label htmlFor="new_password">New password</Label>
            <PasswordInput
              id="new_password"
              autoComplete="new-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
          </Field>
          <Field>
            <Label htmlFor="confirm_password">Confirm password</Label>
            <PasswordInput
              id="confirm_password"
              autoComplete="new-password"
              value={confirmation}
              onChange={(e) => setConfirmation(e.target.value)}
            />
          </Field>
          <Button type="submit" disabled={saving}>
            {saving && <Loader2 className="animate-spin" />}
            Save
          </Button>
        </form>
      </SettingsSection>

      <SettingsSection
        title="Two-factor authentication"
        description="Manage your two-factor authentication settings"
      >
        <p className="max-w-lg text-sm text-muted-foreground">
          When you enable two-factor authentication, you will be prompted for a secure pin during login.
          This pin can be retrieved from a TOTP-supported application on your phone.
        </p>
        <Button onClick={notWired}>Enable 2FA</Button>
      </SettingsSection>

      <SettingsSection title="Passkeys" description="Manage your passkeys for passwordless sign-in">
        <div className="flex flex-col items-center gap-2 rounded-xl border border-zinc-200 px-6 py-10 text-center dark:border-zinc-700">
          <span className="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700">
            <KeyRound className="size-5 text-zinc-400" />
          </span>
          <p className="mt-2 text-sm font-medium">No passkeys yet</p>
          <p className="text-sm text-muted-foreground">Add a passkey to sign in without a password</p>
          <Button className="mt-3" onClick={notWired}>
            Add passkey
          </Button>
        </div>
      </SettingsSection>
    </div>
  );
}
