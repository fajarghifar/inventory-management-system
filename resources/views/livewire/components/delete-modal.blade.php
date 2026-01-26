<div
    x-data="{ show: @entangle('open') }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    class="relative z-[100]"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500/75 opacity-100"
    ></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-card text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-border"
                @click.away="show = false"
            >
                <div class="bg-card px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold leading-6 text-foreground" id="modal-title">
                                {{ $title }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-muted-foreground">
                                    {{ $description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-card px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button
                        type="button"
                        wire:click="confirm"
                        wire:loading.attr="disabled"
                        class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm sm:w-auto ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 {{ $confirmButtonClass }}"
                    >
                        <svg wire:loading wire:target="confirm" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ $confirmButtonText }}
                    </button>
                    <button
                        type="button"
                        @click="show = false"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-background px-3 py-2 text-sm font-semibold text-foreground shadow-sm ring-1 ring-inset ring-border hover:bg-accent hover:text-accent-foreground sm:mt-0 sm:w-auto transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
