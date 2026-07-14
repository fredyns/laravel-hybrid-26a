import { useState } from 'react';
import { useForm } from '@tanstack/react-form';
import { Link, useNavigate } from '@tanstack/react-router';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import { loginSchema } from '@/features/auth/auth.schemas';
import { useLogin } from '@/features/auth/useAuth';
import { getFormErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FormError } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';

/** Ported from resources/views/pages/auth/login.blade.php. */
export function LoginPage() {
  const navigate = useNavigate();
  const login = useLogin();
  const [serverError, setServerError] = useState<string | null>(null);
  const [remember, setRemember] = useState(false);

  const form = useForm({
    defaultValues: { email: '', password: '' },
    validators: { onSubmit: loginSchema },
    onSubmit: async ({ value }) => {
      setServerError(null);
      try {
        await login.mutateAsync(value);
        toast.success('Welcome back!');
        await navigate({ to: '/dashboard' });
      } catch (error) {
        setServerError(getFormErrorMessage(error, 'Unable to sign in. Please try again.'));
      }
    },
  });

  return (
    <div>
      <h1 className="text-2xl font-semibold tracking-tight">Welcome back</h1>
      <p className="mt-1.5 text-sm text-muted-foreground">
        Enter your credentials to access your account.
      </p>

      <form
        className="mt-8 space-y-5"
        onSubmit={(e) => {
          e.preventDefault();
          e.stopPropagation();
          void form.handleSubmit();
        }}
      >
        <FormError message={serverError} />

        <form.Field name="email">
          {(field) => (
            <Field>
              <Label htmlFor={field.name}>Email address</Label>
              <Input
                id={field.name}
                name={field.name}
                type="email"
                autoComplete="email"
                placeholder="email@example.com"
                value={field.state.value}
                onBlur={field.handleBlur}
                onChange={(e) => field.handleChange(e.target.value)}
                aria-invalid={field.state.meta.errors.length > 0}
              />
              <FieldError errors={field.state.meta.errors} />
            </Field>
          )}
        </form.Field>

        <form.Field name="password">
          {(field) => (
            <Field>
              <div className="flex items-center justify-between">
                <Label htmlFor={field.name}>Password</Label>
                <button
                  type="button"
                  onClick={() => toast.info('Password reset is not available in the API client yet.')}
                  className="cursor-pointer text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                >
                  Forgot your password?
                </button>
              </div>
              <PasswordInput
                id={field.name}
                name={field.name}
                autoComplete="current-password"
                placeholder="Password"
                value={field.state.value}
                onBlur={field.handleBlur}
                onChange={(e) => field.handleChange(e.target.value)}
                aria-invalid={field.state.meta.errors.length > 0}
              />
              <FieldError errors={field.state.meta.errors} />
            </Field>
          )}
        </form.Field>

        <label className="flex cursor-pointer items-center gap-2 text-sm">
          <input
            type="checkbox"
            checked={remember}
            onChange={(e) => setRemember(e.target.checked)}
            className="size-4 rounded border-zinc-300 accent-indigo-600"
          />
          Remember me
        </label>

        <form.Subscribe selector={(state) => state.isSubmitting}>
          {(isSubmitting) => (
            <Button
              type="submit"
              disabled={isSubmitting}
              className="w-full rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/25 hover:brightness-110"
            >
              {isSubmitting && <Loader2 className="animate-spin" />}
              Log in
            </Button>
          )}
        </form.Subscribe>
      </form>

      <p className="mt-6 text-center text-sm text-muted-foreground">
        Don&apos;t have an account?{' '}
        <Link to="/register" className="font-medium text-indigo-600 hover:underline dark:text-indigo-400">
          Sign up
        </Link>
      </p>
    </div>
  );
}
