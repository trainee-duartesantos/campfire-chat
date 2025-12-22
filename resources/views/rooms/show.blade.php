<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">{{ $room->name }}</h2>
            <a href="{{ route('rooms.index') }}" class="text-sm underline">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        <div class="border rounded-lg p-6">
            <div class="text-gray-600 text-sm mb-2">Membros</div>

            <div class="flex flex-wrap gap-2">
                @foreach($room->users as $user)
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-sm">
                        {{ $user->name }}
                    </span>
                @endforeach
            </div>

            <div class="border rounded-lg p-4 h-96 overflow-y-auto space-y-4">
                @forelse($room->messages as $message)
                    <div>
                        <span class="font-semibold">{{ $message->user->name }}</span>
                        <span class="text-gray-500 text-xs">
                            {{ $message->created_at->format('H:i') }}
                        </span>
                        <p class="text-gray-800">{{ $message->content }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">Ainda não há mensagens nesta sala.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('rooms.messages.store', $room) }}" class="mt-4">
                @csrf

                <div class="flex gap-2">
                    <input
                        type="text"
                        name="content"
                        placeholder="Escreve uma mensagem..."
                        class="flex-1 border rounded px-3 py-2"
                        required
                    />

                    <button class="bg-black text-white px-4 py-2 rounded">
                        Enviar
                    </button>
                </div>

                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </form>

            <div class="mt-6 text-gray-500">
                (Mensagens entram a seguir 👀)
            </div>
        </div>
    </div>
</x-app-layout>
