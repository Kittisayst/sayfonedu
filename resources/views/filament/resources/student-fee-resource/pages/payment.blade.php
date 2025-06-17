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
                                <span>{{ $selectedStudent->parents[0]?->getFullName() ?? 'ບໍ່ມີຂໍ້ມູນ' }}</span>
                            </p>
                            <p>
                                <strong>ເບີໂທຜູ້ປົກຄອງ:</strong>
                                <span> {{ $selectedStudent->parents[0]?->phone ?? 'ບໍ່ມີຂໍ້ມູນ' }}</span>
                            </p>
                        </div>
                        <div class="">
                            <x-filament::button tag="a"
                                href="{{ route('filament.admin.resources.students.edit', $selectedStudent) }}"
                                color="gray" icon="heroicon-m-pencil-square">
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
                {{-- ສະແດງໃຫ້ເລືອກເດືອນ --}}
                {{ $this->form }}


                {{-- ສ່ວນຂໍ້ມູນຄ່າທຳນຽມທີ່ຈະຖືກສ້າງໃນອະນາຄົດ --}}
            </x-filament::section>

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
                        <x-filament::button tag="a"
                            href="{{ route('filament.admin.resources.student-fees.index') }}"
                            icon="heroicon-m-arrow-left" color="gray">
                            ກັບໄປໜ້າຫຼັກຄ່າທຳນຽມ
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
