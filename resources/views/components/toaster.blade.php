@props(['position' => 'bottom-center'])

<div
    x-data="{
        notifications: [
            @if(session()->has('success'))
                { id: Date.now(), message: @js(session('success')), type: 'success' },
            @endif
            @if(session()->has('error'))
                { id: Date.now() + 1, message: @js(session('error')), type: 'error' },
            @endif
            @if(session()->has('warning'))
                { id: Date.now() + 2, message: @js(session('warning')), type: 'warning' },
            @endif
            @if(session()->has('info'))
                { id: Date.now() + 3, message: @js(session('info')), type: 'info' },
            @endif
        ],
        init() {
            // Auto-dismiss valid initial notifications
            this.notifications.forEach(n => {
                setTimeout(() => this.remove(n.id), 8000);
            });
        },
        add(message, type = 'success') {
            const id = Date.now();
            this.notifications.unshift({ id, message, type });
            setTimeout(() => this.remove(id), 8000);
            if (this.notifications.length > 5) this.notifications.pop();
        },
        remove(id) {
            this.notifications = this.notifications.filter(notification => notification.id !== id);
        }
    }"
    x-on:toast.window="add($event.detail.message, $event.detail.type)"
    class="fixed inset-x-0 top-0 p-4 flex flex-col items-center justify-start z-[100] pointer-events-none gap-2 sm:top-6 sm:p-6"
>
    <!--
        Stack Container: Top Center
    -->
    <div
        class="relative w-full max-w-lg h-auto flex flex-col items-center justify-start"
        x-data="{ expanded: false }"
        x-on:mouseenter="expanded = true"
        x-on:mouseleave="expanded = false"
    >
        <template x-for="(notification, index) in notifications" :key="notification.id">
            <div
                x-show="true"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="-translate-y-full opacity-0 scale-90"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90 -translate-y-4"

                :style="`
                    position: ${index === 0 ? 'relative' : 'absolute'};
                    top: 0;
                    z-index: ${50 - index};
                    transform: translateY(${expanded ? index * (60 + 10) : index * 14}px) scale(${expanded ? 1 : 1 - index * 0.05});
                    opacity: ${1 - (expanded ? 0 : index * 0.1)};
                    margin-bottom: ${index === 0 ? '0' : '0'};
                    visibility: ${index > (expanded ? 50 : 3) ? 'hidden' : 'visible'};
                    height: auto;
                `"

                class="pointer-events-auto flex w-full items-center gap-3 overflow-hidden rounded-xl border border-border bg-card p-4 shadow-xl shadow-black/5 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
            >
                <!-- Icon -->
                <div class="shrink-0">
                    <template x-if="notification.type === 'success'">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-green-500/10 text-green-500">
                            <x-heroicon-o-check class="h-4 w-4" />
                        </div>
                    </template>
                    <template x-if="notification.type === 'error'">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-red-500/10 text-red-500">
                            <x-heroicon-o-x-mark class="h-4 w-4" />
                        </div>
                    </template>
                    <template x-if="notification.type === 'info'">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500/10 text-blue-500">
                            <x-heroicon-o-information-circle class="h-4 w-4" />
                        </div>
                    </template>
                </div>

                <!-- Message -->
                <div class="flex-1">
                    <p x-text="notification.message" class="text-sm font-medium text-card-foreground leading-snug"></p>
                    <template x-if="notification.description">
                        <p x-text="notification.description" class="mt-1 text-xs text-muted-foreground"></p>
                    </template>
                </div>

                <!-- Close / Action -->
                <button
                    @click="remove(notification.id)"
                    class="shrink-0 rounded-md p-1 opacity-0 transition-opacity hover:bg-muted group-hover:opacity-100 focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring"
                    :class="{'opacity-100': true}"
                >
                    <x-heroicon-o-x-mark class="h-4 w-4 text-muted-foreground" />
                </button>
            </div>
        </template>
    </div>
</div>
