<x-app-layout>
    <x-slot name="header">
        <h2 class="flex px-[30px] items-center rounded-[10px] h-[3rem] font-semibold text-xl text-gray-800 leading-tight {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }}">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="flex justify-start items-center w-full sm:px-6 lg:px-8">
            <div class="px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} overflow-hidden shadow-sm sm:rounded-lg w-[91%] rounded-[10px]">
                <div class="p-6 w-full">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
