<?php

return [

    'label' => 'Pagination navigation',

    'overview' => '{1} ສະແດງ 1 result|[2,*] ສະແດງ :first ຫາ :last ຈາກ :total ລາຍການ',

    'fields' => [

        'records_per_page' => [

            'label' => 'ຕໍ່ໜ້າ',

            'options' => [
                'all' => 'ທັງໝົດ',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'ທຳອິດ',
        ],

        'go_to_page' => [
            'label' => 'Go to page :page',
        ],

        'last' => [
            'label' => 'ສຸດທ້າຍ',
        ],

        'next' => [
            'label' => 'ຕໍ່ໄປ',
        ],

        'previous' => [
            'label' => 'ກ່ອນໜ້າ',
        ],

    ],

];
