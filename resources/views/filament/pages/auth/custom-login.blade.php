<x-filament-panels::page.simple>
    <div class="flex items-center justify-center min-h-screen bg-cover bg-center" 
         style="background-image: url('{{ asset('images/school-background.jpg') }}');">
        <div class="absolute inset-0 bg-primary-900/30 backdrop-blur-sm"></div>
        
        <div x-data="{ showForm: false }" 
             x-init="setTimeout(() => showForm = true, 200)"
             class="relative w-full md:w-[32rem]">
            
            <div x-show="showForm" 
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="px-8 py-10 bg-white/95 dark:bg-gray-800/95 shadow-2xl rounded-xl border border-gray-200 dark:border-gray-700 space-y-8">
                
                <div class="w-full flex justify-center">
                    <div class="relative">
                        <img src="{{ asset('images/sayfone-school-logo.png') }}" 
                             alt="Sayfone School" 
                             class="h-20 mx-auto rounded-lg shadow-md">
                        <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-primary-500 text-white text-xs px-4 py-1 rounded-full">
                            ລະບົບຄຸ້ມຄອງໂຮງຮຽນ
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2 pt-4">
                    <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ $this->getHeading() }}
                    </h2>
                    
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->getSubheading() }}
                    </p>
                </div>

                <div class="p-1 border-t border-gray-200 dark:border-gray-700"></div>

                {{ $this->form }}

                <div class="text-center">
                    <div class="mt-2">
                        @if (config('filament.auth.password_reset'))
                            <a class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400" 
                               href="{{ route('filament.auth.password-reset.request') }}">
                                {{ __('ລືມລະຫັດຜ່ານ?') }}
                            </a>
                        @endif
                    </div>

                    @if (config('filament.registration'))
                        <div class="mt-2">
                            <a class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400" 
                               href="{{ route('filament.auth.register') }}">
                                {{ __('ລົງທະບຽນບັນຊີໃໝ່') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="pt-2 mt-6 text-center text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800">
                    <p>&copy; {{ date('Y') }} Sayfone School. {{ __('ສະຫງວນລິຂະສິດ') }}</p>
                    <p class="mt-1">{{ __('ພັດທະນາໂດຍບໍລິສັດໄອທີລາວ ຈຳກັດ') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>

<script>
    // ເພີ່ມການເຄື່ອນໄຫວຂອງພື້ນຫຼັງ (ຖ້າຕ້ອງການ)
    document.addEventListener('DOMContentLoaded', () => {
        const background = document.querySelector('.min-h-screen');
        if (background) {
            document.addEventListener('mousemove', (e) => {
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                background.style.backgroundPosition = `${50 + x * 5}% ${50 + y * 5}%`;
            });
        }
    });
</script>