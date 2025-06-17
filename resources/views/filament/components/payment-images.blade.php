// ສ້າງໄຟລ໌ resources/views/filament/components/payment-images.blade.php

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($images as $image)
        <div class="border rounded-lg p-3 bg-gray-50 hover:bg-gray-100 transition-colors">
            <div class="text-xs font-medium text-gray-600 mb-2">
                {{ $image->getImageTypeLabel() }}
            </div>

            <div class="relative group">
                <img src="{{ Storage::disk('public')->url($image->image_path) }}" alt="Payment Image"
                    class="w-full h-32 object-cover rounded cursor-pointer hover:opacity-90 transition-opacity"
                    onclick="window.open('{{ Storage::disk('public')->url($image->image_path) }}', '_blank')" />
                <div
                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded flex items-center justify-center">
                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </div>
            </div>

            <div class="text-xs text-gray-500 mt-2">
                <div>{{ $image->getFormattedFileSizeAttribute() }}</div>
                <div>{{ $image->upload_date?->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-4 text-gray-500">
            ບໍ່ມີຮູບພາບ
        </div>
    @endforelse
</div>