{{--
    Vista de la sección administrativa de usuarios.
    Incluye el componente Livewire principal para gestión CRUD.
    Documentación: Esta vista debe estar protegida por middleware de autenticación y rol de administrador.
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @livewire('user-management')
    </div>
</x-app-layout>
