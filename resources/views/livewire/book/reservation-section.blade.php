<?php

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Service;
use App\Models\Schedule; 
use App\Models\User; 
use App\Models\Reservation;
use Livewire\Volt\Component;
use Carbon\Carbon; 
use Filament\Notifications\Notification;

new class extends Component
{
    public $vehicleTypes;
    public $service;
    public $selectedServiceId = null;
    public $selectedVehicleTypeId = null;
    public $model = '';
    public $make = '';
    public $year = '';
    public $license_plate = '';
    public $color = '';
    public $mileage = '';
    public $currentDay;
    public $days = [];
    public $availableTimes;
    public $selectedDate;
    public $selectedTime;
    
    public function mount(): void 
{
    $this->vehicleTypes = VehicleType::select('id', 'name', 'description', 'price', 'icon')->get() ?? collect(); 
    $this->service = Service::select('id', 'service_name', 'icon', 'description', 'price', 'duration', 'is_active', 'category', 'popularity')->get() ?? collect(); 
    $this->currentDay = Carbon::now();
    $this->generateDays();
    $this->availableTimes = [
        '08:00 am', '09:00 am', '10:00 am',
        '11:00 am', '12:00 pm', '01:00 pm',
        '02:00 pm', '03:00 pm', '04:00 pm',
        '05:00 pm'
    ];
}
    
    public function selectVehicleType($vehicleTypeId)
    {
        $this->selectedVehicleTypeId = $vehicleTypeId;
    }
    public function selectService($serviceId)
    {
        $this->selectedServiceId = $serviceId;
    }


    public function submitReserve()
{
    $this->validate([
        'selectedVehicleTypeId' => 'required|exists:vehicle_types,id',
        'selectedServiceId' => 'required|exists:services,id',
        'model' => 'required|string',
        'make' => 'required|string',
        'year' => 'required|integer',
        'license_plate' => 'required|string|unique:vehicles,license_plate',
        'color' => 'required|string',
        'mileage' => 'nullable|integer',
        'selectedDate' => 'required|date',
        'selectedTime' => 'required|string',
    ]);

    $timeIn24HourFormat = date('H:i', strtotime($this->selectedTime));

    $vehicle = Vehicle::create([
        'user_id' => auth()->id(),
        'vehicle_type_id' => $this->selectedVehicleTypeId, 
        'model' => $this->model,
        'make' => $this->make,
        'year' => $this->year,
        'license_plate' => $this->license_plate,
        'color' => $this->color,
        'mileage' => $this->mileage,
    ]);
    
    $schedule = Schedule::create([
        'date' => $this->selectedDate,
        'time_slot' => $timeIn24HourFormat, 
    ]);

     $reservation = Reservation::create([
        'user_id' => auth()->id(),
        'vehicle_id' => $vehicle->id,
        'service_id' => $this->selectedServiceId,
        'schedule_id' => $schedule->id, 
        'reservation_date' => Carbon::now(), 
        'status' => 'pending',
    ]);
    
    $currentUser  = auth()->user();
    $service = Service::find($this->selectedServiceId);
    $adminUsers = User::where('role', 'admin')->get(); 

    foreach ($adminUsers as $admin) {
        Notification::make()
            ->title('Reservation of ' . $currentUser ->name . ' check now')
            ->body('A Reservation has reserved at: ' . $reservation->id . 'with' . $service->service_name . '. View Pending Reservations Now.')
             ->sendToDatabase($admin); 
    }

    session()->flash('message', 'Service reserved successfully!');
    $this->reset();
    
    return redirect()->route('reservations.manage');
}
    
    public function generateDays()
    {
        $this->days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $this->currentDay->copy()->addDays($i);
            $this->days[] = $day;
        }
    }

    public function goToNextDays()
    {
        $this->currentDay->addDays(3);
        $this->generateDays();
    }

    public function goToPreviousDays()
    {
        if ($this->currentDay->greaterThan(Carbon::now())) {
            $this->currentDay->subDays(3);
            $this->generateDays();
        }
    }

    public function selectDay($day)
    {
        $this->selectedDate = $day;
        $this->selectedTime = null;
    }

    public function selectTime($time)
    {
        if ($this->selectedDate) {
            $this->selectedTime = $time;
        }
    }
    
    public function getIsSubmitEnabledProperty()
   {
    return !empty($this->selectedVehicleTypeId) &&
           !empty($this->selectedServiceId) &&
           !empty($this->model) &&
           !empty($this->make) &&
           !empty($this->year) &&
           !empty($this->license_plate) &&
           !empty($this->color) &&
           !empty($this->selectedDate) &&
           !empty($this->selectedTime);
   }
};
?>


            <div class="px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} overflow-hidden shadow-sm sm:rounded-lg w-[91%] rounded-[10px]">
                <form wire:submit.prevent="submitReserve">
                    <div class="w-full">
                       <section class="px-4 py-8">
                          <header>
                              <div class="flex flow-row gap-2 items-center">
                                  <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                      <span class="text-white">1/5</span>
                                  </div>
                                  <h2 class="text-lg font-medium text-gray-900">{{ __('Input Vehicle') }}</h2>
                              </div>
                              <p class="mt-1 text-sm text-gray-600">{{ __("Select type and input details") }}</p>
                          </header>
                      
                              <h3 class="text-md font-medium text-gray-900 mb-4">Available Vehicle Types</h3>
                                <div class="grid grid-cols-2 gap-6"> 
                                    @foreach ($vehicleTypes as $type)
                                        <button type="button" 
                                            class="w-full p-4 mb-4 rounded-lg 
                                                   {{ $selectedVehicleTypeId === $type->id ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}"
                                            wire:click="selectVehicleType({{ $type->id }})">
                                            <div class="flex flex-col items-center">
                                                <img src="{{ asset('storage/' . $type->icon) }}" class="h-24 w-24 object-cover" alt="VehicleTypeIcon" />
                                                <p><strong>ID:</strong> {{ $type->id }}</p>
                                                <p><strong>Name:</strong> {{ $type->name }}</p>
                                                <p><strong>Price:</strong> {{ $type->price }}</p>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                              <div class="vehicleInput">
                                  <div class="mb-6">
                                      <label for="model" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Model</label>
                                      <input wire:model.debounce.500ms="model" placeholder="Enter Model" type="text" id="model-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                  </div>
                                  <div class="mb-6">
                                      <label for="make" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Make</label>
                                      <input wire:model.debounce.500ms="make" placeholder="Enter Make" type="text" id="make-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                  </div>
                                  <div class="grid grid-cols-2 gap-10">
                                      <div class="mb-6">
                                          <label for="color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color</label>
                                          <div class="flex items-center">
                                              <input wire:model.debounce.500ms="color" type="color" id="color-input" class="cursor-pointer" style="border: none; padding: 0; width: 40px; height: 40px;">
                                              <input wire:model.debounce.500ms="color" placeholder="Enter Color" type="text" id="color-text-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ml-2">
                                          </div>
                                      </div>
                                       <div class="mb-6">
                                          <label for="license_plate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">License Plate Number</label>
                                          <input wire:model.debounce.500ms="license_plate" placeholder="e.g., ABC1234" type="text" id="license_plate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                          <p class="mt-1 text-xs text-gray-500">Please enter the vehicle's license plate number.</p>
                                      </div>
                                    </div>
                                    <div>
                                        <label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year</label>
                                        <div class="relative w-full">
                                          <div class="absolute inset-y-0 right-[1rem] flex items-center ps-3 pointer-events-none right-0">
                                             <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                              </svg>
                                          </div>
                                            <div class="mb-6">
                                                <select wire:model.debounce.500ms="year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                    <option value="">Select Year</option>
                                                    @for ($i = date('Y'); $i >= 1970; $i--)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-6">
                                      <label for="mileage" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mileage</label>
                                      <input wire:model.debounce.500ms="mileage" placeholder="Enter Mileage" type="text" id="mileage" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                  </div>
                              </div>
                      </section>
                      
                      
                      <section class="px-4 py-8">
                        <header>
                            <div class="flex flow-row gap-2 items-center">
                                <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                    <span class="text-white">2/5</span>
                                </div>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Select Service') }}
                                </h2>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Select from the following") }}
                            </p>
                        </header>
                    
                        <h3 class="text-md font-medium text-gray-900 mb-4">Available Services</h3>
                        <div class="grid grid-cols-2 gap-6"> 
                            @foreach ($service->where('is_active', true) as $serve)
                                <div class="itemService">
                                    <figure class="transform overflow-hidden rounded-lg bg-white dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedServiceId === $serve->id ? 'bg-blue-300' : '' }}"
                                          wire:click="selectService({{ $serve->id }})">
                                        <img src="{{ asset('storage/' . $serve->icon) }}" class="h-48 w-full object-cover object-center" alt="ServiceIcon" />
                                        <div class="p-4">
                                            <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>ID:</strong> {{ $serve->id }}</p>
                                            <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $serve->service_name }}</p>
                                            <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ $serve->description }}</p>
                                            <p><strong>Price:</strong> {{ $serve->price }}</p>
                                            <p class="ml-auto text-base font-medium text-green-500"><strong>Category:</strong> {{ $serve->category }}</p>
                                        </div>
                                    </figure>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    
                    
                    
                    
                    
                    <section class="px-4 py-8">
                      <header>
                        <div class="flex flow-row gap-2 items-center">
                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                <span class="text-white">3/5</span>
                            </div>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Set Schedule') }}
                            </h2>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __("Select Schedule for reservation") }}
                        </p>
                    </header>
                    
                    <div class="max-w-md mx-auto p-4">
                        <!-- Navigation for days -->
                        <div class="flex justify-between items-center mb-4">
                            <button class="text-blue-500" wire:click="goToPreviousDays" {{ $currentDay->isSameDay(Carbon::now()) ? 'disabled' : '' }}>
                                ← Previous
                            </button>
                            <div class="text-center font-semibold">{{ $currentDay->format('F Y') }}</div>
                            <button class="text-blue-500" wire:click="goToNextDays">Next →
                            </button>
                        </div>
                
                        <!-- Display days and time slots in a 3-column grid layout -->
                        <div class="grid grid-cols-3 gap-4">
                            @foreach (array_slice($days, 0, 3) as $day)
                                <div>
                                    <!-- Day header -->
                                    <button type="button" 
                                        class="text-center cursor-pointer w-full font-semibold p-2 rounded 
                                               {{ $selectedDate == $day->toDateString() ? 'bg-blue-500 text-white' : '' }}"
                                        wire:click="selectDay('{{ $day->toDateString() }}')">
                                        {{ $day->format('D, d') }}
                                    </button>
                                    
                                    <!-- Time slots for each day -->
                                    <div class="mt-2">
                                      @foreach ($availableTimes as $time)
                                          @if ($day->isSameDay(Carbon::now()->addDays(7)) || $day->greaterThan(Carbon::now()->addDays(7)))
                                              <div class="w-full p-2 mb-1 text-center bg-gray-100 text-gray-400">
                                                  No Service Available Yet
                                              </div>
                                          @else
                                              <button type="button"
                                                  class="w-full p-2 mb-1 rounded-lg 
                                                         {{ $selectedDate == $day->toDateString() && $selectedTime == $time ? 'bg-blue-500 text-white' : ($selectedDate == $day->toDateString() ? 'bg-gray-200' : 'bg-gray-100 text-gray-400') }}"
                                                  wire:click="selectTime('{{ $time }}')"
                                                  {{ $selectedDate != $day->toDateString() ? 'disabled' : '' }}
                                              >
                                                  {{ $time }}
                                              </button>
                                          @endif
                                      @endforeach
                                  </div>
                
                                </div>
                            @endforeach
                        </div>
                
                        <!-- Selected Date and Time -->
                        <div class="mt-4">
                            <p>Selected Date: <strong>{{ $selectedDate ?? 'None' }}</strong></p>
                            <p>Selected Time: <strong>{{ $selectedTime ?? 'None' }}</strong></p>
                        </div>
                    </div>
                </section>







                    </div>
                   <div class="w-full flex justify-center items-center">
                     <button class="bg-blue-300 text-white rounded-[5px] w-[80%]" :disabled="!$wire.isSubmitEnabled">{{ __('Reserve') }}</button>
                   </div>
                </form>

                @if (session()->has('message'))
                    <div class="mt-4 text-green-600">
                        {{ session('message') }}
                    </div>
                @endif
                    <style>
                      [disabled] {
                          opacity: 0.5;
                          cursor: not-allowed;
                      }
                      .sliderItem {
                          transition: transform 0.3s ease-in-out, filter 0.3s ease-in-out;
                      }
                      .sliderItem.blurred {
                          filter: blur(2px); 
                      }
                      .sliderItem figure.selected {
                          background-color: rgb(135,225,255); 
                      }
                      .itemService figure.selected {
                          background-color: rgb(135,225,255); 
                      }
                  </style>
              
                  <script>
                      document.addEventListener('DOMContentLoaded', function() {
                         const sliderWrapper = document.querySelector('.sliderWrapper');
                         const sliderItems = document.querySelectorAll('.sliderItem');
                         const figure = document.querySelectorAll('.sliderItem figure'); 
                          
                          let isSliding = false;
                          
                          const figureService = document.querySelectorAll('.itemService figure'); 
           
           
                          figureService.forEach(item => {
                              item.addEventListener('click', function() {
                                  figureService.forEach(i => {
                                      i.classList.remove('selected');
                                      i.style.transform = 'scale(1)'; 
                                  });
                                  this.classList.add('selected');
                              });
                          });
                          
                          sliderWrapper.addEventListener('mousedown', function() {
                              isSliding = true;
                              sliderItems.forEach(item => {
                                  item.style.transform = 'scale(0.8)'; 
                                  item.classList.add('blurred'); 
                              });
                          });
              
                          sliderWrapper.addEventListener('touchstart', function() {
                              isSliding = true;
                              sliderItems.forEach(item => {
                                  item.style.transform = 'scale(0.8)'; 
                                  item.classList.add('blurred'); 
                              });
                          });
                          
                          sliderWrapper.addEventListener('mouseup', function() {
                              isSliding = false;
                              sliderItems.forEach(item => {
                                  item.style.transform = 'scale(1)'; 
                                  item.classList.remove('blurred'); 
                              });
                          });
                          
                          sliderWrapper.addEventListener('touchend', function() {
                              isSliding = false;
                              sliderItems.forEach(item => {
                                  item.style.transform = 'scale(1)'; 
                                  item.classList.remove('blurred'); 
                              });
                          });
                          sliderWrapper.addEventListener('mousemove', function() {
                              if (isSliding) {
                                  sliderItems.forEach(item => {
                                      item.style.transform = 'scale(0.8)';
                                      item.classList.add('blurred'); 
                                  });
                              }
                          });
              
                          sliderWrapper.addEventListener('touchmove', function() {
                              if (isSliding) {
                                  sliderItems.forEach(item => {
                                      item.style.transform = 'scale(0.8)';
                                      item.classList.add('blurred'); 
                                  });
                              }
                          });
              
                          
                          figure.forEach(item => {
                              item.addEventListener('click', function() {
                                  figure.forEach(i => {
                                      i.classList.remove('selected');
                                      i.style.transform = 'scale(1)'; 
                                  });
                                  this.classList.add('selected');
                              });
                          });
                      });
                  </script>
              

            </div>
      