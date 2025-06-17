<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ນະຄອນຫຼວງວຽງຈັນ
        $vientiane = Province::where('province_name_lao', 'ນະຄອນຫຼວງວຽງຈັນ')->first();
        $vientiane_districts = [
            ['district_name_lao' => 'ຈັນທະບູລີ', 'district_name_en' => 'Chanthabouly'],
            ['district_name_lao' => 'ສີໂຄດຕະບອງ', 'district_name_en' => 'Sikhottabong'],
            ['district_name_lao' => 'ໄຊເສດຖາ', 'district_name_en' => 'Xaysetha'],
            ['district_name_lao' => 'ສີສັດຕະນາກ', 'district_name_en' => 'Sisattanak'],
            ['district_name_lao' => 'ນາຊາຍທອງ', 'district_name_en' => 'Naxaithong'],
            ['district_name_lao' => 'ໄຊທານີ', 'district_name_en' => 'Xaythany'],
            ['district_name_lao' => 'ຫາດຊາຍຟອງ', 'district_name_en' => 'Hadxayfong'],
            ['district_name_lao' => 'ສັງທອງ', 'district_name_en' => 'Sangthong'],
            ['district_name_lao' => 'ປາກງື່ມ', 'district_name_en' => 'Pakngum'],
        ];
        foreach ($vientiane_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $vientiane->province_id,
            ]);
        }

        // ແຂວງຫຼວງພະບາງ
        $luangprabang = Province::where('province_name_lao', 'ຫຼວງພະບາງ')->first();
        $luangprabang_districts = [
            ['district_name_lao' => 'ຫຼວງພະບາງ', 'district_name_en' => 'Luang Prabang'],
            ['district_name_lao' => 'ຊຽງເງິນ', 'district_name_en' => 'Xiengngeun'],
            ['district_name_lao' => 'ນານ', 'district_name_en' => 'Nan'],
            ['district_name_lao' => 'ປາກອູ', 'district_name_en' => 'Pak Ou'],
            ['district_name_lao' => 'ນ້ຳບາກ', 'district_name_en' => 'Nambak'],
            ['district_name_lao' => 'ງອຍ', 'district_name_en' => 'Ngoi'],
            ['district_name_lao' => 'ປາກແຊງ', 'district_name_en' => 'Pak Seng'],
            ['district_name_lao' => 'ພູຄູນ', 'district_name_en' => 'Phoukhoun'],
            ['district_name_lao' => 'ວຽງຄຳ', 'district_name_en' => 'Viengkham'],
            ['district_name_lao' => 'ຈອມເພັດ', 'district_name_en' => 'Chomphet'],
            ['district_name_lao' => 'ວັງວຽງ', 'district_name_en' => 'Vangvieng'],
        ];
        foreach ($luangprabang_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $luangprabang->province_id,
            ]);
        }

        // ແຂວງຫົວພັນ
        $huaphanh = Province::where('province_name_lao', 'ຫົວພັນ')->first();
        $huaphanh_districts = [
            ['district_name_lao' => 'ຊຳເໜືອ', 'district_name_en' => 'Xam Neua'],
            ['district_name_lao' => 'ຊຳໃຕ້', 'district_name_en' => 'Xam Tai'],
            ['district_name_lao' => 'ວຽງໄຊ', 'district_name_en' => 'Viengxay'],
            ['district_name_lao' => 'ຫົວເມືອງ', 'district_name_en' => 'Houamuang'],
            ['district_name_lao' => 'ຊອນ', 'district_name_en' => 'Xon'],
            ['district_name_lao' => 'ຊຽງຄໍ້', 'district_name_en' => 'Xiengkho'],
            ['district_name_lao' => 'ກອດ', 'district_name_en' => 'Kout'],
            ['district_name_lao' => 'ສົບເບົາ', 'district_name_en' => 'Sop Bao'],
        ];
        foreach ($huaphanh_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $huaphanh->province_id,
            ]);
        }

        // ແຂວງຊຽງຂວາງ
        $xiengkhuang = Province::where('province_name_lao', 'ຊຽງຂວາງ')->first();
        $xiengkhuang_districts = [
            ['district_name_lao' => 'ພູກູດ', 'district_name_en' => 'Phoukout'],
            ['district_name_lao' => 'ຄຳ', 'district_name_en' => 'Kham'],
            ['district_name_lao' => 'ໜອງແຮດ', 'district_name_en' => 'Nong Het'],
            ['district_name_lao' => 'ປາກແຊງ', 'district_name_en' => 'Pek'],
        ];
        foreach ($xiengkhuang_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $xiengkhuang->province_id,
            ]);
        }

        // ແຂວງວຽງຈັນ
        $vientiane_province = Province::where('province_name_lao', 'ວຽງຈັນ')->first();
        $vientiane_province_districts = [
            ['district_name_lao' => 'ໂພນສະຫວັນ', 'district_name_en' => 'Phonxay'],
            ['district_name_lao' => 'ທຸລະຄົມ', 'district_name_en' => 'Thoulakhom'],
            ['district_name_lao' => 'ແກ້ວອຸດົມ', 'district_name_en' => 'Keo Oudom'],
            ['district_name_lao' => 'ກາສີ', 'district_name_en' => 'Kasy'],
            ['district_name_lao' => 'ວັງວຽງ', 'district_name_en' => 'Vangvieng'],
            ['district_name_lao' => 'ເຟືອງ', 'district_name_en' => 'Feuang'],
            ['district_name_lao' => 'ຊະນະຄາມ', 'district_name_en' => 'Xanakham'],
            ['district_name_lao' => 'ມະຫາໄຊ', 'district_name_en' => 'Mahaxay'],
            ['district_name_lao' => 'ວັງຊຽງ', 'district_name_en' => 'Vangxieng'],
            ['district_name_lao' => 'ຫົວສະຫວັນ', 'district_name_en' => 'Homsavanh'],
        ];
        foreach ($vientiane_province_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $vientiane_province->province_id,
            ]);
        }

        // ແຂວງບໍລິຄຳໄຊ
        $bolikhamxay = Province::where('province_name_lao', 'ບໍລິຄຳໄຊ')->first();
        $bolikhamxay_districts = [
            ['district_name_lao' => 'ປາກຊັນ', 'district_name_en' => 'Pakxan'],
            ['district_name_lao' => 'ທ່າພະບາດ', 'district_name_en' => 'Thaphabat'],
            ['district_name_lao' => 'ປາກກະດິງ', 'district_name_en' => 'Pakkading'],
            ['district_name_lao' => 'ບໍລິຄັນ', 'district_name_en' => 'Bolikhan'],
            ['district_name_lao' => 'ຄຳເກີດ', 'district_name_en' => 'Khamkeut'],
            ['district_name_lao' => 'ວຽງທອງ', 'district_name_en' => 'Viengthong'],
        ];
        foreach ($bolikhamxay_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $bolikhamxay->province_id,
            ]);
        }

        // ແຂວງຄຳມ່ວນ
        $khammouane = Province::where('province_name_lao', 'ຄຳມ່ວນ')->first();
        $khammouane_districts = [
            ['district_name_lao' => 'ທ່າແຂກ', 'district_name_en' => 'Thakhek'],
            ['district_name_lao' => 'ມະຫາໄຊ', 'district_name_en' => 'Mahaxay'],
            ['district_name_lao' => 'ນູນ', 'district_name_en' => 'Nongbok'],
            ['district_name_lao' => 'ຫີນບູນ', 'district_name_en' => 'Hinboun'],
            ['district_name_lao' => 'ຍົມມະລາດ', 'district_name_en' => 'Yommalath'],
            ['district_name_lao' => 'ບົວລະພາ', 'district_name_en' => 'Boualapha'],
            ['district_name_lao' => 'ນາກາຍ', 'district_name_en' => 'Nakai'],
            ['district_name_lao' => 'ເຊບັ້ງໄຟ', 'district_name_en' => 'Sebangfai'],
            ['district_name_lao' => 'ໄຊບົວທອງ', 'district_name_en' => 'Xaybuathong'],
        ];
        foreach ($khammouane_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $khammouane->province_id,
            ]);
        }

        // ແຂວງສະຫວັນນະເຂດ
        $savannakhet = Province::where('province_name_lao', 'ສະຫວັນນະເຂດ')->first();
        $savannakhet_districts = [
            ['district_name_lao' => 'ໄກສອນພົມວິຫານ', 'district_name_en' => 'Kaysone Phomvihane'],
            ['district_name_lao' => 'ອຸທຸມພອນ', 'district_name_en' => 'Outhoumphone'],
            ['district_name_lao' => 'ອາດສະພັງທອງ', 'district_name_en' => 'Atsaphangthong'],
            ['district_name_lao' => 'ພີນ', 'district_name_en' => 'Phin'],
            ['district_name_lao' => 'ເຊໂປນ', 'district_name_en' => 'Sepon'],
            ['district_name_lao' => 'ນອງ', 'district_name_en' => 'Nong'],
            ['district_name_lao' => 'ທ່າປາງທອງ', 'district_name_en' => 'Thapangthong'],
            ['district_name_lao' => 'ສອງຄອນ', 'district_name_en' => 'Songkhone'],
            ['district_name_lao' => 'ຈຳພອນ', 'district_name_en' => 'Champhone'],
            ['district_name_lao' => 'ໄຊບູລີ', 'district_name_en' => 'Xaybouly'],
            ['district_name_lao' => 'ວິລະບູລີ', 'district_name_en' => 'Vilabouly'],
            ['district_name_lao' => 'ອາດສະພອນ', 'district_name_en' => 'Atsaphone'],
            ['district_name_lao' => 'ໄຊພູທອງ', 'district_name_en' => 'Xayphouthong'],
            ['district_name_lao' => 'ທ່າແຕງ', 'district_name_en' => 'Tha Teng'],
            ['district_name_lao' => 'ສະໝ້ວຍ', 'district_name_en' => 'Samouay'],
        ];
        foreach ($savannakhet_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $savannakhet->province_id,
            ]);
        }

        // ແຂວງສາລະວັນ
        $salavan = Province::where('province_name_lao', 'ສາລະວັນ')->first();
        $salavan_districts = [
            ['district_name_lao' => 'ສາລະວັນ', 'district_name_en' => 'Salavan'],
            ['district_name_lao' => 'ຕະໂອ້ຍ', 'district_name_en' => 'Ta Oy'],
            ['district_name_lao' => 'ຕຸ້ມລານ', 'district_name_en' => 'Toumlane'],
            ['district_name_lao' => 'ລະຄອນເພັງ', 'district_name_en' => 'Lakhonepheng'],
            ['district_name_lao' => 'ວາປີ', 'district_name_en' => 'Vapy'],
            ['district_name_lao' => 'ຄົງເຊໂດນ', 'district_name_en' => 'Khongsedone'],
            ['district_name_lao' => 'ລະມາມ', 'district_name_en' => 'Lamam'],
            ['district_name_lao' => 'ສະມ້ວຍ', 'district_name_en' => 'Samouay'],
        ];
        foreach ($salavan_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $salavan->province_id,
            ]);
        }

        // ແຂວງເຊກອງ
        $sekong = Province::where('province_name_lao', 'ເຊກອງ')->first();
        $sekong_districts = [
            ['district_name_lao' => 'ເຊກອງ', 'district_name_en' => 'Sekong'],
            ['district_name_lao' => 'ລະເມືອງ', 'district_name_en' => 'Lamam'],
            ['district_name_lao' => 'ກະລືມ', 'district_name_en' => 'Kaleum'],
            ['district_name_lao' => 'ດັກເຈງ', 'district_name_en' => 'Dakcheung'],
        ];
        foreach ($sekong_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $sekong->province_id,
            ]);
        }

        // ແຂວງຈຳປາສັກ
        $champasak = Province::where('province_name_lao', 'ຈຳປາສັກ')->first();
        $champasak_districts = [
            ['district_name_lao' => 'ປາກເຊ', 'district_name_en' => 'Pakse'],
            ['district_name_lao' => 'ຊະນະສົມບູນ', 'district_name_en' => 'Sanasomboun'],
            ['district_name_lao' => 'ບາຈຽງຈະເລີນສຸກ', 'district_name_en' => 'Bachiangchaleunsouk'],
            ['district_name_lao' => 'ປາກຊ່ອງ', 'district_name_en' => 'Pakxong'],
            ['district_name_lao' => 'ປະທຸມພອນ', 'district_name_en' => 'Pathoumphone'],
            ['district_name_lao' => 'ໂພນທອງ', 'district_name_en' => 'Phonthong'],
            ['district_name_lao' => 'ຈຳປາສັກ', 'district_name_en' => 'Champasak'],
            ['district_name_lao' => 'ສຸຂຸມາ', 'district_name_en' => 'Sukhuma'],
            ['district_name_lao' => 'ມຸນລະປະໂມກ', 'district_name_en' => 'Mounlapamok'],
            ['district_name_lao' => 'ໂຂງ', 'district_name_en' => 'Khong'],
        ];
        foreach ($champasak_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $champasak->province_id,
            ]);
        }

        // ແຂວງອັດຕະປື
        $attapeu = Province::where('province_name_lao', 'ອັດຕະປື')->first();
        $attapeu_districts = [
            ['district_name_lao' => 'ສານໄຊ', 'district_name_en' => 'Sanxay'],
            ['district_name_lao' => 'ສາມັກຄີໄຊ', 'district_name_en' => 'Samakkhixay'],
            ['district_name_lao' => 'ສະໝ້ວຍ', 'district_name_en' => 'Samouay'],
            ['district_name_lao' => 'ພູວົງ', 'district_name_en' => 'Phouvong'],
            ['district_name_lao' => 'ໄຊເສດຖາ', 'district_name_en' => 'Xaysetha'],
        ];
        foreach ($attapeu_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $attapeu->province_id,
            ]);
        }

        // ແຂວງໄຊຍະບູລີ
        $xayaboury = Province::where('province_name_lao', 'ໄຊຍະບູລີ')->first();
        $xayaboury_districts = [
            ['district_name_lao' => 'ໄຊຍະບູລີ', 'district_name_en' => 'Xayaboury'],
            ['district_name_lao' => 'ຄອບ', 'district_name_en' => 'Khop'],
            ['district_name_lao' => 'ຫົງສາ', 'district_name_en' => 'Hongsa'],
            ['district_name_lao' => 'ເງິນ', 'district_name_en' => 'Ngeun'],
            ['district_name_lao' => 'ຊຽງຮ່ອນ', 'district_name_en' => 'Xianghon'],
            ['district_name_lao' => 'ພຽງ', 'district_name_en' => 'Phieng'],
            ['district_name_lao' => 'ປາກລາຍ', 'district_name_en' => 'Paklai'],
            ['district_name_lao' => 'ແກ່ນທ້າວ', 'district_name_en' => 'Kenethao'],
            ['district_name_lao' => 'ບໍ່ແຕນ', 'district_name_en' => 'Botene'],
            ['district_name_lao' => 'ທົ່ງມີໄຊ', 'district_name_en' => 'Thongmyxay'],
            ['district_name_lao' => 'ໄຊຊະຖານ', 'district_name_en' => 'Xaysathan'],
        ];
        foreach ($xayaboury_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $xayaboury->province_id,
            ]);
        }

        // ແຂວງບໍ່ແກ້ວ
        $bokeo = Province::where('province_name_lao', 'ບໍ່ແກ້ວ')->first();
        $bokeo_districts = [
            ['district_name_lao' => 'ຫ້ວຍຊາຍ', 'district_name_en' => 'Houayxay'],
            ['district_name_lao' => 'ຕົ້ນເຜິ້ງ', 'district_name_en' => 'Tonpheung'],
            ['district_name_lao' => 'ເມິງ', 'district_name_en' => 'Meung'],
            ['district_name_lao' => 'ຜາອຸດົມ', 'district_name_en' => 'Pha Oudom'],
            ['district_name_lao' => 'ປາກທາ', 'district_name_en' => 'Paktha'],
        ];
        foreach ($bokeo_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $bokeo->province_id,
            ]);
        }

        // ແຂວງຫຼວງນ້ຳທາ
        $luangnamtha = Province::where('province_name_lao', 'ຫຼວງນ້ຳທາ')->first();
        $luangnamtha_districts = [
            ['district_name_lao' => 'ຫຼວງນ້ຳທາ', 'district_name_en' => 'Luang Namtha'],
            ['district_name_lao' => 'ສິງ', 'district_name_en' => 'Sing'],
            ['district_name_lao' => 'ລອງ', 'district_name_en' => 'Long'],
            ['district_name_lao' => 'ວຽງພູຄາ', 'district_name_en' => 'Viengphoukha'],
            ['district_name_lao' => 'ນາແລ', 'district_name_en' => 'Nale'],
        ];
        foreach ($luangnamtha_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $luangnamtha->province_id,
            ]);
        }

        // ແຂວງອຸດົມໄຊ
        $oudomxay = Province::where('province_name_lao', 'ອຸດົມໄຊ')->first();
        $oudomxay_districts = [
            ['district_name_lao' => 'ໄຊ', 'district_name_en' => 'Xay'],
            ['district_name_lao' => 'ລາ', 'district_name_en' => 'La'],
            ['district_name_lao' => 'ນາ', 'district_name_en' => 'Na'],
            ['district_name_lao' => 'ເງິນ', 'district_name_en' => 'Nga'],
            ['district_name_lao' => 'ບໍ່', 'district_name_en' => 'Beng'],
            ['district_name_lao' => 'ຫົວເມືອງ', 'district_name_en' => 'Houne'],
            ['district_name_lao' => 'ປາກແບ່ງ', 'district_name_en' => 'Pakbeng'],
            ['district_name_lao' => 'ນ້ຳທາ', 'district_name_en' => 'Namtha'],
        ];
        foreach ($oudomxay_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $oudomxay->province_id,
            ]);
        }

        // ແຂວງຜົ້ງສາລີ
        $phongsaly = Province::where('province_name_lao', 'ຜົ້ງສາລີ')->first();
        $phongsaly_districts = [
            ['district_name_lao' => 'ຜົ້ງສາລີ', 'district_name_en' => 'Phongsaly'],
            ['district_name_lao' => 'ໃໝ່', 'district_name_en' => 'May'],
            ['district_name_lao' => 'ຂວາ', 'district_name_en' => 'Khoua'],
            ['district_name_lao' => 'ສຳພັນ', 'district_name_en' => 'Samphanh'],
            ['district_name_lao' => 'ບູນເຫນືອ', 'district_name_en' => 'Bounneua'],
            ['district_name_lao' => 'ຍອດອູ', 'district_name_en' => 'Yot Ou'],
            ['district_name_lao' => 'ບູນໃຕ້', 'district_name_en' => 'Bountai'],
        ];
        foreach ($phongsaly_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $phongsaly->province_id,
            ]);
        }

        // ແຂວງໄຊສົມບູນ
        $xaysomboun = Province::where('province_name_lao', 'ໄຊສົມບູນ')->first();
        $xaysomboun_districts = [
            ['district_name_lao' => 'ອະນຸວົງ', 'district_name_en' => 'Anouvong'],
            ['district_name_lao' => 'ລະຄອນເພັງ', 'district_name_en' => 'Longchaeng'],
            ['district_name_lao' => 'ທ່າແຕງ', 'district_name_en' => 'Thathom'],
        ];
        foreach ($xaysomboun_districts as $district) {
            District::create([
                'district_name_lao' => $district['district_name_lao'],
                'district_name_en' => $district['district_name_en'],
                'province_id' => $xaysomboun->province_id,
            ]);
        }
    }
}
