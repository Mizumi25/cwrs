<nav x-data="{ open: false }" class="hidden md:flex lg:flex h-full w-[13rem] z-[100] block {{ $mode === 'dark' ? 'bg-[#262837] text-white' : 'bg-gray-100 text-black' }} py-[30px] px-[30px]">
    <!-- Primary Navigation Menu -->
    <div class="hidden sm:block h-screen bg-[#5186E8] rounded-[20px] fixed"> 
    <!-- Hide on small screens -->
        <div class="flex items-center flex-col justify-between h-full w-full relative">
             <a class="bg-white rounded-full m-[3em]" href="{{ route('dashboard') }}" wire:navigate>
               <div>
                  <img src="{{ asset('favicon.ico') }}" alt="Logo" class="h-[70px] w-auto">
              </div>
             </a>
            <div class="flex flex-col justify-evenly items-center h-full w-full absolute">
                <!-- Navigation Links -->
          
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                       <i class="fa-solid fa-gauge text-[1rem]"></i><span>{{ __('Dashboard') }}</span>
                    </x-nav-link>
                
                    <x-nav-link :href="route('reservation.new')" :active="request()->routeIs('reservation.new')" wire:navigate>
                       <i class="fa-solid fa-book text-[1rem]"></i><span>{{ __('Add Reservation') }}</span>
                     </x-nav-link> 
                     
                     <x-nav-link :href="route('reservations.manage')" :active="request()->routeIs('reservations.manage')" wire:navigate>
                       <i class="fa-solid fa-book-open-reader text-[1rem]"></i><span>{{ __('My Reservations') }}</span>
                     </x-nav-link> 
                     
                     <x-nav-link :href="route('vehicles')" :active="request()->routeIs('vehicles')" wire:navigate>
                       <i class="fa-solid fa-car text-[1rem]"></i><span>{{ __('My Vehicles') }}</span>
                     </x-nav-link> 
                   
            </div>
        </div>
    </div>
</nav>
