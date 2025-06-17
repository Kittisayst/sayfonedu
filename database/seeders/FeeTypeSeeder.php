<?php

namespace Database\Seeders;

use App\Models\FeeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $feeTypes = [
            [
                'fee_name' => 'ຄ່າຮຽນພາກຮຽນທີ 1',
                'fee_description' => 'ຄ່າຮຽນສຳລັບພາກຮຽນທີ 1 ຂອງສົກຮຽນ',
                'amount' => 2500000,
                'is_recurring' => true,
                'recurring_interval' => 'yearly',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າຮຽນພາກຮຽນທີ 2',
                'fee_description' => 'ຄ່າຮຽນສຳລັບພາກຮຽນທີ 2 ຂອງສົກຮຽນ',
                'amount' => 2500000,
                'is_recurring' => true,
                'recurring_interval' => 'yearly',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າລົງທະບຽນປະຈຳປີ',
                'fee_description' => 'ຄ່າລົງທະບຽນປະຈຳປີສຳລັບນັກຮຽນໃນແຕ່ລະສົກຮຽນ',
                'amount' => 500000,
                'is_recurring' => true,
                'recurring_interval' => 'yearly',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າພິມປື້ມຮຽນ',
                'fee_description' => 'ຄ່າພິມປື້ມຮຽນປະຈຳສົກຮຽນ',
                'amount' => 350000,
                'is_recurring' => true,
                'recurring_interval' => 'yearly',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າກິດຈະກຳພາຍໃນໂຮງຮຽນ',
                'fee_description' => 'ຄ່າກິດຈະກຳພາຍໃນໂຮງຮຽນປະຈຳສົກຮຽນ',
                'amount' => 300000,
                'is_recurring' => true,
                'recurring_interval' => 'yearly',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າທັດສະນະສຶກສາປະຈຳປີ',
                'fee_description' => 'ຄ່າທັດສະນະສຶກສາປະຈຳປີສຳລັບນັກຮຽນ',
                'amount' => 250000,
                'is_recurring' => true,
                'recurring_interval' => 'yearly',
                'is_mandatory' => false,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າລົງທະບຽນນັກຮຽນໃໝ່',
                'fee_description' => 'ຄ່າລົງທະບຽນນັກຮຽນໃໝ່ ລວມທັງຄ່າໃຊ້ຈ່າຍໃນການອອກບັດນັກຮຽນແລະຄ່າກວດສຸຂະພາບເບື້ອງຕົ້ນ',
                'amount' => 1000000,
                'is_recurring' => false,
                'recurring_interval' => null,
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າອາຫານກາງເວັນ',
                'fee_description' => 'ຄ່າອາຫານກາງເວັນປະຈຳເດືອນຂອງນັກຮຽນ',
                'amount' => 350000,
                'is_recurring' => true,
                'recurring_interval' => 'monthly',
                'is_mandatory' => false,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າລົດຮັບສົ່ງ',
                'fee_description' => 'ຄ່າລົດຮັບສົ່ງປະຈຳເດືອນຂອງນັກຮຽນ',
                'amount' => 400000,
                'is_recurring' => true,
                'recurring_interval' => 'monthly',
                'is_mandatory' => false,
                'is_active' => true,
            ],
            [
                'fee_name' => 'ຄ່າກິດຈະກຳຊົມລົມ',
                'fee_description' => 'ຄ່າກິດຈະກຳຊົມລົມປະຈຳເດືອນ ເຊັ່ນ: ຊົມລົມກິລາ, ຊົມລົມສິລະປະ, ຊົມລົມພາສາ',
                'amount' => 200000,
                'is_recurring' => true,
                'recurring_interval' => 'monthly',
                'is_mandatory' => false,
                'is_active' => true,
            ],
        ];
        
        foreach ($feeTypes as $feeType) {
            FeeType::create($feeType);
        }
    }
}
