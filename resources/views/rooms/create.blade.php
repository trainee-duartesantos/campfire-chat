<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Criar sala</h2>
    </x-slot>

    <div class="max-w-xl mx-auto py-8">
        <form method="POST" action="{{ route('rooms.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Nome</label>
                <input name="name" class="w-full border rounded p-2" required
                       value="{{ old('name') }}">
                @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Avatar (opcional)</label>
                <input name="avatar" class="w-full border rounded p-2"
                       value="{{ old('avatar') }}">
                @error('avatar') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button class="px-4 py-2 rounded bg-black text-black">
                Criar
            </button>
        </form>
    </div>
</x-app-layout>
