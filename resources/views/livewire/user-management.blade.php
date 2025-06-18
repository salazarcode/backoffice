{{--
    Vista principal para la gestión de usuarios en el panel administrativo.
    Incluye listado, formularios, modals de confirmación y mensajes de feedback.
--}}
<div>
    {{-- Mensajes de éxito o error --}}
    @if ($successMessage)
        <div class="bg-green-100 text-green-800 p-2 mb-2 rounded">{{ $successMessage }}</div>
    @endif
    @if ($errorMessage)
        <div class="bg-red-100 text-red-800 p-2 mb-2 rounded">{{ $errorMessage }}</div>
    @endif

    {{-- Botón para crear usuario --}}
    <button wire:click="showCreateUserModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Agregar Usuario</button>

    {{-- Tabla de usuarios --}}
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="py-2 px-4 border">Nombre</th>
                <th class="py-2 px-4 border">Email</th>
                <th class="py-2 px-4 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="py-2 px-4 border">{{ $user->name }}</td>
                    <td class="py-2 px-4 border">{{ $user->email }}</td>
                    <td class="py-2 px-4 border">
                        <button wire:click="showEditUserModal({{ $user->id }})" class="bg-yellow-400 text-white px-2 py-1 rounded">Editar</button>
                        <button wire:click="showDeleteUserModal({{ $user->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Modal para crear usuario --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" 
             x-data 
             x-init="
                document.body.style.overflow = 'hidden';
                $el.addEventListener('click', (e) => {
                    if (e.target === $el) {
                        $wire.closeModals();
                    }
                });
             "
             x-effect="if (!$wire.showCreateModal) document.body.style.overflow = ''">
            <div class="bg-white p-6 rounded shadow-lg w-96" @click.stop>
                <h2 class="text-lg font-bold mb-4">Agregar Usuario</h2>
                <form wire:submit.prevent="createUser" enctype="multipart/form-data">
                    <div class="mb-2">
                        <label>Nombre</label>
                        <input type="text" wire:model.defer="userForm.name" class="w-full border rounded px-2 py-1" />
                        @error('userForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" wire:model.defer="userForm.email" class="w-full border rounded px-2 py-1" />
                        @error('userForm.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label>Contraseña</label>
                        <input type="password" wire:model.defer="userForm.password" class="w-full border rounded px-2 py-1" />
                        @error('userForm.password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label>Foto de perfil</label>
                        <input type="file" wire:model="photo" class="w-full border rounded px-2 py-1" />
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="h-16 w-16 rounded-full mt-2" />
                        @endif
                        @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" wire:click="closeModals" class="bg-gray-300 px-3 py-1 rounded">Cancelar</button>
                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal para editar usuario y mostrar cambios --}}
    @if ($showEditModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
             x-data
             x-init="
                document.body.style.overflow = 'hidden';
                $el.addEventListener('click', (e) => {
                    if (e.target === $el) {
                        $wire.closeModals();
                    }
                });
             "
             x-effect="if (!$wire.showEditModal) document.body.style.overflow = ''">
            <div class="bg-white p-6 rounded shadow-lg w-96" @click.stop>
                <h2 class="text-lg font-bold mb-2">Editar Usuario</h2>
                <form wire:submit.prevent="updateUser" enctype="multipart/form-data">
                    <div class="mb-2">
                        <label>Nombre</label>
                        <input type="text" wire:model.defer="userForm.name" class="w-full border rounded px-2 py-1" />
                        @error('userForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" wire:model.defer="userForm.email" class="w-full border rounded px-2 py-1" />
                        @error('userForm.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label>Nueva Contraseña (opcional)</label>
                        <input type="password" wire:model.defer="userForm.password" class="w-full border rounded px-2 py-1" />
                        @error('userForm.password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label>Foto de perfil</label>
                        <input type="file" wire:model="photo" class="w-full border rounded px-2 py-1" />
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="h-16 w-16 rounded-full mt-2" />
                        @elseif ($selectedUserId)
                            @php $user = $users->find($selectedUserId); @endphp
                            @if ($user && $user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" class="h-16 w-16 rounded-full mt-2" />
                            @endif
                        @endif
                        @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    {{-- Mostrar cambios detectados --}}
                    @if ($changes)
                        <div class="bg-yellow-100 text-yellow-800 p-2 rounded mb-2">
                            <strong>Cambios a guardar:</strong>
                            <ul class="text-xs">
                                @foreach ($changes as $field => $change)
                                    <li>{{ ucfirst($field) }}: <span class="line-through">{{ $change['old'] }}</span> → <span class="font-bold">{{ $change['new'] }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" wire:click="closeModals" class="bg-gray-300 px-3 py-1 rounded">Cancelar</button>
                        <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal de confirmación para eliminar usuario --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
             x-data
             x-init="
                document.body.style.overflow = 'hidden';
                $el.addEventListener('click', (e) => {
                    if (e.target === $el) {
                        $wire.closeModals();
                    }
                });
             "
             x-effect="if (!$wire.showDeleteModal) document.body.style.overflow = ''">
            <div class="bg-white p-6 rounded shadow-lg w-96" @click.stop>
                <h2 class="text-lg font-bold mb-4">Confirmar Eliminación</h2>
                <p>¿Estás seguro de que deseas eliminar este usuario?</p>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" wire:click="closeModals" class="bg-gray-300 px-3 py-1 rounded">Cancelar</button>
                    <button type="button" wire:click="deleteUser" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
