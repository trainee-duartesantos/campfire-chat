<div class="p-4 space-y-6 text-sm">

    {{-- Utilizador --}}
    <div class="font-semibold">
        {{ auth()->user()->name }}
    </div>

    {{-- Salas --}}
    <div>
        <h4 class="text-xs uppercase text-gray-500 mb-2">Salas</h4>

        <ul class="space-y-1">
            @foreach(auth()->user()->rooms as $room)
                <li>
                    <a href="{{ route('rooms.show', $room) }}"
                       class="block px-2 py-1 rounded hover:bg-gray-200">
                        # {{ $room->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Mensagens diretas --}}
    <div>
        <h4 class="text-xs uppercase text-gray-500 mb-2">Mensagens Diretas</h4>

        <ul class="space-y-1">
            @foreach(\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                <li>
                    <a href="{{ route('messages.direct.show', $user) }}"
                       class="block px-2 py-1 rounded hover:bg-gray-200">
                        {{ $user->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

</div>
