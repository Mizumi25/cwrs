<?php

use Livewire\Volt\Component;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;

new class extends Component {
    public $pdfUrl;

    public function mount()
    {
        // Assuming you have the client's information and the services they booked
        $customer = new Buyer([
            'name' => 'John Doe',
            'custom_fields' => [
                'email' => 'john.doe@example.com',
            ],
        ]);

        $item = InvoiceItem::make('Car Wash Service')->pricePerUnit(50.00); // Example service

        $invoice = Invoice::make()
            ->buyer($customer)
            ->addItem($item)
            ->currencySymbol('$')
            ->filename('Invoice_' . $customer->name)
            ->save('public'); // Save to the public disk

        // Set the URL to the saved PDF
        $this->pdfUrl = asset('storage/Invoice_' . $customer->name . '.pdf');
    }
}; ?>


 <div class="grid grid-cols-2 place-items-center mx-2 p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <ol class="relative text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400">                  
        <li class="mb-10 ms-6">            
            <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                4/5
            </span>
            <h3 class="font-medium leading-tight">Personal Info</h3>
            <p class="text-sm">Currently Pending</p>
        </li>
        <li class="mb-10 ms-6">
            <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                5/5
            </span>
            <h3 class="font-medium leading-tight">Account Info</h3>
            <p class="text-sm">Pay total amount here</p>
        </li>
        <li class="mb-10 ms-6">
            <span class="absolute flex items-center justify-center w-8 h-8 bg-green-100  rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900">
               Complete
            </span>
            <h3 class="font-medium leading-tight">Review</h3>
            <p class="text-sm">Step details here</p>
        </li>
    </ol> 
    <div>
    <div>
        @if($pdfUrl)
            <iframe src="{{ $pdfUrl }}" width="100%" height="600px"></iframe>
        @else
            <p>Loading invoice...</p>
        @endif
    </div>
</div>
  </div>