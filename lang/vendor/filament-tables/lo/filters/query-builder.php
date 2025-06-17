<?php

return [
    'label' => 'ເຄື່ອງມືສ້າງຄຳຖາມ',

    'form' => [
        'operator' => [
            'label' => 'ຕົວດຳເນີນການ',
        ],

        'or_groups' => [
            'label' => 'ກຸ່ມ',
            'block' => [
                'label' => 'ແຍກເງື່ອນໄຂ (ຫຼື)',
                'or' => 'ຫຼື',
            ],
        ],

        'rules' => [
            'label' => 'ເງື່ອນໄຂ',
            'item' => [
                'and' => 'ແລະ',
            ],
        ],
    ],

    'no_rules' => '(ບໍ່ມີເງື່ອນໄຂ)',

    'item_separators' => [
        'and' => 'ແລະ',
        'or' => 'ຫຼື',
    ],

    'operators' => [
        'is_filled' => [
            'label' => [
                'direct' => 'ມີຂໍ້ມູນ',
                'inverse' => 'ວ່າງເປົ່າ',
            ],
            'summary' => [
                'direct' => ':attribute ມີຂໍ້ມູນ',
                'inverse' => ':attribute ວ່າງເປົ່າ',
            ],
        ],

        'boolean' => [
            'is_true' => [
                'label' => [
                    'direct' => 'ເປັນຈິງ',
                    'inverse' => 'ເປັນເທັດ',
                ],
                'summary' => [
                    'direct' => ':attribute ເປັນຈິງ',
                    'inverse' => ':attribute ເປັນເທັດ',
                ],
            ],
        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Is after',
                    'inverse' => 'Is not after',
                ],

                'summary' => [
                    'direct' => ':attribute is after :date',
                    'inverse' => ':attribute is not after :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Is before',
                    'inverse' => 'Is not before',
                ],

                'summary' => [
                    'direct' => ':attribute is before :date',
                    'inverse' => ':attribute is not before :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Is date',
                    'inverse' => 'Is not date',
                ],

                'summary' => [
                    'direct' => ':attribute is :date',
                    'inverse' => ':attribute is not :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Is month',
                    'inverse' => 'Is not month',
                ],

                'summary' => [
                    'direct' => ':attribute is :month',
                    'inverse' => ':attribute is not :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Is year',
                    'inverse' => 'Is not year',
                ],

                'summary' => [
                    'direct' => ':attribute is :year',
                    'inverse' => ':attribute is not :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Date',
                ],

                'month' => [
                    'label' => 'Month',
                ],

                'year' => [
                    'label' => 'Year',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Equals',
                    'inverse' => 'Does not equal',
                ],

                'summary' => [
                    'direct' => ':attribute equals :number',
                    'inverse' => ':attribute does not equal :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Is maximum',
                    'inverse' => 'Is greater than',
                ],

                'summary' => [
                    'direct' => ':attribute is maximum :number',
                    'inverse' => ':attribute is greater than :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Is minimum',
                    'inverse' => 'Is less than',
                ],

                'summary' => [
                    'direct' => ':attribute is minimum :number',
                    'inverse' => ':attribute is less than :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Average',
                    'summary' => 'Average :attribute',
                ],

                'max' => [
                    'label' => 'Max',
                    'summary' => 'Max :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Sum',
                    'summary' => 'Sum of :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Aggregate',
                ],

                'number' => [
                    'label' => 'Number',
                ],

            ],

        ],

        'relationship' => [
            'equals' => [
                'label' => [
                    'direct' => 'ມີ',
                    'inverse' => 'ບໍ່ມີ',
                ],
                'summary' => [
                    'direct' => 'ມີ :count :relationship',
                    'inverse' => 'ບໍ່ມີ :count :relationship',
                ],
            ],

            'has_max' => [
                'label' => [
                    'direct' => 'ມີສູງສຸດ',
                    'inverse' => 'ມີຫຼາຍກວ່າ',
                ],
                'summary' => [
                    'direct' => 'ມີສູງສຸດ :count :relationship',
                    'inverse' => 'ມີຫຼາຍກວ່າ :count :relationship',
                ],
            ],

            'has_min' => [
                'label' => [
                    'direct' => 'ມີຕ່ຳສຸດ',
                    'inverse' => 'ມີໜ້ອຍກວ່າ',
                ],
                'summary' => [
                    'direct' => 'ມີຕ່ຳສຸດ :count :relationship',
                    'inverse' => 'ມີໜ້ອຍກວ່າ :count :relationship',
                ],
            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Is empty',
                    'inverse' => 'Is not empty',
                ],

                'summary' => [
                    'direct' => ':relationship is empty',
                    'inverse' => ':relationship is not empty',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Is',
                        'inverse' => 'Is not',
                    ],

                    'multiple' => [
                        'direct' => 'Contains',
                        'inverse' => 'Does not contain',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship is :values',
                        'inverse' => ':relationship is not :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship contains :values',
                        'inverse' => ':relationship does not contain :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' or ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Value',
                    ],

                    'values' => [
                        'label' => 'Values',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Count',
                ],

            ],

        ],

        'select' => [
            'is' => [
                'label' => [
                    'direct' => 'ແມ່ນ',
                    'inverse' => 'ບໍ່ແມ່ນ',
                ],
                'summary' => [
                    'direct' => ':attribute ແມ່ນ :values',
                    'inverse' => ':attribute ບໍ່ແມ່ນ :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ຫຼື ',
                    ],
                ],
            ],
        ],

        'text' => [
            'contains' => [
                'label' => [
                    'direct' => 'ປະກອບມີ',
                    'inverse' => 'ບໍ່ປະກອບມີ',
                ],
                'summary' => [
                    'direct' => ':attribute ປະກອບມີ :text',
                    'inverse' => ':attribute ບໍ່ປະກອບມີ :text',
                ],
            ],

            'ends_with' => [
                'label' => [
                    'direct' => 'ລົງທ້າຍດ້ວຍ',
                    'inverse' => 'ບໍ່ລົງທ້າຍດ້ວຍ',
                ],
                'summary' => [
                    'direct' => ':attribute ລົງທ້າຍດ້ວຍ :text',
                    'inverse' => ':attribute ບໍ່ລົງທ້າຍດ້ວຍ :text',
                ],
            ],

            'equals' => [

                'label' => [
                    'direct' => 'Equals',
                    'inverse' => 'Does not equal',
                ],

                'summary' => [
                    'direct' => ':attribute equals :text',
                    'inverse' => ':attribute does not equal :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Starts with',
                    'inverse' => 'Does not start with',
                ],

                'summary' => [
                    'direct' => ':attribute starts with :text',
                    'inverse' => ':attribute does not start with :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Text',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Add rule',
        ],

        'add_rule_group' => [
            'label' => 'Add rule group',
        ],

    ],

];



