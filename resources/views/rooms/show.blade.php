@extends('chat.layout')

@section('content')
<div class="flex flex-col h-full">

    {{-- Header --}}
    <div class="border-b px-6 py-3 font-semibold">
        # {{ $room->name }}
    </div>

    @can('manage', $room)
        <form method="POST"
            action="{{ route('rooms.destroy', $room) }}"
            onsubmit="return confirm('Apagar esta sala?')">
            @csrf
            @method('DELETE')

            <button class="text-red-600 text-sm mt-4">
                Apagar sala
            </button>
        </form>
    @endcan


    @can('manage', $room)
        <div class="mb-4 border p-3 rounded bg-gray-50">
            <h4 class="font-semibold mb-2">Adicionar membro</h4>

            <form method="POST" action="{{ route('rooms.members.add', $room) }}">
                @csrf

                <select name="user_id" class="border rounded p-2 w-full">
                    @foreach($users as $user)
                        @if(!$room->users->contains($user))
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>

                <button class="mt-2 text-sm text-blue-600">
                    Adicionar
                </button>
            </form>
        </div>
    @endcan

    <h3 class="font-semibold mt-4">Membros</h3>

    <ul class="space-y-1">
    @foreach($room->users as $member)
        <li class="flex justify-between items-center">
            <span>{{ $member->name }}</span>

            @can('manage', $room)
                @if($member->id !== $room->created_by)
                    <form method="POST"
                        action="{{ route('rooms.members.remove', [$room, $member]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-500 text-xs">Remover</button>
                    </form>
                @endif
            @endcan
        </li>
    @endforeach
    </ul>



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
            <p class="text-sm text-gray-400">Ainda não há mensagens.</p>
        @endforelse
    </div>

    {{-- Input --}}
    <form method="POST"
          action="{{ route('rooms.messages.store', $room) }}"
          class="border-t bg-white px-6 py-4">
        @csrf
        <input type="text"
               name="content"
               placeholder="Escrever mensagem…"
               class="w-full border rounded px-4 py-2 text-sm"
               required>
    </form>

</div>
@endsection
