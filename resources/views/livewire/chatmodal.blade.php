<?php

use Livewire\Volt\Component;
use App\Events\MessageSent;
use Livewire\Attributes\On;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public bool $showModal = false;
    public $message;
    public $convo = [];
    
    public function mount() {
      $messages = Message::all();
      foreach($messages as $message) {
        $this->convo[] = [
          'username' => $message->user->name,
          'message' => $message->message
        ];
      }
    }
    
    #[On('echo:messages,MessageSent')]
    public function listenForMessage($data) {
        $this->convo[] = [
          'username' => $data['username'],
          'message' => $data['message']
        ];
    }

    public function toggleModal()
    {
        $this->showModal = !$this->showModal;
    }
    
    public function submitMessage() 
    {
      MessageSent::dispatch(Auth::user()->id, $this->message);
      $this->message = "";
    }
}; 
?>

<div>
    <button 
        class="fixed bottom-2 right-2 bg-sky-500 hover:bg-sky-600 text-white rounded-full p-4 flex items-center justify-center shadow-lg z-[999]"
        wire:click="toggleModal"
        style="background-color: #87CEEB;"
    >
        <i class="fa fa-comments"></i>
    </button>
    
    @if ($showModal)
        <div class="fixed bottom-2 right-2 flex items-center justify-center z-[999]">
            <div 
                class="fixed inset-0 bg-black opacity-50" 
                wire:click="toggleModal"
            ></div>
            
            <div class="bg-white w-80 h-96 rounded-lg shadow-lg p-4 relative">
                <button 
                    class="absolute top-2 right-2 text-gray-600 hover:text-gray-800"
                    wire:click="toggleModal"
                >
                    <i class="fa fa-times"></i>
                </button>

                <div class="flex flex-col items-center relative h-full">
                    <p class="text-lg font-semibold">Chat</p>
                    
                    @foreach ($convo as $convoItem)
                        <p>{{ $convoItem['username'] }}: {{ $convoItem['message'] }}</p>
                    @endforeach
                    
                    <form class="w-full" wire:submit.prevent="submitMessage">
                        <div class="absolute bottom-0 w-full flex flex-row gap-2">
                            <x-text-input class="w-[90%]" placeholder="Message Admin" wire:model="message"></x-text-input>
                            <button type="submit">
                                <i class="text-2xl fa-solid fa-caret-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

