<nav x-data="{ open: false }" class="hidden md:flex lg:flex bg-white  h-full w-[15rem] border z-[100] block">
    <!-- Primary Navigation Menu -->
    <div class="hidden sm:block w-full h-screen"> 
    <!-- Hide on small screens -->
        <div class="flex items-center flex-col justify-between h-full w-full relative">
             <a href="{{ route('dashboard') }}" wire:navigate>
                   <span>
                       <h1 class="text-1xl text-center text-black lg:block md:block hidden">Car Wash Reservation</h1>
                   </span>
             </a>
            <div class="flex flex-col justify-evenly items-center h-full w-full ">
                <!-- Navigation Links -->
          
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                       <i class="fa-solid fa-gauge text-[2rem]"></i><span>{{ __('Dashboard') }}</span>
                    </x-nav-link>
                
                    <x-nav-link :href="route('reservation.new')" :active="request()->routeIs('reservation.new')" wire:navigate>
                       <i class="fa-solid fa-book text-[2rem]"></i><span>{{ __('Add Reservation') }}</span>
                     </x-nav-link> 
                     
                     <x-nav-link :href="route('reservations.manage')" :active="request()->routeIs('reservations.manage')" wire:navigate>
                       <i class="fa-solid fa-book-open-reader text-[2rem]"></i><span>{{ __('My Reservations') }}</span>
                     </x-nav-link> 
                     
                     <x-nav-link :href="route('vehicles')" :active="request()->routeIs('vehicles')" wire:navigate>
                       <i class="fa-solid fa-car text-[2rem]"></i><span>{{ __('My Vehicles') }}</span>
                     </x-nav-link> 
                   
            </div>
        </div>
    </div>
</nav>
