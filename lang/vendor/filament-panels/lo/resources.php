<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'ແລະ ອີກ :count ລາຍການ',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'ຄົ້ນຫາ',
            'placeholder' => 'ຄົ້ນຫາ',
        ],

    ],

    'pagination' => [

        'label' => 'ການນຳທາງໜ້າ',

        'overview' => 'ກຳລັງສະແດງ :first ຫາ :last ຈາກທັງໝົດ :total ລາຍການ',

        'fields' => [

            'records_per_page' => [
                'label' => 'ຕໍ່ໜ້າ',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'ໄປທີ່ໜ້າ :page',
            ],

            'next' => [
                'label' => 'ໜ້າຕໍ່ໄປ',
            ],

            'previous' => [
                'label' => 'ໜ້າກ່ອນ',
            ],

        ],

    ],

    'buttons' => [

        'create' => [
            'label' => 'ສ້າງໃໝ່',
        ],

        'delete' => [
            'label' => 'ລຶບ',
        ],

        'edit' => [
            'label' => 'ແກ້ໄຂ',
        ],

        'restore' => [
            'label' => 'ກູ້ຄືນ',
        ],

        'force_delete' => [
            'label' => 'ລຶບຖາວອນ',
        ],

    ],

    'empty' => [
        'heading' => 'ບໍ່ພົບຂໍ້ມູນ',
    ],

    'modal' => [

        'confirmation' => [
            'title' => 'ທ່ານແນ່ໃຈບໍ່?',
            'buttons' => [
                'cancel' => [
                    'label' => 'ຍົກເລີກ',
                ],
                'confirm' => [
                    'label' => 'ຢືນຢັນ',
                ],
            ],
        ],

    ],

    'messages' => [
        'created' => 'ສ້າງສຳເລັດ',
        'deleted' => 'ລຶບສຳເລັດ',
        'restored' => 'ກູ້ຄືນສຳເລັດ',
        'updated' => 'ແກ້ໄຂສຳເລັດ',
    ],

];