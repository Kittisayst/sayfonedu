<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'ແລະ ອີກ :count ລາຍການ',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'ເລືອກ/ຍົກເລີກການເລືອກທຸກລາຍການສຳລັບການດຳເນີນການຈຳນວນຫຼາຍ',
        ],

        'bulk_select_record' => [
            'label' => 'ເລືອກ/ຍົກເລີກການເລືອກ :key ສຳລັບການດຳເນີນການຈຳນວນຫຼາຍ',
        ],

        'search' => [
            'label' => 'ຄົ້ນຫາ',
            'placeholder' => 'ຄົ້ນຫາ',
            'indicator' => 'ຄົ້ນຫາ',
        ],

    ],

    'summary' => [
        'heading' => 'ສະຫຼຸບ',
        'subheading' => 'ສະແດງ :total ລາຍການ',
    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'ສຳເລັດການຈັດລຽງລາຍການ',
        ],

        'enable_reordering' => [
            'label' => 'ຈັດລຽງລາຍການ',
        ],

        'filter' => [
            'label' => 'ຕົວກອງ',
        ],

        'group' => [
            'label' => 'ຈັດກຸ່ມ',
        ],

        'open_bulk_actions' => [
            'label' => 'ເປີດການດຳເນີນການ',
        ],

        'toggle_columns' => [
            'label' => 'ສະແດງ/ເຊື່ອງຖັນ',
        ],

    ],

    'empty' => [
        'heading' => 'ບໍ່ພົບຂໍ້ມູນ',
        'description' => 'ສ້າງ :model ເພື່ອເລີ່ມຕົ້ນ.',
    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'ລຶບຕົວກອງ',
            ],

            'remove_all' => [
                'label' => 'ລຶບຕົວກອງທັງໝົດ',
                'tooltip' => 'ລຶບຕົວກອງທັງໝົດ',
            ],

            'reset' => [
                'label' => 'ຕັ້ງຄ່າໃໝ່',
            ],

        ],

        'indicator' => 'ຕົວກອງທີ່ໃຊ້ງານ',

        'multi_select' => [
            'placeholder' => 'ທັງໝົດ',
        ],

        'select' => [
            'placeholder' => 'ທັງໝົດ',
        ],

        'trashed' => [

            'label' => 'ລາຍການທີ່ຖືກລຶບ',

            'only_trashed' => 'ສະແດງສະເພາະລາຍການທີ່ຖືກລຶບ',

            'with_trashed' => 'ສະແດງລາຍການທີ່ຖືກລຶບ',

            'without_trashed' => 'ບໍ່ສະແດງລາຍການທີ່ຖືກລຶບ',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'ຈັດກຸ່ມຕາມ',
                'placeholder' => 'ຈັດກຸ່ມຕາມ',
            ],

            'direction' => [

                'label' => 'ທິດທາງການຈັດກຸ່ມ',

                'options' => [
                    'asc' => 'ນ້ອຍຫາໃຫຍ່',
                    'desc' => 'ໃຫຍ່ຫານ້ອຍ',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'ລາກແລະວາງລາຍການເພື່ອຈັດລຽງ.',

    'selection_indicator' => [

        'selected_count' => 'ເລືອກ 1 ລາຍການ.|ເລືອກ :count ລາຍການ.',

        'actions' => [

            'select_all' => [
                'label' => 'ເລືອກທັງໝົດ :count ລາຍການ',
            ],

            'deselect_all' => [
                'label' => 'ຍົກເລີກການເລືອກທັງໝົດ',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'ຈັດລຽງຕາມ',
            ],

            'direction' => [

                'label' => 'ທິດທາງການຈັດລຽງ',

                'options' => [
                    'asc' => 'ນ້ອຍຫາໃຫຍ່',
                    'desc' => 'ໃຫຍ່ຫານ້ອຍ',
                ],

            ],

        ],

    ],

];
