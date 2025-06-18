<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Pruebas de integración para el componente UserManagement de Livewire.
 * Verifica operaciones CRUD y la interacción con los modals y mensajes.
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_un_usuario()
    {
        Livewire::test('user-management')
            ->set('userForm.name', 'Nuevo Usuario')
            ->set('userForm.email', 'nuevo@ejemplo.com')
            ->set('userForm.password', 'password123')
            ->call('createUser')
            ->assertSee('Usuario creado exitosamente.');

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@ejemplo.com',
            'name' => 'Nuevo Usuario',
        ]);
    }

    /** @test */
    public function puede_actualizar_un_usuario()
    {
        $user = User::factory()->create();
        Livewire::test('user-management')
            ->call('showEditUserModal', $user->id)
            ->set('userForm.name', 'Nombre Editado')
            ->call('updateUser')
            ->assertSee('Usuario actualizado exitosamente.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre Editado',
        ]);
    }

    /** @test */
    public function puede_eliminar_un_usuario()
    {
        $user = User::factory()->create();
        Livewire::test('user-management')
            ->call('showDeleteUserModal', $user->id)
            ->call('deleteUser')
            ->assertSee('Usuario eliminado exitosamente.');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function muestra_mensajes_de_error_si_falla_la_creacion()
    {
        // No se envía email ni password
        Livewire::test('user-management')
            ->set('userForm.name', '')
            ->set('userForm.email', '')
            ->set('userForm.password', '')
            ->call('createUser')
            ->assertHasErrors(['userForm.name', 'userForm.email', 'userForm.password']);
    }
}
