@extends('chat.layout')

@section('content')
<div class="flex flex-col h-full">

    {{-- HEADER DA SALA --}}
    <div class="border-b bg-white px-6 py-3 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold">
                # {{ $room->name }}
            </h2>
            <p class="text-xs text-gray-500">
                {{ $room->users->count() }} membros
            </p>
        </div>

        {{-- AÇÕES ADMIN --}}
        @can('manage', $room)
            <div class="flex items-center gap-3">

                {{-- Dropdown simples --}}
                <details class="relative">
                    <summary class="cursor-pointer text-sm text-gray-600 hover:text-black">
                        Gerir
                    </summary>

                    <div class="absolute right-0 mt-2 w-56 bg-white border rounded shadow z-10 p-3 space-y-2">

                        {{-- ADICIONAR MEMBRO --}}
                        <form method="POST"
                              action="{{ route('rooms.members.add', $room) }}">
                            @csrf

                            <select name="user_id"
                                    class="w-full border rounded text-sm p-1"
                                    required>
                                
                                <option value="" disabled selected>
                                    Selecionar utilizador…
                                </option>

                                @foreach($users as $user)
                                    @if(!$room->users->contains($user))
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>


                            <button class="mt-2 w-full text-xs bg-black text-black py-1 rounded">
                                Adicionar membro
                            </button>
                        </form>

                        <hr>

                        {{-- APAGAR SALA --}}
                        <form method="POST"
                              action="{{ route('rooms.destroy', $room) }}"
                              onsubmit="return confirm('Apagar esta sala?')">
                            @csrf
                            @method('DELETE')

                            <button class="w-full text-xs text-red-600 hover:underline">
                                Apagar sala
                            </button>
                        </form>

                    </div>
                </details>

            </div>
        @endcan
    </div>

    {{-- MEMBROS DA SALA --}}
    <div class="border-b bg-white px-6 py-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">
            Membros
        </h3>

        <ul class="flex flex-wrap gap-4">
            @foreach($room->users as $member)
                <li class="flex items-center gap-2 text-sm">

                    {{-- Estado --}}
                    <span
                        class="inline-block w-2 h-2 rounded-full
                        {{ $member->status === 'online' ? 'bg-green-500' : 'bg-red-400' }}">
                    </span>

                    {{-- Nome --}}
                    <span class="{{ $member->id === auth()->id() ? 'font-semibold' : '' }}">
                        {{ $member->name }}
                    </span>

                    <span class="text-xs text-gray-400">
                        ({{ $member->status }})
                    </span>
                    {{-- Badge criador --}}
                    @if($member->id === $room->created_by)
                        <span class="text-xs text-gray-400">(admin)</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>



    {{-- MENSAGENS --}}
    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 bg-gray-50">

        @forelse($room->messages->reverse() as $message)
            <div>
                <div class="text-sm">
                    <span class="font-semibold">
                        {{ $message->user->name }}
                    </span>
                    <span class="text-xs text-gray-400 ml-2">
                        {{ $message->created_at->format('H:i') }}
                    </span>
                </div>

                <div class="text-sm text-gray-800">
                    {{ $message->content }}
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">
                Ainda não há mensagens nesta sala.
            </p>
        @endforelse

    </div>

    {{-- INDICADOR "A ESCREVER" --}}
    <div
        id="typing-indicator"
        class="text-xs text-gray-400 px-6 pb-2 hidden">
    </div>


    {{-- INPUT --}}
    <form method="POST"
          action="{{ route('rooms.messages.store', $room) }}"
          class="border-t bg-white px-6 py-4">
        @csrf

        <input
            id="message-input"
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

@push('scripts')
<script>
    const conversationId = {{ $room->id }};
    const currentUserId = {{ auth()->id() }};

    // ouvir typing via Echo
    window.listenTyping(conversationId, currentUserId);

    const input = document.getElementById('message-input');
    let typingTimer = null;

    input.addEventListener('input', () => {
        clearTimeout(typingTimer);

        fetch('/typing', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                conversation_id: conversationId
            })
        });

        typingTimer = setTimeout(() => {
            // apenas debounce
        }, 1500);
    });
</script>
@endpush
