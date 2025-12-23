@extends('chat.layout')

@section('content')
<div class="flex flex-col h-full">

    {{-- Header --}}
    <div class="border-b px-6 py-3 font-semibold">
        Conversa com {{ $user->name }}
    </div>

    {{-- Mensagens --}}
    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 bg-gray-50">
        @forelse($messages as $message)
            <div class="{{ $message->user_id === auth()->id() ? 'text-right' : '' }}">
                <div class="text-sm">
                    <span class="font-semibold">{{ $message->user->name }}</span>
                    <span class="text-xs text-gray-400 ml-2">
                        {{ $message->created_at->format('H:i') }}
                    </span>
                </div>

                <div class="text-gray-800 text-sm inline-block bg-white px-3 py-2 rounded">
                    {{ $message->content }}
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Sem mensagens ainda.</p>
        @endforelse
    </div>

    {{-- Input --}}
    <form method="POST"
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
