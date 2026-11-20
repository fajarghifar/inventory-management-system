@props([
    'subtotal' => 0,
    'discount' => 0,
    'total' => 0,
    'currency' => 'ETB',
    'cashReceived' => null,
    'change' => 0,
    'remaining' => 0,
    'paymentMethod' => 'cash',
])

<div {{ $attributes->merge(['class' => 'bg-gray-50/80 rounded-2xl p-4 border border-gray-100 space-y-2.5 font-mono text-xs select-none']) }}>
    <div class="flex justify-between text-gray-600">
        <span>Subtotal</span>
        <span class="font-bold text-gray-900">{{ $currency }} {{ number_format((float)$subtotal, 2) }}</span>
    </div>

    @if((float)$discount > 0)
        <div class="flex justify-between text-rose-600 font-semibold">
            <span>Discount Applied</span>
            <span>- {{ $currency }} {{ number_format((float)$discount, 2) }}</span>
        </div>
    @endif

    <div class="pt-2 border-t border-gray-200/80 flex justify-between items-center text-sm">
        <span class="font-bold text-gray-900 font-sans">Total Amount</span>
        <span class="font-black text-brand text-lg">
            {{ $currency }} {{ number_format((float)$total, 2) }}
        </span>
    </div>

    @if($paymentMethod === 'cash' && $cashReceived !== null)
        <div class="pt-2 border-t border-dashed border-gray-200 space-y-1.5">
            <div class="flex justify-between text-gray-600">
                <span class="font-sans">Cash Received</span>
                <span class="font-bold text-gray-800">{{ $currency }} {{ number_format((float)$cashReceived, 2) }}</span>
            </div>

            @if((float)$change > 0)
                <div class="flex justify-between text-emerald-700 font-bold">
                    <span class="font-sans">Change to Return</span>
                    <span class="text-sm font-black">{{ $currency }} {{ number_format((float)$change, 2) }}</span>
                </div>
            @elseif((float)$remaining > 0)
                <div class="flex justify-between text-rose-600 font-bold">
                    <span class="font-sans">Remaining Due</span>
                    <span class="text-sm font-black">{{ $currency }} {{ number_format((float)$remaining, 2) }}</span>
                </div>
            @endif
        </div>
    @endif
</div>
