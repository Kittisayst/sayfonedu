@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // ປ່ຽນຂໍ້ຄວາມປຸ່ມ Save
        const saveButtons = document.querySelectorAll('button[type="submit"]');
        saveButtons.forEach(button => {
            if (button.textContent.includes('Save')) {
                button.textContent = button.textContent.replace('Save', '{{ $saveText }}');
            }
        });

        // ປ່ຽນຂໍ້ຄວາມປຸ່ມ Cancel
        const cancelButtons = document.querySelectorAll('button[type="button"]');
        cancelButtons.forEach(button => {
            if (button.textContent.includes('Cancel')) {
                button.textContent = button.textContent.replace('Cancel', '{{ $cancelText }}');
            }
        });
    });
</script>
@endpush 