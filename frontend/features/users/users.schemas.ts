import { z } from 'zod';

const passwordWithConfirmation = {
  password: z.string().min(8, 'Password must be at least 8 characters'),
  password_confirmation: z.string(),
};

const passwordsMatch = (data: { password: string; password_confirmation: string }) =>
  data.password === data.password_confirmation;

const passwordMismatch = {
  message: 'Passwords do not match',
  path: ['password_confirmation'] as ['password_confirmation'],
};

export const createUserSchema = z
  .object({
    name: z.string().min(1, 'Name is required').max(255),
    email: z.string().email('Enter a valid email address').max(255),
    ...passwordWithConfirmation,
  })
  .refine(passwordsMatch, passwordMismatch);

export const updateUserSchema = z.object({
  name: z.string().min(1, 'Name is required').max(255),
  email: z.string().email('Enter a valid email address').max(255),
});

/**
 * Edit-mode variant of the user form schema: same input shape as
 * createUserSchema so both can drive one form, but the (hidden)
 * password fields are unconstrained.
 */
export const editUserFormSchema = z.object({
  name: z.string().min(1, 'Name is required').max(255),
  email: z.string().email('Enter a valid email address').max(255),
  password: z.string(),
  password_confirmation: z.string(),
});

export const changePasswordSchema = z
  .object(passwordWithConfirmation)
  .refine(passwordsMatch, passwordMismatch);
