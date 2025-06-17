<x-filament-panels::page>
    <div class="flex flex-col gap-4">
        {{-- ສ່ວນຄົ້ນຫາແລະຜົນລັບ --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="h-5 w-5 text-gray-500" />
                    <span>ຄົ້ນຫານັກຮຽນ</span>
                </div>
            </x-slot>

            <div class="space-y-4">
                {{-- ຊ່ອງຄົ້ນຫາ --}}
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <x-filament::input type="search" wire:model.live.debounce.500ms="search_val"
                            wire:keydown.enter="performSearch" placeholder="ພິມລະຫັດນັກຮຽນ ຫຼື ຊື່-ນາມສະກຸນ (ພາສາລາວ)"
                            class="w-full" />

                        <div wire:loading.delay wire:target="updatedSearchVal"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <x-filament::loading-indicator class="h-5 w-5" />
                        </div>
                    </div>
                </div>

                {{-- ຜົນການຄົ້ນຫາ --}}
                @if ($foundStudents->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        @foreach ($foundStudents as $student)
                            <div
                                class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:border-primary-500 transition duration-200">
                                <div class="flex items-center p-4 gap-2">
                                    <div class="flex-shrink-0">
                                        <x-filament::avatar
                                            src="{{ $student->profile_image ? asset('storage/' . $student->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($student->first_name_lao) }}"
                                            alt="{{ $student->first_name_lao }}" size="w-11 h-11" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $student->getFullName() }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            ລະຫັດ: {{ $student->student_code }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $student->class ?? 'ບໍ່ລະບຸຊັ້ນຮຽນ' }}
                                        </p>
                                    </div>
                                    <div>
                                        <x-filament::button wire:click="selectStudent('{{ $student->student_id }}')"
                                            color="primary">
                                            ເລືອກ
                                        </x-filament::button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif(strlen($search_val) >= 2)
                    <div
                        class="flex flex-col items-center justify-center p-6 bg-white rounded-lg border border-dashed border-gray-300 mt-4">
                        <div class="rounded-full bg-primary-50 p-3">
                            <x-filament::icon alias="empty-state" icon="heroicon-o-magnifying-glass"
                                class="h-6 w-6 text-primary-500" />
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">ບໍ່ພົບຂໍ້ມູນນັກຮຽນ</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            ບໍ່ພົບຂໍ້ມູນນັກຮຽນທີ່ກົງກັບຄຳຄົ້ນຫາ "{{ $search_val }}"
                        </p>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- ຂໍ້ມູນນັກຮຽນ --}}
        @if ($selectedStudent)
            {{-- ສ່ວນຂໍ້ມູນນັກຮຽນ --}}
            <x-filament::section icon="heroicon-o-user">
                <x-slot name="heading">
                    ຂໍ້ມູນນັກຮຽນ
                </x-slot>
                <div class="flex gap-3">
                    <div class="">
                        <img src="{{ $profile_image ? asset('storage/' . $profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($selectedStudent->first_name_lao) }}"
                            alt="ຮູບນັກຮຽນ" width="100px" class="rounded" />
                    </div>

                    <div class="flex justify-between gap-3 text-lg bg-blue-500  w-full">
                        <div class="flex flex-col justify-between">
                            <p>
                                <strong>ລະຫັດນັກຮຽນ:</strong>
                                <span>{{ $selectedStudent->student_code }}</span>
                            </p>
                            <p>
                                <strong>ຊື່ ແລະ ນາມສະກຸນ:</strong>
                                <span>{{ $selectedStudent->getFullName() }}</span>
                            </p>
                            <p>
                                <strong>ວັນເດືອນປີເກີດ:</strong>
                                <span>
                                    {{ $selectedStudent->getDateOfBirth() }}
                                </span>
                            </p>

                        </div>
                        <div class="flex flex-col justify-between">
                            <p>
                                <strong>ຫ້ອງຮຽນ:</strong>
                                <span id="studentClass">
                                    {{ $selectedStudent->enrollments[0]?->schoolClass?->class_name ?? 'ບໍ່ມີຂໍ້ມູນ' }}
                                </span>
                            </p>
                            <p>
                                <strong>ຊື່ຜູ້ປົກຄອງ:</strong>
                                {{ optional($selectedStudent->parents->first())->getFullName() ?? 'ບໍ່ມີຂໍ້ມູນ' }}
                            </p>
                            <p>
                                <strong>ເບີໂທຜູ້ປົກຄອງ:</strong>
                                {{ optional($selectedStudent->parents->first())->phone ?? 'ບໍ່ມີຂໍ້ມູນ' }}
                            </p>
                        </div>
                        <div class="">
                            <x-filament::button tag="a"
                                href="{{ route('filament.admin.resources.students.edit', $selectedStudent) }}" color="gray"
                                icon="heroicon-m-pencil-square">
                                ແກ້ໄຂຂໍ້ມູນ
                            </x-filament::button>
                        </div>
                    </div>

            </x-filament::section>

            {{-- ສ່ວນຂໍ້ມູນຄ່າທຳນຽມ --}}
            <x-filament::section class="md:col-span-2">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-currency-dollar" class="h-5 w-5 text-gray-500" />
                        <span>ຂໍ້ມູນຄ່າທຳນຽມ</span>
                    </div>
                </x-slot>
                <form wire:submit="processPaymentAction">
                    {{-- ສະແດງໃຫ້ເລືອກເດືອນ --}}
                    {{ $this->form }}

                    <div style="margin-top: 20px">
                        <x-filament::button type="submit" color="primary" class="w-full" size="lg"
                            icon="heroicon-m-check-circle">
                            ບັນທຶກຂໍ້ມູນ {{-- "Submit" in Lao --}}
                        </x-filament::button>
                    </div>
                </form>

                {{-- ສ່ວນຂໍ້ມູນຄ່າທຳນຽມທີ່ຈະຖືກສ້າງໃນອະນາຄົດ --}}
            </x-filament::section>

            <!-- ແກ້ໄຂສ່ວນ Modal ໃນ payment-page.blade.php -->

            <!-- Confirmation Modal -->
            @if($showConfirmModal)
                <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showConfirmModal') }" x-show="show"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.cancelPayment()">
                        </div>

                        <!-- Modal panel -->
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <x-filament::icon icon="heroicon-o-information-circle" class="h-6 w-6 text-blue-600" />
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                            ຢືນຢັນການຊຳລະເງິນ
                                        </h3>
                                        <div class="mt-4">
                                            @if ($pendingPaymentData)
                                                <div class="space-y-4 w-full">
                                                    <!-- ຂໍ້ມູນນັກຮຽນ -->
                                                    <div class="bg-gray-50 p-4 rounded-lg">
                                                        <h4 class="font-semibold text-lg mb-2">ຂໍ້ມູນນັກຮຽນ</h4>
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <span class="font-medium">ຊື່:
                                                                    {{ $selectedStudent->getFullName() ?? '' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="font-medium">ລະຫັດ:
                                                                    {{ $selectedStudent->student_code ?? '' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- ລາຍລະອຽດການຊຳລະ -->
                                                    <div class="bg-blue-50 p-4 rounded-lg">
                                                        <h4 class="font-semibold text-lg mb-2">ລາຍລະອຽດການຊຳລະ</h4>
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div class="space-y-2">
                                                                <p><span class="font-medium">ເລກໃບບິນ:</span>
                                                                    {{ $pendingPaymentData['receipt_number'] ?? '' }}</p>
                                                                <p><span class="font-medium">ວັນທີ:</span>
                                                                    {{ isset($pendingPaymentData['payment_date']) ? \Carbon\Carbon::parse($pendingPaymentData['payment_date'])->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                                                                </p>
                                                                <p><span class="font-medium">ເງິນສົດ:</span>
                                                                    {{ number_format($pendingPaymentData['cash'] ?? 0) }} ກີບ</p>
                                                                <p><span class="font-medium">ເງິນໂອນ:</span>
                                                                    {{ number_format($pendingPaymentData['transfer'] ?? 0) }} ກີບ
                                                                </p>
                                                            </div>
                                                            <div class="space-y-2">
                                                                <p><span class="font-medium">ຄ່າອາຫານ:</span>
                                                                    {{ number_format($pendingPaymentData['food_money'] ?? 0) }} ກີບ
                                                                </p>
                                                                <p><span class="font-medium">ສ່ວນຫຼຸດ:</span>
                                                                    {{ number_format($pendingPaymentData['discount_amount'] ?? 0) }}
                                                                    ກີບ</p>
                                                                <p><span class="font-medium">ຄ່າປັບ:</span>
                                                                    {{ number_format($pendingPaymentData['late_fee'] ?? 0) }} ກີບ
                                                                </p>
                                                                <p class="text-lg font-bold text-green-600">
                                                                    <span>ລວມທັງໝົດ:</span>
                                                                    {{ number_format($pendingPaymentData['total_amount'] ?? 0) }}
                                                                    ກີບ
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- ເດືອນທີ່ຈ່າຍ -->
                                                    @php
                                                        $tuitionMonths = !empty($pendingPaymentData['tuition_months']) ? json_decode($pendingPaymentData['tuition_months'], true) : [];
                                                        $foodMonths = !empty($pendingPaymentData['food_months']) ? json_decode($pendingPaymentData['food_months'], true) : [];
                                                    @endphp

                                                    @if (!empty($tuitionMonths))
                                                        <div class="bg-green-50 p-4 rounded-lg">
                                                            <h4 class="font-semibold mb-2">ເດືອນຈ່າຍຄ່າຮຽນ</h4>
                                                            <p>{{ implode(', ', $tuitionMonths) }}</p>
                                                        </div>
                                                    @endif

                                                    @if (!empty($foodMonths))
                                                        <div class="bg-yellow-50 p-4 rounded-lg">
                                                            <h4 class="font-semibold mb-2">ເດືອນຈ່າຍຄ່າອາຫານ</h4>
                                                            <p>{{ implode(', ', $foodMonths) }}</p>
                                                        </div>
                                                    @endif

                                                    <!-- ໝາຍເຫດ -->
                                                    @if (!empty($pendingPaymentData['note']))
                                                        <div class="bg-gray-50 p-4 rounded-lg">
                                                            <h4 class="font-semibold mb-2">ໝາຍເຫດ</h4>
                                                            <p>{{ $pendingPaymentData['note'] }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" wire:click="confirmPayment"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 mr-2" />
                                    ຢືນຢັນ ແລະ ພິມໃບບິນ
                                </button>
                                <button type="button" wire:click="cancelPayment"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    <x-filament::icon icon="heroicon-o-x-mark" class="h-5 w-5 mr-2" />
                                    ຍົກເລີກ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ສ່ວນປະຫວັດການຊຳລະເງິນ --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5 text-gray-500" />
                        <span>ປະຫວັດການຊຳລະເງິນ</span>
                    </div>
                </x-slot>

                {{-- ສ່ວນປະຫວັດການຊຳລະເງິນທີ່ຈະຖືກສ້າງໃນອະນາຄົດ --}}

            </x-filament::section>
        @else
            {{-- ສ່ວນແນະນຳການໃຊ້ງານເມື່ອຍັງບໍ່ໄດ້ເລືອກນັກຮຽນ --}}
            <x-filament::section>
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                    <div class="rounded-full bg-primary-50 p-4 mb-4">
                        <x-filament::icon icon="heroicon-o-user-plus" class="h-8 w-8 text-primary-500" />
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">ເລືອກນັກຮຽນເພື່ອຈັດການຄ່າທຳນຽມ</h2>
                    <p class="mt-2 text-sm text-gray-500 max-w-2xl">
                        ກະລຸນາຄົ້ນຫາແລະເລືອກນັກຮຽນເພື່ອເບິ່ງຂໍ້ມູນຄ່າທຳນຽມແລະຈັດການຊຳລະເງິນ.
                        ທ່ານສາມາດຄົ້ນຫາໂດຍໃຊ້ລະຫັດນັກຮຽນຫຼືຊື່-ນາມສະກຸນຂອງນັກຮຽນ.
                    </p>
                    <div class="mt-6">
                        <x-filament::button tag="a" href="{{ route('filament.admin.resources.payments.index') }}"
                            icon="heroicon-m-arrow-left" color="gray">
                            ກັບໄປໜ້າຫຼັກຄ່າທຳນຽມ
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>