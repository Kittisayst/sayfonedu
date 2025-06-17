@props([
    'label' => 'ເຂົ້າສູ່ລະບົບ',
])

<button
    type="submit"
    class="fi-button relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70 rounded-lg fi-color-primary fi-size-md fi-button-size-md gap-1.5 px-3 py-2 text-sm bg-primary-600 text-white hover:bg-primary-500 focus:ring-primary-500/50 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:ring-primary-400/50"
>
    {{ $label }}
</button> 