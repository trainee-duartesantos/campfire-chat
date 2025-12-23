<x-app-layout>
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <div class="w-64 border-r bg-gray-100">
            @include('chat.sidebar')
        </div>

        {{-- Conteúdo do Chat --}}
        <div class="flex-1 flex flex-col">
            @yield('content')
        </div>

    </div>
</x-app-layout>
