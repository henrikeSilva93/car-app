     <!-- Alert Component -->
        @if(session()->has('success'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => { open = false }, 5000);"
                 x-show="open"
                 x-transition
                 class="fixed top-4 left-4 right-4 z-50 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-lg p-4 text-green-800 dark:text-green-200">
                <div class="flex items-center justify-between">
                    <div>{{ session('success') }}</div>
                    <button @click="open = false" class="ml-2 bg-green-100 dark:bg-green-800 hover:bg-green-200 dark:hover:bg-green-700 text-sm px-3 py-1 rounded">Fechar</button>
                </div>
            </div>
        @elseif(session()->has('error'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => { open = false }, 6000);"
                 x-show="open"
                 x-transition
                 class="fixed top-4 left-4 right-4 z-50 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg p-4 text-red-800 dark:text-red-200">
                <div class="flex items-center justify-between">
                    <div>{{ session('error') }}</div>
                    <button @click="open = false" class="ml-2 bg-red-100 dark:bg-red-800 hover:bg-red-200 dark:hover:bg-red-700 text-sm px-3 py-1 rounded">Fechar</button>
                </div>
            </div>
        @elseif(session()->has('warning'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => { open = false }, 6000);"
                 x-show="open"
                 x-transition
                 class="fixed top-4 left-4 right-4 z-50 bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-yellow-800 dark:text-yellow-200">
                <div class="flex items-center justify-between">
                    <div>{{ session('warning') }}</div>
                    <button @click="open = false" class="ml-2 bg-yellow-100 dark:bg-yellow-800 hover:bg-yellow-200 dark:hover:bg-yellow-700 text-sm px-3 py-1 rounded">Fechar</button>
                </div>
            </div>
        @elseif(session()->has('delete'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => { open = false }, 6000);"
                 x-show="open"
                 x-transition
                 class="fixed top-4 left-4 right-4 z-50 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg p-4 text-red-800 dark:text-red-200">
                <div class="flex items-center justify-between">
                    <div>{{ session('delete') }}</div>
                    <button @click="open = false" class="ml-2 bg-red-100 dark:bg-red-800 hover:bg-red-200 dark:hover:bg-red-700 text-sm px-3 py-1 rounded">Fechar</button>
                </div>
            </div>  
        @endif