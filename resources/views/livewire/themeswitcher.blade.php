<?php

use Livewire\Volt\Component;

new class extends Component {
    public $theme;

    public function mount()
    {
        $this->theme = session()->get('theme', 'system');
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
        session()->put('theme', $theme);
        $this->dispatch('theme-updated', ['theme' => $this->theme]);
    }
};?>

<div x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" 
     x-bind:class="{ 'dark': darkMode }" 
     x-init="
        if (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            localStorage.setItem('theme', 'dark');
            darkMode = true;
        } else {
            darkMode = localStorage.getItem('theme') === 'dark';
        }
        $watch('darkMode', value => {
            localStorage.setItem('theme', value ? 'dark' : 'light');
            // Dispatch event to Livewire
            $wire.setTheme(value ? 'dark' : 'light');
        });
    ">
    
    <div class="fi-theme-switcher grid grid-flow-col gap-x-1">
        <button
            aria-label="Light Theme"
            type="button"
            x-on:click="darkMode = false"
            class="fi-theme-switcher-btn flex justify-center rounded-md p-2 outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
        >
            <x-filament::icon alias="panels::theme-switcher.light-button" icon="heroicon-m-sun" class="h-5 w-5" />
        </button>

        <button
            aria-label="Dark Theme"
            type="button"
            x-on:click="darkMode = true"
            class="fi-theme-switcher-btn flex justify-center rounded-md p-2 outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
        >
            <x-filament::icon alias="panels::theme-switcher.dark-button" icon="heroicon-m-moon" class="h-5 w-5" />
        </button>

        <button
            aria-label="System Theme"
            type="button"
            x-on:click="
                darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                localStorage.removeItem('theme'); // Reset to system preference
                $wire.setTheme('system');
            "
            class="fi-theme-switcher-btn flex justify-center rounded-md p-2 outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
        >
            <x-filament::icon alias="panels::theme-switcher.system-button" icon="heroicon-m-computer-desktop" class="h-5 w-5" />
        </button>
    </div>
    
    <script>
        // Listen for the theme change events
        window.addEventListener('theme-updated', (event) => {
            const newTheme = event.detail.theme;
            console.log("Theme updated to:", newTheme);
            // Update the local storage if needed
            localStorage.setItem('theme', newTheme);
        });
    </script>
</div>