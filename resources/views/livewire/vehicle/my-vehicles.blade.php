<?php

use Livewire\Volt\Component;
use App\Models\Vehicle;
use App\Models\VehicleType;

new class extends Component {
    public $vehicleTypes = [];
    public $selectedVehicleTypeId;
    public $model = '';
    public $make = '';
    public $year = '';
    public $license_plate = '';
    public $color = '';
    public $mileage = '';
    public $vehicles;
    public $selectedVehicleId = null;

    
    public function mount(): void 
    {
        $this->vehicleTypes = VehicleType::select('id', 'name', 'description', 'price', 'icon')->get() ?? collect(); 
        $this->vehicles = Vehicle::with('vehicleType')->where('user_id', auth()->id())->get() ?? collect();
    }
        
    public function selectVehicleType($vehicleTypeId)
    {
        $this->selectedVehicleTypeId = $vehicleTypeId;
        \Log::info('Selected Vehicle Type ID: ' . $this->selectedVehicleTypeId);
    }

    public function selectVehicle($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if ($vehicle) {
            $this->selectedVehicleId = $vehicleId; 
            $this->selectedVehicleTypeId = $vehicle->vehicle_type_id; 
            $this->model = $vehicle->model;
            $this->make = $vehicle->make;
            $this->year = $vehicle->year;
            $this->license_plate = $vehicle->license_plate;
            $this->color = $vehicle->color;
            $this->mileage = $vehicle->mileage;
        }
    }

    public function cancelEdit()
    {
        $this->reset(['model', 'make', 'year', 'license_plate', 'color', 'mileage', 'selectedVehicleTypeId', 'selectedVehicleId']);
    }

    public function submitVehicle()
{
    $this->validate([
        'selectedVehicleTypeId' => 'required|exists:vehicle_types,id',
        'model' => 'required|string',
        'make' => 'required|string',
        'year' => 'required|integer',
        'license_plate' => 'required|string|unique:vehicles,license_plate,' . $this->selectedVehicleId,
        'color' => 'required|string',
        'mileage' => 'nullable|integer',
    ]);

    if ($this->selectedVehicleId) {
        $vehicle = Vehicle::find($this->selectedVehicleId);
        if (!$vehicle) {
            \Log::error('Vehicle not found for ID: ' . $this->selectedVehicleId);
            return;
        }
        $vehicle->update([
            'vehicle_type_id' => $this->selectedVehicleTypeId,
            'model' => $this->model,
            'make' => $this->make,
            'year' => $this->year,
            'license_plate' => $this->license_plate,
            'color' => $this->color,
            'mileage' => $this->mileage,
        ]);
        session()->flash('message', 'Vehicle updated successfully!');
    } else {
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
    }
    $this->vehicles = Vehicle::with('vehicleType')->where('user_id', auth()->id())->get() ?? collect();
    
    $this->reset();
}
    
    public function getIsSubmitEnabledProperty()
    {
        return !empty($this->selectedVehicleTypeId) &&
               !empty($this->model) &&
               !empty($this->make) &&
               !empty($this->year) &&
               !empty($this->license_plate) &&
               !empty($this->color);
    }
}; ?>


  <div>
       <div class="px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} overflow-hidden shadow-sm sm:rounded-lg w-[91%] rounded-[10px]">
                <form wire:submit.prevent="submitVehicle">
                    <div class="w-full">
                       <section class="px-4 py-8">
                          <header>
                              <p class="mt-1 text-sm text-gray-600">{{ __("Select type and input details") }}</p>
                          </header>
                      
                              <h3 class="text-md font-medium text-gray-900 mb-4">Available Vehicle Types</h3>
                                <div class="w-full">
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
                   <button wire:click="cancelEdit" type="button" class ="px-3 py-1 bg-red-500 text-white rounded">
                      Cancel
                  </button>
                </form>
                 @if (session()->has('message'))
                    <div class="mt-4 text-green-600">
                        {{ session('message') }}
                    </div>
                @endif

    <section class="mt-10">
        <div class="px-4 lg:px-12">
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
                                    <button wire:click="selectVehicle({{ $vehicle->id }})" class="px-3 py-1 bg-[#5186E8] text-white rounded" title="Edit Vehicle">
                                        <i class="fa-solid fa-pen-nib"></i>
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
     
      <script>
         document.addEventListener('DOMContentLoaded', function() {
            const figure = document.querySelectorAll('.sliderItem figure'); 
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
