     <!-- Alert Component -->
        @if(session()->has('success'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => { open = false }, 5000);"
                 x-show="open"
                 x-transition
                 class="fixed top-4 left-4 right-4 z-50 bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">
                <div class="flex items-center justify-between">
                    <div>{{ session('success') }}</div>
                    <button @click="open = false" class="ml-2 bg-green-100 hover:bg-green-200 text-sm px-3 py-1 rounded">Fechar</button>
                </div>
            </div>
        @elseif(session()->has('error'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => { open = false }, 6000);"
                 x-show="open"
                 x-transition
                 class="fixed top-4 left-4 right-4 z-50 bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
                <div class="flex items-center justify-between">
                    <div>{{ session('error') }}</div>
                    <button @click="open = false" class="ml-2 bg-red-100 hover:bg-red-200 text-sm px-3 py-1 rounded">Fechar</button>
                </div>
            </div>
        @elseif(session()->has('warning'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => { open = false }, 6000);"
                 x-show="open"
                 x-transition
                 class="fixed top-4 left-4 right-4 z-50 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
                <div class="flex items-center justify-between">
                    <div>{{ session('warning') }}</div>
                    <button @click="open = false" class="ml-2 bg-yellow-100 hover:bg-yellow-200 text-sm px-3 py-1 rounded">Fechar</button>
                </div>
            </div>
        @elseif(session()->has('delete'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => { open = false }, 6000);"
                 x-show="open"
                 x-transition
                 class="fixed top-4 left-4 right-4 z-50 bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
                <div class="flex items-center justify-between">
                    <div>{{ session('delete') }}</div>
                    <button @click="open = false" class="ml-2 bg-red-100 hover:bg-red-200 text-sm px-3 py-1 rounded">Fechar</button>
                </div>
            </div>  
        @endif