import { keepPreviousData, useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import {
  changeUserPassword,
  createUser,
  deleteUser,
  fetchUsers,
  updateUser,
  type ChangePasswordInput,
  type CreateUserInput,
  type UpdateUserInput,
  type UsersListParams,
} from '@/features/users/users.api';

const USERS_KEY = 'users';

export function useUsersList(params: UsersListParams) {
  return useQuery({
    queryKey: [USERS_KEY, params],
    queryFn: () => fetchUsers(params),
    placeholderData: keepPreviousData,
  });
}

function useInvalidateUsers() {
  const queryClient = useQueryClient();
  return () => queryClient.invalidateQueries({ queryKey: [USERS_KEY] });
}

export function useCreateUser() {
  const invalidate = useInvalidateUsers();
  return useMutation({
    mutationFn: (input: CreateUserInput) => createUser(input),
    onSuccess: invalidate,
  });
}

export function useUpdateUser() {
  const invalidate = useInvalidateUsers();
  return useMutation({
    mutationFn: ({ id, input }: { id: string; input: UpdateUserInput }) => updateUser(id, input),
    onSuccess: invalidate,
  });
}

export function useDeleteUser() {
  const invalidate = useInvalidateUsers();
  return useMutation({
    mutationFn: (id: string) => deleteUser(id),
    onSuccess: invalidate,
  });
}

export function useChangeUserPassword() {
  return useMutation({
    mutationFn: ({ id, input }: { id: string; input: ChangePasswordInput }) =>
      changeUserPassword(id, input),
  });
}
