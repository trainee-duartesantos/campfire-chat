@extends('chat.layout')
<style>
    mark.active-result {
        background-color: #facc15; /* amarelo mais forte */
        outline: 2px solid #f59e0b;
    }
    .day-separator {
        font-size: 0.75rem;
        color: #9ca3af;
        text-transform: capitalize;
        margin: 1.5rem 0;
    }

</style>

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
        {{-- Contador pesquisa --}}
        <div
            id="search-counter"
            class="text-xs text-gray-500 px-6 py-1 hidden"
        >
            0 resultados
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

    <div
        id="new-messages-badge"
        class="hidden cursor-pointer text-xs bg-yellow-200 text-gray-800 px-3 py-1 rounded-full mx-auto mb-2 w-fit shadow"
    >
        Novas mensagens
    </div>


    {{-- MENSAGENS --}}
    <div id="messages"
        class="flex-1 overflow-y-auto px-6 py-4 space-y-4 bg-white">

        @php $lastUserId = null; @endphp

        @forelse($room->messages->reverse() as $message)
            @php
                $isNewGroup = $lastUserId !== $message->user_id;
                $lastUserId = $message->user_id;
            @endphp

            <div class="flex items-start gap-3">
                {{-- Avatar --}}
                <div class="w-8">
                    @if($isNewGroup)
                        <img
                            src="{{ $message->user->avatar_url }}"
                            alt="{{ $message->user->name }}"
                            class="w-8 h-8 rounded-full object-cover mt-1"
                        />
                    @endif
                </div>

                {{-- Conteúdo --}}
                <div class="flex-1">
                    @if($isNewGroup)
                        <div class="text-sm">
                            <span class="font-semibold">
                                {{ $message->user->name }}
                            </span>
                            <span class="text-xs text-gray-400 ml-2">
                                {{ $message->created_at->format('H:i') }}
                            </span>
                        </div>
                    @endif

                    <div class="text-sm text-gray-800">
                        {{ $message->content }}
                    </div>
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
        class="text-xs text-red-400 px-6 pb-2 hidden italic transition-opacity duration-200">
    </div>


    {{-- INPUT --}}
    <form
        id="message-form"
        method="POST"
        action="{{ route('rooms.messages.store', $room) }}"
        class="border-t bg-white px-4 py-3"
    >
        @csrf

        <div class="flex items-center gap-3">

            {{-- AVATAR --}}
            <img
                src="{{ auth()->user()->avatar_url }}"
                class="w-8 h-8 rounded-full"
            />

            {{-- SEARCH ICON --}}
            <button
                type="button"
                id="open-search"
                class="text-gray-400 hover:text-gray-600"
                title="Pesquisar"
            >
                🔍
            </button>

            {{-- MESSAGE INPUT --}}
            <input
                id="message-input"
                name="content"
                type="text"
                placeholder="Escrever mensagem…"
                class="flex-1 border rounded-full px-4 py-2 text-sm"
            />

            {{-- SEARCH INPUT (hidden) --}}
            <input
                id="search-input"
                type="text"
                placeholder="Pesquisar nesta sala…"
                class="flex-1 border rounded-full px-4 py-2 text-sm hidden"
            />

            {{-- CLOSE SEARCH --}}
            <button
                type="button"
                id="close-search"
                class="text-gray-400 hidden"
                title="Fechar pesquisa"
            >
                ❌
            </button>

            {{-- ATTACH --}}
            <button type="button" class="text-gray-400 cursor-default">📎</button>

            <button type="submit" class="hidden"></button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let lastRenderedUserId = null;
    let lastRenderedAt = null;
    let currentSearchTerm = '';
    let isSearchMode = false;
    let searchResults = [];
    let currentResultIndex = -1;
    let lastRenderedDate = null;
    let isAtBottom = true;
    let unreadCount = 0;

    const messages = document.getElementById('messages');
    const form = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');

    const searchInput = document.getElementById('search-input');
    const openSearch = document.getElementById('open-search');
    const closeSearch = document.getElementById('close-search');
    const searchCounter = document.getElementById('search-counter');

    messages.addEventListener('scroll', () => {
        const threshold = 40; // tolerância px
        const position = messages.scrollTop + messages.clientHeight;
        const height = messages.scrollHeight;

        isAtBottom = height - position < threshold;

        if (isAtBottom) {
            hideNewMessagesBadge();
            unreadCount = 0;
        }
    });


    const roomId = {{ $room->id }};
    const currentUserId = {{ auth()->id() }};
    const currentUserName = @json(auth()->user()->name);

    window.listenRoomTyping(roomId, currentUserId);

    function minutesDiff(a, b) {
        return Math.abs((a - b) / 1000 / 60);
    }


    function highlight(text, term) {
        if (!term) return text;

        const escaped = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escaped})`, 'gi');

        return text.replace(
            regex,
            '<mark class="bg-yellow-200 text-gray-900 px-1 rounded">$1</mark>'
        );
    }

    function formatDayLabel(date) {
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        const d = new Date(date);

        if (d.toDateString() === today.toDateString()) {
            return 'Hoje';
        }

        if (d.toDateString() === yesterday.toDateString()) {
            return 'Ontem';
        }

        return d.toLocaleDateString('pt-PT', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    function isNewDay(date) {
        if (!lastRenderedDate) return true;

        const d1 = new Date(date).toDateString();
        const d2 = new Date(lastRenderedDate).toDateString();

        return d1 !== d2;
    }

    function appendDaySeparator(label) {
        const sep = document.createElement('div');
        sep.className = 'day-separator text-center';
        sep.textContent = label;
        messages.appendChild(sep);
    }


    function appendMessage(message) {
        const messageTime = new Date(message.created_at);
        // 📅 Separador de dia
        if (isNewDay(messageTime)) {
            appendDaySeparator(formatDayLabel(messageTime));
            lastRenderedDate = messageTime;
        }
        const isNewGroup =
            lastRenderedUserId !== message.user.id ||
            !lastRenderedAt ||
            minutesDiff(messageTime, lastRenderedAt) >= 5;
            

        lastRenderedUserId = message.user.id;

        const wrapper = document.createElement('div');
        wrapper.classList.add('flex', 'items-start', 'gap-3');

        wrapper.innerHTML = `
            <div class="w-8">
                ${
                    isNewGroup
                        ? `<img src="${message.user.avatar_url}"
                                class="w-8 h-8 rounded-full object-cover mt-1" />`
                        : ''
                }
            </div>

            <div class="flex-1">
                ${
                    isNewGroup
                        ? `<div class="text-sm">
                            <span class="font-semibold">${message.user.name}</span>
                            <span class="text-xs text-gray-400 ml-2">
                                ${new Date(message.created_at).toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </span>
                        </div>`
                        : ''
                }

                <div class="text-sm text-gray-800">
                    ${highlight(message.content, currentSearchTerm)}
                </div>
            </div>
        `;

        messages.appendChild(wrapper);
        if (isAtBottom) {
            messages.scrollTop = messages.scrollHeight;
        } else {
            showNewMessagesBadge();
        }

        lastRenderedUserId = message.user.id;
        lastRenderedAt = messageTime;
    }

    // 👂 OUVIR EVENTOS EM TEMPO REAL (OUTROS USERS)
    window.Echo.private(`room.${roomId}`)
        .listen('.room.message.sent', (e) => {
            if (e.message.user.id === currentUserId) return;
            appendMessage(e.message);
        });

    // 📤 ENVIAR MENSAGEM
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (isSearchMode) {
            return; // 🔒 Campfire-style: não envia durante pesquisa
        }

        const content = messageInput.value.trim();
        if (!content) return;

        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ content })
        });

        const message = await response.json();

        appendMessage(message);
        messageInput.value = '';
    });

    
    let lastTyped = 0;

    messageInput.addEventListener("input", () => {
        if (isSearchMode) return; // 🔒 não envia typing enquanto pesquisa

        const now = Date.now();
        if (now - lastTyped < 600) return;
        lastTyped = now;

        window.Echo.private(`room.${roomId}`).whisper("typing", {
            user_id: currentUserId,
            name: currentUserName,
        });
    });

    let originalMessagesHTML = messages.innerHTML;

    // 🔍 abrir pesquisa
    openSearch.addEventListener('click', () => {
        isSearchMode = true;

        isAtBottom = true;
        hideNewMessagesBadge();
        unreadCount = 0;


        originalMessagesHTML = messages.innerHTML;

        messageInput.classList.add('hidden');
        searchInput.classList.remove('hidden');
        closeSearch.classList.remove('hidden');

        searchInput.focus();
    });


    // ❌ fechar pesquisa
    closeSearch.addEventListener('click', () => {
        isSearchMode = false;
        isAtBottom = true;
        hideNewMessagesBadge();
        unreadCount = 0;

        currentSearchTerm = '';

        searchInput.value = '';
        searchInput.classList.add('hidden');
        closeSearch.classList.add('hidden');
        messageInput.classList.remove('hidden');

        searchResults = [];
        currentResultIndex = -1;
        searchCounter.classList.add('hidden');


        messages.innerHTML = originalMessagesHTML;
        lastRenderedUserId = null;
        lastRenderedAt = null;
        lastRenderedDate = null;
        messages.scrollTop = messages.scrollHeight;

        messageInput.focus();
    });


    // ⌨️ pesquisar em tempo real
    searchInput.addEventListener('input', async () => {
        const q = searchInput.value.trim();
        currentSearchTerm = q;

        if (!q) {
            messages.innerHTML = originalMessagesHTML;
            searchCounter.classList.add('hidden');
            return;
        }

        const res = await fetch(`/rooms/${roomId}/search?q=${encodeURIComponent(q)}`);
        const results = await res.json();

        messages.innerHTML = '';
        lastRenderedUserId = null;
        lastRenderedAt = null;
        lastRenderedDate = null;

        results.forEach(appendMessage);

        // ⏳ esperar DOM render
        requestAnimationFrame(() => {
            searchResults = Array.from(messages.querySelectorAll('mark'));
            currentResultIndex = searchResults.length ? 0 : -1;

            updateActiveResult();
            updateCounter();
        });
    });

    function updateActiveResult() {
        searchResults.forEach(el => el.classList.remove('active-result'));

        if (currentResultIndex < 0 || !searchResults[currentResultIndex]) return;

        const el = searchResults[currentResultIndex];
        el.classList.add('active-result');

        el.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    function updateCounter() {
        if (!searchResults.length) {
            searchCounter.textContent = '0 resultados';
        } else if (searchResults.length === 1) {
            searchCounter.textContent = '1 resultado';
        } else {
            searchCounter.textContent =
                `${currentResultIndex + 1} de ${searchResults.length} resultados`;
        }

        searchCounter.classList.remove('hidden');
    }

    const newMessagesBadge = document.getElementById('new-messages-badge');

    function showNewMessagesBadge() {
        unreadCount++;
        newMessagesBadge.textContent =
            unreadCount === 1
                ? 'Nova mensagem'
                : `${unreadCount} novas mensagens`;

        newMessagesBadge.classList.remove('hidden');
    }

    function hideNewMessagesBadge() {
        newMessagesBadge.classList.add('hidden');
    }


    // ESC fecha pesquisa (Campfire feel 😄)
    searchInput.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;

        e.preventDefault();

        if (!searchResults.length) return;

        if (e.shiftKey) {
            // ⬆ anterior
            currentResultIndex =
                (currentResultIndex - 1 + searchResults.length) %
                searchResults.length;
        } else {
            // ⬇ próximo
            currentResultIndex =
                (currentResultIndex + 1) % searchResults.length;
        }

        updateActiveResult();
        updateCounter();
    });

    newMessagesBadge.addEventListener('click', () => {
        messages.scrollTo({
            top: messages.scrollHeight,
            behavior: 'smooth'
        });

        hideNewMessagesBadge();
        unreadCount = 0;
    });


});
</script>

@endpush
