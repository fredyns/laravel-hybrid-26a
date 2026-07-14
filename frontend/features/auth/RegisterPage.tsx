import { useState } from 'react';
import { useForm } from '@tanstack/react-form';
import { Link, useNavigate } from '@tanstack/react-router';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';
import { registerSchema } from '@/features/auth/auth.schemas';
import { useRegister } from '@/features/auth/useAuth';
import { getFormErrorMessage } from '@/lib/api';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FormError } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';

/** Ported from resources/views/pages/auth/register.blade.php. */
export function RegisterPage() {
  const navigate = useNavigate();
  const register = useRegister();
  const [serverError, setServerError] = useState<string | null>(null);

  const form = useForm({
    defaultValues: {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    },
    validators: { onSubmit: registerSchema },
    onSubmit: async ({ value }) => {
      setServerError(null);
      try {
        await register.mutateAsync(value);
        toast.success('Account created!');
        await navigate({ to: '/dashboard' });
      } catch (error) {
        setServerError(getFormErrorMessage(error, 'Unable to register. Please try again.'));
      }
    },
  });

  return (
    <div>
      <h1 className="text-2xl font-semibold tracking-tight">Create an account</h1>
      <p className="mt-1.5 text-sm text-muted-foreground">
        Enter your details below to get started.
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

        <form.Field name="name">
          {(field) => (
            <Field>
              <Label htmlFor={field.name}>Name</Label>
              <Input
                id={field.name}
                name={field.name}
                autoComplete="name"
                placeholder="Full name"
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
              <Label htmlFor={field.name}>Password</Label>
              <PasswordInput
                id={field.name}
                name={field.name}
                autoComplete="new-password"
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

        <form.Field name="password_confirmation">
          {(field) => (
            <Field>
              <Label htmlFor={field.name}>Confirm password</Label>
              <PasswordInput
                id={field.name}
                name={field.name}
                autoComplete="new-password"
                placeholder="Confirm password"
                value={field.state.value}
                onBlur={field.handleBlur}
                onChange={(e) => field.handleChange(e.target.value)}
                aria-invalid={field.state.meta.errors.length > 0}
              />
              <FieldError errors={field.state.meta.errors} />
            </Field>
          )}
        </form.Field>

        <form.Subscribe selector={(state) => state.isSubmitting}>
          {(isSubmitting) => (
            <Button
              type="submit"
              disabled={isSubmitting}
              className="w-full rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/25 hover:brightness-110"
            >
              {isSubmitting && <Loader2 className="animate-spin" />}
              Create account
            </Button>
          )}
        </form.Subscribe>
      </form>

      <p className="mt-6 text-center text-sm text-muted-foreground">
        Already have an account?{' '}
        <Link to="/login" className="font-medium text-indigo-600 hover:underline dark:text-indigo-400">
          Log in
        </Link>
      </p>
    </div>
  );
}
