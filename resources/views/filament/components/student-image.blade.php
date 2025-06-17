@php
    $student = $getState();
@endphp

<div class="max-w-md bg-white rounded-xl shadow-md overflow-hidden p-4">
    <div class="flex flex-row items-start gap-4">
        {{-- Image section (left side) --}}
        <div class="flex-shrink-0 mr-4">
            @if($student && $student->profile_image)
                <img src="{{ Storage::url($student->profile_image) }}" alt="ຮູບນັກຮຽນ"
                    class="w-32 h-32 object-cover rounded-lg border border-gray-200 shadow-sm">
            @else
                <div class="w-32 h-32 flex items-center justify-center bg-gray-100 rounded-lg">
                    <span class="text-gray-400 text-sm">ບໍ່ມີຮູບ</span>
                </div>
            @endif
        </div>

        {{-- Student information (right side) --}}
        <div class="flex-1">
            @if($student)
                {{-- Student code at the top --}}
                <div class="mb-1">
                    <span class="font-bold text-xl">{{ $student->student_code ?? '0001' }}</span>
                </div>
                <div class="text-sm mb-3">ລະຫັດນັກຮຽນ</div>

                {{-- Student details with simplified format --}}
                <div class="space-y-1 text-sm mb-2">
                    <div><span class="font-semibold">ຊັ້ນຮຽນ:</span> {{ $student->class->class_name ?? '-' }}</div>
                    <div><span class="font-semibold">ຊື່ຜູ້ປົກຄອງ:</span> {{ $student->parent_name }}</div>
                    <div><span class="font-semibold">ເບີໂທລະສັບ:</span> {{ $student->phone }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom border line --}}
    <div class="mt-2 border-t border-gray-200"></div>

    {{-- School name footer --}}
    <div class="mt-2 text-center">
        <div class="text-xs text-gray-500">ໂຮງຮຽນມັດທະຍົມສົມບູນ</div>
        <div class="text-xs font-semibold text-gray-700">Secondary School</div>
    </div>
</div>