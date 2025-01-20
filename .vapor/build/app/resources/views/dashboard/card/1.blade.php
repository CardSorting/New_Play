<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Card Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center items-center min-h-[calc(100vh-200px)] bg-gradient-to-br from-gray-900 to-gray-800 p-4 rounded-lg">
                <div class="w-[375px]">
                    <livewire:card-display :card="$card" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
