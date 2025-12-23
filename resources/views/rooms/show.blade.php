@extends('chat.layout')

@section('content')
<div class="flex flex-col h-full">

    {{-- Header da sala --}}
    <div class="border-b bg-white px-6 py-4">
        <h2 class="text-lg font-semibold">
            # {{ $room->name }}
        </h2>
        <p class="text-xs text-gray-500">
            {{ $room->users->count() }} membros
        </p>
    </div>

    {{-- Mensagens --}}
    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 bg-gray-50">
        @forelse($room->messages as $message)
            <div>
                <div class="text-sm">
                    <span class="font-semibold">{{ $message->user->name }}</span>
                    <span class="text-xs text-gray-400 ml-2">
                        {{ $message->created_at->format('H:i') }}
                    </span>
                </div>

                <div class="text-gray-800 text-sm">
                    {{ $message->content }}
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">
                Ainda não há mensagens nesta sala.
            </p>
        @endforelse
    </div>

    {{-- Input --}}
    <form method="POST"
          action="{{ route('rooms.messages.store', $room) }}"
          class="border-t bg-white px-6 py-4">
        @csrf

        <input
            type="text"
            name="content"
            placeholder="Escrever mensagem…"
            class="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring"
            required
            autofocus
        />
    </form>

</div>
@endsection
