<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'discount_name' => 'ສ່ວນຫຼຸດນັກຮຽນຮຽນດີ',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'description' => 'ສ່ວນຫຼຸດ 20% ສຳລັບນັກຮຽນທີ່ມີຜົນການຮຽນດີເດັ່ນໃນປີຜ່ານມາ',
                'is_active' => true,
            ],
            [
                'discount_name' => 'ສ່ວນຫຼຸດພີ່ນ້ອງ',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'description' => 'ສ່ວນຫຼຸດ 15% ສຳລັບນັກຮຽນທີ່ມີອ້າຍ/ເອື້ອຍ/ນ້ອງຮຽນຢູ່ໃນໂຮງຮຽນດຽວກັນ',
                'is_active' => true,
            ],
            [
                'discount_name' => 'ສ່ວນຫຼຸດຊຳລະຄ່າຮຽນເຕັມປີ',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'description' => 'ສ່ວນຫຼຸດ 10% ສຳລັບການຊຳລະຄ່າຮຽນຄົບທັງປີ',
                'is_active' => true,
            ],
            [
                'discount_name' => 'ສ່ວນຫຼຸດນັກຮຽນເກົ່າ',
                'discount_type' => 'percentage',
                'discount_value' => 5.00,
                'description' => 'ສ່ວນຫຼຸດ 5% ສຳລັບນັກຮຽນທີ່ຮຽນໃນໂຮງຮຽນຕໍ່ເນື່ອງເກີນ 3 ປີ',
                'is_active' => true,
            ],
            [
                'discount_name' => 'ສ່ວນຫຼຸດລູກຄູອາຈານ',
                'discount_type' => 'percentage',
                'discount_value' => 25.00,
                'description' => 'ສ່ວນຫຼຸດ 25% ສຳລັບລູກຂອງຄູອາຈານໃນໂຮງຮຽນ',
                'is_active' => true,
            ],
            [
                'discount_name' => 'ສ່ວນຫຼຸດນັກຮຽນຍາກໄຮ້',
                'discount_type' => 'percentage',
                'discount_value' => 50.00,
                'description' => 'ສ່ວນຫຼຸດ 50% ສຳລັບນັກຮຽນທີ່ມີຄອບຄົວຍາກໄຮ້',
                'is_active' => true,
            ],
            [
                'discount_name' => 'ສ່ວນຫຼຸດຊຳລະກ່ອນກຳນົດ',
                'discount_type' => 'fixed',
                'discount_value' => 100000.00,
                'description' => 'ສ່ວນຫຼຸດ 100,000 ກີບ ສຳລັບການຊຳລະກ່ອນກຳນົດ 1 ເດືອນ',
                'is_active' => true,
            ],
            [
                'discount_name' => 'ທຶນການສຶກສາເຕັມຈຳນວນ',
                'discount_type' => 'percentage',
                'discount_value' => 100.00,
                'description' => 'ທຶນການສຶກສາເຕັມຈຳນວນສຳລັບນັກຮຽນທີ່ມີຄວາມສາມາດພິເສດ',
                'is_active' => true,
            ],
        ];
        
        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}
