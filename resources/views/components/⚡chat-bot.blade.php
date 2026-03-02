<?php

use Livewire\Component;
new class extends Component
{
   
    public $showChat = false;
    public $messages = [];
    public $input = '';
    public $loading = false;

    public function mount() {
       $messagesFromDb = \App\Models\ChatbotMessageHistory::where('user_id', auth()->id())->get();
       if($messagesFromDb->isEmpty()) {
        $this->messages[] = ['from' => 'bot', 'text' => 'Bem-vindo, em que posso ajudar?'];
       }
       foreach($messagesFromDb as $msg) {
            $this->messages[] = ['from' => $msg->sender, 'text' => $msg->message];
       }

       //$this->dispatch('scrollDown');
    }

    public function sendMessage() {
        if (trim($this->input) === '') {
            return;
        }
        $prismService = app(\App\Services\PrismService::class);
        $this->messages[] = ['from' => 'user', 'text' => $this->input];
        $userMessage = $this->input;
        $this->input = '';
        $this->loading = true;
        \App\Models\ChatbotMessageHistory::create([
            'user_id' => auth()->id(),
            'message' => $userMessage,
            'sender' => 'user'
            ]);
            
            $botResponse = $prismService->generateResponse($userMessage);
            if($botResponse) {
                $this->messages[] = ['from' => 'bot', 'text' => $botResponse];
                $this->dispatch('scrollDown');
                $this->loading = false;

        }
    }
};
?>
<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.25s ease;
}
</style>
<div>
    <div class="fixed bottom-8 right-8 z-50">
    <button wire:click="$toggle('showChat')" class="bg-blue-600 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg focus:outline-none">
        <!-- Chat Icon SVG -->
        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 21l1.8-4A7.97 7.97 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </button>
    @if($showChat)
    <div class="absolute bottom-20 right-0 w-80 max-h-[480px] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-fade-in">
        <div class="bg-blue-600 text-white px-4 py-3 font-bold flex justify-between items-center">
            <span>ChatBot</span>
            <button wire:click="$toggle('showChat')" class="text-white hover:text-gray-200 text-xl">&times;</button>
        </div>
        <div id="chat-messages" class="px-4 py-3 flex-1 overflow-y-auto text-sm bg-gray-50" style="height:260px;">
            @foreach($messages as $msg)
                <div class="mb-2 text-{{ $msg['from'] === 'user' ? 'right' : 'left' }}">
                    <span class="inline-block px-3 py-2 rounded-xl {{ $msg['from'] === 'user' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-gray-200 text-gray-900 rounded-bl-none' }}">
                        {!! \Illuminate\Support\Str::markdown($msg['text']) !!}
                    </span>
                </div>
            @endforeach
            <div wire:loading>
                <div class="mb-2 text-left">
                    <span class="inline-block px-3 py-2 rounded-xl bg-gray-200 text-gray-900 rounded-bl-none animate-pulse">
                        <span class="inline-block w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                        <span class="inline-block w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                        <span class="inline-block w-2 h-2 bg-gray-300 rounded-full"></span>
                    </span>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="sendMessage" class="flex border-t border-gray-200">
            <input wire:model.defer="input" type="text" placeholder="Digite sua mensagem..." class="flex-1 border-none px-4 py-3 text-sm focus:outline-none" autocomplete="off" />
            <button type="submit" class="bg-blue-600 text-white px-5 text-sm font-semibold">Enviar</button>
        </form>
    </div>
    @endif
</div>

</div>

@script
 <script>    
     
        Livewire.on('scrollDown', () => {
            let box = document.getElementById('chat-messages');
            console.log('scrolling down',);
            box.scrollTo({
                top: box.scrollHeight,
                behavior: 'smooth'
            });
        });
  

      
 </script>
@endscript