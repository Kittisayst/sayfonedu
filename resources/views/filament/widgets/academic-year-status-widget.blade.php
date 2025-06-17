<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- ສ່ວນຫົວ -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-primary-50 dark:bg-primary-900/50 rounded-lg">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            ສະຖານະສົກຮຽນປັດຈຸບັນ
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            ຂໍ້ມູນສົກຮຽນປັດຈຸບັນ ແລະ ຄວາມຄືບໜ້າ
                        </p>
                    </div>
                </div>
                @if($hasCurrentYear)
                    <div class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-700 dark:text-{{ $statusColor }}-300">
                        <span class="w-2 h-2 rounded-full bg-{{ $statusColor }}-500 mr-2"></span>
                        @switch($academicYear->status)
                            @case('upcoming')
                                ກຳລັງຈະເລີ່ມ
                                @break
                            @case('active')
                                ກຳລັງດຳເນີນຢູ່
                                @break
                            @case('completed')
                                ສິ້ນສຸດແລ້ວ
                                @break
                            @default
                                {{ $academicYear->status }}
                        @endswitch
                    </div>
                @endif
            </div>

            @if(!$hasCurrentYear)
                <!-- ສະແດງຂໍ້ຄວາມເມື່ອບໍ່ມີສົກຮຽນ -->
                <div class="flex flex-col items-center justify-center p-8 bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                        <x-heroicon-o-exclamation-circle class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                    </div>
                    <div class="text-gray-500 dark:text-gray-400 text-center mb-4">
                        ບໍ່ພົບຂໍ້ມູນສົກຮຽນປັດຈຸບັນ
                    </div>
                    <a href="{{ route('filament.admin.resources.academic-years.create') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800 transition-colors">
                        <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                        ຕັ້ງຄ່າສົກຮຽນໃໝ່
                    </a>
                </div>
            @else
                <!-- ສະແດງຂໍ້ມູນສົກຮຽນ -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- ຂໍ້ມູນສົກຮຽນ -->
                        <div class="col-span-1 md:col-span-2 space-y-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $academicYear->year_name }}</h3>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    <x-heroicon-o-calendar class="w-4 h-4 mr-1" />
                                    {{ \Carbon\Carbon::parse($academicYear->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($academicYear->end_date)->format('d/m/Y') }}
                                </div>
                            </div>
                            
                            <!-- ຄວາມຄືບໜ້າສົກຮຽນ -->
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ຄວາມຄືບໜ້າ</span>
                                    <span class="text-sm font-medium text-{{ $statusColor }}-600 dark:text-{{ $statusColor }}-400">{{ $progressPercentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-{{ $statusColor }}-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ຂໍ້ມູນສະຖິຕິ -->
                        <div class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $daysRemaining }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 text-center mt-1">ວັນທີ່ເຫຼືອ</div>
                        </div>
                        
                        <div class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $newStudents }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 text-center mt-1">ນັກຮຽນໃໝ່</div>
                        </div>
                    </div>
                    
                    <!-- ໄທມ໌ໄລນ໌ສົກຮຽນ -->
                    <div class="mt-8">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">ໄທມ໌ໄລນ໌ສົກຮຽນ</h4>
                        <div class="relative">
                            <!-- ເສັ້ນເຊື່ອມຕໍ່ -->
                            <div class="absolute top-5 left-2.5 h-full w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                            
                            <ol class="relative space-y-6">
                                @foreach($timeline as $item)
                                    <li class="ml-6">
                                        <span class="absolute flex items-center justify-center w-5 h-5 rounded-full -left-2.5 ring-4 ring-white dark:ring-gray-800 {{ $item['isPast'] ? 'bg-green-200 dark:bg-green-900' : ($item['isCurrent'] ? 'bg-blue-500 dark:bg-blue-700' : 'bg-gray-200 dark:bg-gray-700') }}">
                                            @if($item['isPast'])
                                                <x-heroicon-s-check class="w-3 h-3 text-green-600 dark:text-green-300" />
                                            @endif
                                        </span>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $item['label'] }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $item['date'] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>