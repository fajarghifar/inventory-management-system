<div x-data="{
    notifications: [],
    add(message, type = 'success') {
        const id = Date.now();
        this.notifications.push({ id, message, type });
        setTimeout(() => this.remove(id), 6000);
    },
    remove(id) {
        this.notifications = this.notifications.filter(notification => notification.id !== id);
    }
}"

x-init="
    @if (session('success'))
        add('{{ session('success') }}', 'success');
    @endif
    @if (session('error'))
        add('{{ session('error') }}', 'error');
    @endif
    window.addEventListener('toast', event => {
        add(event.detail.message, event.detail.type);
    });
"
class="fixed bottom-4 right-8 z-50 flex flex-col gap-2 items-end">
    <template x-for="notification in notifications" :key="notification.id">
        <div x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="w-auto min-w-[350px] max-w-2xl bg-white dark:bg-slate-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden backdrop-blur-sm bg-opacity-95 dark:bg-opacity-95">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <template x-if="notification.type === 'success'">
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="notification.type === 'error'">
                            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </template>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p x-text="notification.type === 'success' ? 'Success!' : 'Error!'" class="text-sm font-semibold text-gray-900 dark:text-gray-100"></p>
                        <p x-text="notification.message" class="mt-1 text-sm text-gray-500 dark:text-gray-400 break-words"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button type="button" @click="remove(notification.id)" class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
