<?php

use Livewire\Volt\Component;
use App\Models\Vehicle;
use App\Models\VehicleType;

new class extends Component {
    public $vehicleTypes;
    public $selectedVehicleTypeId = null;
    public $model = '';
    public $make = '';
    public $year = '';
    public $license_plate = '';
    public $color = '';
    public $mileage = '';
    public $vehicles;

    
    public function mount(): void 
    {
    $this->vehicles = Vehicle::all()
            ->where('user_id', auth()->id());
    $this->vehicleTypes = VehicleType::all();
    }
    
    public function selectVehicleType($vehicleTypeId)
    {
        $this->selectedVehicleTypeId = $vehicleTypeId;
    }

    public function submitVehicle()
   {
    $this->validate([
        'selectedVehicleTypeId' => 'required|exists:vehicle_types,id',
        'model' => 'required|string',
        'make' => 'required|string',
        'year' => 'required|integer',
        'license_plate' => 'required|string|unique:vehicles,license_plate',
        'color' => 'required|string',
        'mileage' => 'nullable|integer',
    ]);

    Vehicle::create([
        'user_id' => auth()->id(),
        'vehicle_type_id' => $this->selectedVehicleTypeId, 
        'model' => $this->model,
        'make' => $this->make,
        'year' => $this->year,
        'license_plate' => $this->license_plate,
        'color' => $this->color,
        'mileage' => $this->mileage,
    ]);
    
    

    session()->flash('message', 'Vehicle added successfully!');
    $this->reset();
   }
   
   public function getIsSubmitEnabledProperty()
   {
    return !empty($this->selectedVehicleTypeId) &&
           !empty($this->selectedServiceId) &&
           !empty($this->model) &&
           !empty($this->make) &&
           !empty($this->year) &&
           !empty($this->license_plate) &&
           !empty($this->color);
   }
}; ?>


  <div>
       <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form wire:submit.prevent="submitVehicle">
                    <div class="w-full">
                       <section class="px-4 py-8">
                          <header>
                              <p class="mt-1 text-sm text-gray-600">{{ __("Select type and input details") }}</p>
                          </header>
                      
                              <h3 class="text-md font-medium text-gray-900 mb-4">Available Vehicle Types</h3>
                                <div class="w-full">
                                    <div class="grid grid-cols-4 place-items-center gap-4">
                                        @foreach ($vehicleTypes as $type)
                                            <div class="sliderItem flex-none h-[6rem] w-[6rem] transition-transform duration-300 ease-in-out">
                                              <figure 
                                                  class="relative pb-[50%] overflow-hidden bg-white/20 shadow-lg ring-1 ring-black/5 h-full w-full rounded-1xl shadow-lg {{ $selectedVehicleTypeId === $type->id ? 'bg-blue-300' : '' }}"
                                                  wire:click="selectVehicleType({{ $type->id }})" 
                                              >
                                                  <div class="p-4">
                                                      <p><strong>ID:</strong> {{ $type->id }}</p>
                                                      <p><strong>Name:</strong> {{ $type->name }}</p>
                                                      <p><strong>Price:</strong> {{ $type->price }}</p>
                                                  </div>
                                                  <img src="{{ asset('storage/' . $type->icon) }}" class="absolute top-0 left-0 w-full h-full object-cover rounded-2xl" alt="VehicleTypeIcon" />
                                              </figure>
                                          </div>
                                        @endforeach
                                    </div>
                                    
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
                   </div>
                   <button :disabled="!$this->isSubmitEnabled">{{ __('Add New Vehicle') }}</button>
                </form>
                 @if (session()->has('message'))
                    <div class="mt-4 text-green-600">
                        {{ session('message') }}
                    </div>
                @endif

    <section class="mt-10">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">Vehice Type</th>
                                <th scope="col" class="px-4 py-3">Model</th>
                                <th scope="col" class="px-4 py-3">Make</th>
                                <th scope="col" class="px-4 py-3">Year</th>
                                <th scope="col" class="px-4 py-3">License Plate</th>
                                <th scope="col" class="px-4 py-3">Color</th>
                                <th scope="col" class="px-4 py-3">Mileage</th>
                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                                <tr class="border-b dark:border-gray-700">
                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $vehicle->vehicleType->name ?? 'N/A' }} 
                                    </th>
                                    <td class="px-4 py-3">{{ $vehicle->model }}</td>
                                    <td class="px-4 py-3">{{ $vehicle->make }}</td>
                                    <td class="px-4 py-3">{{ $vehicle->year }}</td>
                                    <td class="px-4 py-3">{{ $vehicle->license_plate }}</td>
                                    <td class="px-4 py-3">{{ $vehicle->color }}</td>
                                     <td class="px-4 py-3">{{ $vehicle->mileage }}</td>
                                    <td class="px-4 py-3 flex items-center justify-end">
                                        <button class="px-3 py-1 bg-green-500 text-white rounded">
                                            <i class="fa-solid fa-right-to-bracket"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="py-4 px-3">
                    <div class="flex ">
                        <div class="flex space-x-4 items-center mb-3">
                            <label class="w-32 text-sm font-medium text-gray-900">Per Page</label>
                            <select
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


      </div>
      <style>
          [disabled] {
             opacity: 0.5;
             cursor: not-allowed;
          }
                      
           .sliderItem figure.selected {
               background-color: rgb(135,225,255); 
         }
     </style>
</div>
