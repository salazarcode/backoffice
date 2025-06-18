<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

/**
 * Componente principal para la gestión de usuarios en el panel administrativo.
 * Permite listar, crear, editar y eliminar usuarios, mostrando modals de confirmación y mensajes de éxito/error.
 */
class UserManagement extends Component
{
    use WithFileUploads;
    // Lista de usuarios
    public $users;
    // Datos del usuario en edición/creación
    public $userForm = [
        'name' => '',
        'email' => '',
        'password' => '',
    ];
    // ID del usuario seleccionado para editar/eliminar
    public $selectedUserId = null;
    // Control de visibilidad de modals
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $showCreateModal = false;
    // Mensajes de feedback
    public $successMessage = '';
    public $errorMessage = '';
    // Cambios detectados para mostrar en el modal de actualización
    public $changes = [];
    // Imagen de perfil temporal
    public $photo;

    /**
     * Monta el componente y carga los usuarios.
     */
    public function mount()
    {
        $this->loadUsers();
    }

    /**
     * Carga todos los usuarios desde la base de datos.
     */
    public function loadUsers()
    {
        $this->users = User::all();
    }

    /**
     * Muestra el modal para crear un usuario.
     */
    public function showCreateUserModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    /**
     * Crea un nuevo usuario y muestra mensaje de éxito o error.
     */
    public function createUser()
    {
        $this->validate([
            'userForm.name' => 'required|string|max:255',
            'userForm.email' => 'required|email|unique:users,email',
            'userForm.password' => 'required|string|min:6',
            'photo' => 'nullable|image|max:2048',
        ]);
        try {
            $profilePhotoPath = null;
            if ($this->photo) {
                $profilePhotoPath = $this->photo->store('profile-photos', 'public');
            }
            User::create([
                'name' => $this->userForm['name'],
                'email' => $this->userForm['email'],
                'password' => Hash::make($this->userForm['password']),
                'profile_photo_path' => $profilePhotoPath,
            ]);
            $this->successMessage = 'Usuario creado exitosamente.';
            $this->showCreateModal = false;
            $this->loadUsers();
            $this->photo = null;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al crear usuario: ' . $e->getMessage();
        }
    }

    /**
     * Muestra el modal para editar un usuario y detecta cambios.
     */
    public function showEditUserModal($userId)
    {
        $user = User::findOrFail($userId);
        $this->selectedUserId = $userId;
        $this->userForm = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => '', // No se muestra el password actual
        ];
        $this->photo = null;
        $this->changes = [];
        $this->showEditModal = true;
    }

    /**
     * Detecta cambios entre los datos originales y los editados.
     */
    public function updatedUserForm($value, $key)
    {
        $user = User::find($this->selectedUserId);
        if ($user && $user->$key !== $value && $key !== 'password') {
            $this->changes[$key] = ['old' => $user->$key, 'new' => $value];
        } elseif ($key === 'password' && $value !== '') {
            $this->changes[$key] = ['old' => '********', 'new' => '********'];
        } else {
            unset($this->changes[$key]);
        }
    }

    /**
     * Actualiza el usuario seleccionado y muestra los cambios en un modal de confirmación.
     */
    public function updateUser()
    {
        $this->validate([
            'userForm.name' => 'required|string|max:255',
            'userForm.email' => 'required|email|unique:users,email,' . $this->selectedUserId,
            'photo' => 'nullable|image|max:2048',
        ]);
        try {
            $user = User::findOrFail($this->selectedUserId);
            $user->name = $this->userForm['name'];
            $user->email = $this->userForm['email'];
            if (!empty($this->userForm['password'])) {
                $user->password = Hash::make($this->userForm['password']);
            }
            if ($this->photo) {
                $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
            }
            $user->save();
            $this->successMessage = 'Usuario actualizado exitosamente.';
            $this->showEditModal = false;
            $this->loadUsers();
            $this->photo = null;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al actualizar usuario: ' . $e->getMessage();
        }
    }

    /**
     * Muestra el modal de confirmación para eliminar un usuario.
     */
    public function showDeleteUserModal($userId)
    {
        $this->selectedUserId = $userId;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el usuario seleccionado y muestra mensaje de éxito o error.
     */
    public function deleteUser()
    {
        try {
            User::destroy($this->selectedUserId);
            $this->successMessage = 'Usuario eliminado exitosamente.';
            $this->showDeleteModal = false;
            $this->loadUsers();
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al eliminar usuario: ' . $e->getMessage();
        }
    }

    /**
     * Cierra todos los modals activos.
     */
    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
    }

    /**
     * Resetea el formulario de usuario y cierra los modals.
     */
    public function resetForm()
    {
        $this->userForm = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];
        $this->selectedUserId = null;
        $this->changes = [];
        $this->successMessage = '';
        $this->errorMessage = '';
        $this->photo = null;
        $this->closeModals();
    }

    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        return view('livewire.user-management');
    }
}
