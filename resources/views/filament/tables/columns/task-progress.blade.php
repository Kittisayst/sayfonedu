@php
    $state = $getState() ?? 0;
    $maxValue = $getMaxValue() ?? 100;
    $percentage = $state !== null ? min(round(($state / $maxValue) * 100), 100) : 0;
    
    // ກຳນົດສີຕາມຄ່າ percentage
    $backgroundColor = match (true) {
        $percentage >= 80 => '#10b981', // ສີຂຽວ (success)
        $percentage >= 50 => '#f59e0b', // ສີເຫຼືອງ (warning)
        default => '#ef4444', // ສີແດງ (danger)
    };
@endphp

<div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden relative">
    <div
        style="width: {{ $percentage }}%; background-color: {{ $backgroundColor }}; height: 100%;"
        class="transition-all duration-500"
    ></div>
    <span class="absolute inset-0 flex items-center justify-center text-xs text-white font-medium">{{ $percentage }}%</span>
</div>