<x-app-layout>
    <div class="flex h-[calc(100vh-4rem)] bg-gray-100">

        {{-- Sidebar --}}
        <div class="w-72 border-r bg-white">
            @include('chat.sidebar')
        </div>

        {{-- Conteúdo do Chat --}}
        <div class="flex-1 flex flex-col">
            @yield('content')
        </div>

    </div>
</x-app-layout>
