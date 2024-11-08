<?php

use Livewire\Volt\Component;
use App\Models\Reservation;
use Stripe\StripeClient;

new class extends Component {
    public $amount;
    public $reservationId;
    public $paymentOption;
    public $paymentUrl;
    public $serviceName;

    public function mount($reservationId)
    {
        $this->reservationId = $reservationId;

        $reservation = Reservation::with(['service', 'vehicle.vehicleType'])->findOrFail($reservationId);
        
        $servicePrice = $reservation->service->price ?? 0;
        $vehicleTypePrice = $reservation->vehicle->vehicleType->price ?? 0;
        $currentDollarRate = 58.07;
        $totalAmount = ($servicePrice + $vehicleTypePrice) / $currentDollarRate;
        $this->amount = number_format($totalAmount, 2, '.', '');
        $this->paymentOption = 'full';

        $this->serviceName = $reservation->service->name ?? 'Unknown Service';
    }

    public function updatedPaymentOption($value)
    {
        $reservation = Reservation::with(['service', 'vehicle.vehicleType'])->findOrFail($this->reservationId);
        $servicePrice = $reservation->service->price ?? 0;
        $vehicleTypePrice = $reservation->vehicle->vehicleType->price ?? 0;
        $currentDollarRate = 58.07;
        $totalAmount = ($servicePrice + $vehicleTypePrice) / $currentDollarRate;
    
        $this->amount = $value === 'half'
            ? $totalAmount / 2
            : $totalAmount;
    
        
        $this->formattedAmount = number_format($this->amount, 2, '.', '');
    }
    
    public function initiateCheckout()
    {
        try {
            // Create a new Stripe Checkout session
            $stripe = new StripeClient(config('cashier.secret'));
            
            $paymentStatus = $this->paymentOption === 'half' ? 'partialy_paid' : 'full_paid';
            
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $this->serviceName,
                        ],
                        'unit_amount' => (int)($this->amount * 100), // Amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('reservation.reserved', [
                    'id' => $this->reservationId,
                    'service_name' => $this->serviceName,
                    'amount' => $this->amount,
                    'payment_method' => 'stripe',
                    'payment_status' => $paymentStatus,
                ]),
                'cancel_url' => route('payment.cancel', [
                    'reservationId' => $this->reservationId
                ]),
            ]);
    
            $this->paymentUrl = $session->url;
            $this->redirect($this->paymentUrl);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }
};
?>


 <div class="grid grid-cols-2 place-items-center mx-2 p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <ol class="relative text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400">                  
        <li class="mb-10 ms-6">            
            <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-green-900">
                4/5
            </span>
            <h3 class="font-medium leading-tight">Personal Info</h3>
            <p class="text-sm">Currently Pending</p>
        </li>
        <li class="mb-10 ms-6">
            <span class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900">
                5/5
            </span>
            <h3 class="font-medium leading-tight">Account Info</h3>
            <p class="text-sm">Pay total amount here</p>
        </li>
        <li class="mb-10 ms-6">
            <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
               Complete
            </span>
            <h3 class="font-medium leading-tight">Review</h3>
            <p class="text-sm">Step details here</p>
        </li>
    </ol> 
    <div>
    <h2>Pay for {{ $serviceName }}</h2>

    <!-- Payment Option Selection -->
    <label for="paymentOption">Select Payment Option:</label>
    <select id="paymentOption" wire:model.live="paymentOption">
        <option value="full">Pay Full Amount</option>
        <option value="half">Pay 50%</option>
    </select>

    <button wire:click="initiateCheckout">Proceed to Pay ${{ number_format($amount, 2, '.', '') }}</button>

    <!-- Error Display -->
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
</div>
  </div>