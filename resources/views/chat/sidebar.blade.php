<div class="p-4 space-y-6">

    {{-- SALAS --}}
    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">
            Salas
        </h3>

        @forelse(auth()->user()->rooms as $room)
            <a href="{{ route('rooms.show', $room) }}"
               class="block px-3 py-2 rounded hover:bg-gray-100 text-sm">
                # {{ $room->name }}
            </a>
        @empty
            <p class="text-xs text-gray-400">Sem salas</p>
        @endforelse
    </div>

    {{-- MENSAGENS DIRETAS --}}
    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">
            Mensagens
        </h3>

        @foreach(\App\Models\User::where('id','!=',auth()->id())->get() as $user)
            <a href="{{ route('messages.direct.show', $user) }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100 text-sm">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                {{ $user->name }}
            </a>
        @endforeach
    </div>

</div>
