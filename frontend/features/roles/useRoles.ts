import { keepPreviousData, useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import {
  createRole,
  deleteRole,
  fetchPermissionGroups,
  fetchRoleById,
  fetchRoles,
  updateRole,
  type GuardName,
  type RoleInput,
  type RolesListParams,
} from '@/features/roles/roles.api';

const ROLES_KEY = 'roles';

export function useRolesList(params: RolesListParams) {
  return useQuery({
    queryKey: [ROLES_KEY, params],
    queryFn: () => fetchRoles(params),
    placeholderData: keepPreviousData,
  });
}

export function useRole(id: string) {
  return useQuery({
    queryKey: [ROLES_KEY, 'detail', id],
    queryFn: () => fetchRoleById(id),
  });
}

export function usePermissionGroups(guard: GuardName) {
  return useQuery({
    queryKey: ['permissions', guard],
    queryFn: () => fetchPermissionGroups(guard),
    staleTime: 5 * 60_000,
  });
}

function useInvalidateRoles() {
  const queryClient = useQueryClient();
  return () => queryClient.invalidateQueries({ queryKey: [ROLES_KEY] });
}

export function useCreateRole() {
  const invalidate = useInvalidateRoles();
  return useMutation({
    mutationFn: (input: RoleInput) => createRole(input),
    onSuccess: invalidate,
  });
}

export function useUpdateRole() {
  const invalidate = useInvalidateRoles();
  return useMutation({
    mutationFn: ({ id, input }: { id: string; input: RoleInput }) => updateRole(id, input),
    onSuccess: invalidate,
  });
}

export function useDeleteRole() {
  const invalidate = useInvalidateRoles();
  return useMutation({
    mutationFn: (id: string) => deleteRole(id),
    onSuccess: invalidate,
  });
}
