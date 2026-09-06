@props([
    'title' => 'Bạn có chắc không?',
    'message' => 'Hành động này không thể hoàn tác.',
    'confirmLabel' => 'Xác nhận',
    'danger' => true,
    'name' => null,
])

<div
    x-data="{ show: false }"
    x-show="show"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    {{ $attributes->merge(['class' => 'fixed inset-0 z-50 overflow-y-auto']) }}
>
    <!-- Background backdrop -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
        x-on:click="show = false"
    ></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <!-- Modal panel -->
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-sm p-6"
        >
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                    <!-- SVG Icon: AlertTriangle -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-rose-600">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                        <path d="M12 9v4"/>
                        <path d="M12 17h.01"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-1" id="modal-title">
                        {{ $title }}
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $message }}</p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button
                    type="button"
                    x-on:click="show = false"
                    class="flex-1 h-9 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                >
                    Hủy
                </button>
                <button
                    type="button"
                    x-on:click="$dispatch('confirm'); show = false;"
                    class="flex-1 h-9 rounded-lg text-sm font-semibold text-white transition-colors {{ $danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-indigo-600 hover:bg-indigo-700' }}"
                >
                    {{ $confirmLabel }}
                </button>
            </div>
        </div>
    </div>
</div>
