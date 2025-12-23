@extends('chat.layout')

@section('content')
<div class="flex flex-col h-full">

    {{-- Header da conversa --}}
    <div class="border-b bg-white px-6 py-4">
        <h2 class="text-lg font-semibold">
            {{ $user->name }}
        </h2>
        <p class="text-xs text-gray-500">
            Mensagem direta
        </p>
    </div>

    {{-- Mensagens --}}
    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 bg-gray-50">
        @forelse($messages as $message)
            <div class="{{ $message->user_id === auth()->id() ? 'text-right' : 'text-left' }}">
                <div class="inline-block max-w-[75%]">

                    <div class="text-xs text-gray-400 mb-1">
                        {{ $message->user->name }}
                        · {{ $message->created_at->format('H:i') }}
                    </div>

                    <div class="px-3 py-2 rounded text-sm
                        {{ $message->user_id === auth()->id()
                            ? 'bg-blue-500 text-white'
                            : 'bg-white border' }}">
                        {{ $message->content }}
                    </div>

                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">
                Ainda não há mensagens.
            </p>
        @endforelse
    </div>

    {{-- Input --}}
    <form method="POST"
          action="{{ route('messages.direct.store', $user) }}"
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
