<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const ROLES_SISTEMA = ['doador', 'funcionario', 'diretor', 'admin'];

    private const PERMISSIONS = [
        'Agendamentos' => [
            'ver_agendamentos'       => 'Ver agendamentos',
            'criar_agendamentos'     => 'Criar agendamentos',
            'confirmar_agendamentos' => 'Confirmar / reabrir agendamentos',
            'cancelar_agendamentos'  => 'Cancelar agendamentos',
        ],
        'Doações' => [
            'ver_doacoes'       => 'Ver doações',
            'registrar_doacoes' => 'Registrar doações',
        ],
        'Triagem' => [
            'ver_triagens'      => 'Ver triagens',
            'registrar_triagem' => 'Registrar triagem clínica',
        ],
        'Estoque' => [
            'ver_estoque'       => 'Ver estoque',
            'gerenciar_estoque' => 'Gerenciar estoque',
        ],
        'Alertas Médicos' => [
            'ver_alertas_medicos'       => 'Ver alertas médicos',
            'gerenciar_alertas_medicos' => 'Criar / editar alertas médicos',
        ],
        'Usuários' => [
            'ver_usuarios'     => 'Ver usuários',
            'criar_usuarios'   => 'Criar usuários',
            'excluir_usuarios' => 'Excluir usuários',
        ],
        'Hemocentros' => [
            'ver_hemocentros'       => 'Ver hemocentros',
            'gerenciar_hemocentros' => 'Gerenciar hemocentros',
        ],
        'Campanhas' => [
            'ver_campanhas'       => 'Ver campanhas',
            'gerenciar_campanhas' => 'Criar / editar campanhas',
            'disparar_campanhas'  => 'Disparar campanhas',
        ],
        'Estatísticas' => [
            'ver_estatisticas_hemocentro' => 'Ver estatísticas do hemocentro',
            'ver_estatisticas_globais'    => 'Ver estatísticas globais',
        ],
        'Relatórios' => [
            'exportar_relatorios' => 'Exportar relatórios PDF',
        ],
    ];

    public function permissions()
    {
        return response()->json(self::PERMISSIONS);
    }

    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->orderBy('id')->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'guard_name'  => $r->guard_name,
                'users_count' => $r->users_count,
                'sistema'     => in_array($r->name, self::ROLES_SISTEMA),
                'permissions' => $r->permissions->pluck('name')->values(),
            ]);

        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:50|unique:roles,name',
            'permissions'   => 'array',
            'permissions.*' => 'string',
        ]);

        $role = Role::create([
            'name'       => strtolower(trim($request->name)),
            'guard_name' => 'api',
        ]);

        if (!empty($request->permissions)) {
            $this->ensurePermissionsExist($request->permissions);
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'id'          => $role->id,
            'name'        => $role->name,
            'guard_name'  => $role->guard_name,
            'users_count' => 0,
            'sistema'     => false,
            'permissions' => collect($request->permissions ?? [])->values(),
        ], 201);
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, self::ROLES_SISTEMA)) {
            // Roles do sistema só podem ter permissões atualizadas, não renomeadas
            if ($request->has('permissions')) {
                $this->ensurePermissionsExist($request->permissions);
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'id'          => $role->id,
                'name'        => $role->name,
                'guard_name'  => $role->guard_name,
                'users_count' => $role->users()->count(),
                'sistema'     => true,
                'permissions' => $role->permissions->pluck('name')->values(),
            ]);
        }

        $request->validate([
            'name'          => 'required|string|max:50|unique:roles,name,' . $role->id,
            'permissions'   => 'array',
            'permissions.*' => 'string',
        ]);

        $role->update(['name' => strtolower(trim($request->name))]);

        if ($request->has('permissions')) {
            $this->ensurePermissionsExist($request->permissions);
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'id'          => $role->id,
            'name'        => $role->name,
            'guard_name'  => $role->guard_name,
            'users_count' => $role->users()->count(),
            'sistema'     => false,
            'permissions' => $role->permissions->pluck('name')->values(),
        ]);
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, self::ROLES_SISTEMA)) {
            return response()->json(['message' => 'Roles do sistema não podem ser removidas.'], 422);
        }

        if ($role->users()->exists()) {
            return response()->json(['message' => 'Role possui usuários vinculados. Remova-os antes de excluir.'], 422);
        }

        $role->delete();

        return response()->json(null, 204);
    }

    private function ensurePermissionsExist(array $names): void
    {
        foreach ($names as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'api']);
        }
    }
}
