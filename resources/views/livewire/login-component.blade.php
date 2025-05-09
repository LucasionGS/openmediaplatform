<span x-data="{ open: false }">
    <!-- Login button that opens a dialog using Alpine.js -->
    <button
        @click="open = true"
        class="bg-blue-500 text-white font-bold py-2 px-4 rounded">
        {{ $buttonText }}
    </button>

    <!-- Dialog -->
    <div
        x-show="open"
        class="fixed inset-0 flex items-center justify-center bg-[#0000000f] z-50"
        style="display: none;">
        <div
            class="bg-white rounded-lg shadow-lg p-6"
            @click.away="open = false"
        >
            <h2 class="text-xl font-bold mb-4">Login</h2>
            <form wire:submit.prevent="login">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" wire:model.defer="email" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" wire:model.defer="password" required>
                </div>
                @if ($error)
                    <p class="text-red-500 text-sm mt-2">{{ $error }}</p>
                @endif
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">
                    {{ $buttonText }}
                </button>
            </form>
            <button @click="open = false" class="mt-4 text-gray-500 hover:text-gray-700">Close</button>
        </div>
    </div>
</span>
