<?php

namespace App\Utils;

class Money
{
    /**
     * ແປງຈຳນວນເງິນໃຫ້ເປັນຮູບແບບ LAK (ກີບ) ທີ່ມີ comma
     */
    public static function toLAK($amount): string
    {
        return number_format($amount, 0, '', ',');
    }

    /**
     * ແປງສະຕຣິງທີ່ມີ comma ໃຫ້ເປັນ integer (ສຳລັບ cash, transfer, food_money)
     */
    public static function toInt($amount): int
    {
        if (is_null($amount) || $amount === '') {
            return 0;
        }
        return (int) str_replace(',', '', (string) $amount);
    }

    /**
     * ✅ ເພີ່ມ: ແປງສະຕຣິງທີ່ມີ comma ໃຫ້ເປັນ decimal (ສຳລັບ discount_amount, late_fee, total_amount)
     */
    public static function toDecimal($amount): float
    {
        if (is_null($amount) || $amount === '') {
            return 0.0;
        }
        
        // ລຶບ comma ແລະ spaces
        $cleaned = str_replace([',', ' '], '', (string) $amount);
        
        return is_numeric($cleaned) ? (float) $cleaned : 0.0;
    }

    /**
     * ✅ ເພີ່ມ: ແປງຄ່າຈາກຖານຂໍ້ມູນໃຫ້ເປັນຮູບແບບທີ່ສະແດງໃນ form (integer fields)
     */
    public static function fromIntForDisplay(int $amount): string
    {
        return (string) $amount;
    }

    /**
     * ✅ ເພີ່ມ: ແປງຄ່າຈາກຖານຂໍ້ມູນໃຫ້ເປັນຮູບແບບທີ່ສະແດງໃນ form (decimal fields)
     */
    public static function fromDecimalForDisplay($amount): string
    {
        if (is_null($amount)) {
            return '0';
        }
        return (string) floatval($amount);
    }

    /**
     * ✅ ເພີ່ມ: ຈັດຮູບແບບເງິນໃຫ້ສວຍງາມສຳລັບສະແດງຜົນ
     */
    public static function format($amount): string
    {
        if (is_null($amount)) {
            return '0';
        }
        return number_format((float) $amount, 0, '.', ',');
    }

    /**
     * ✅ ເພີ່ມ: ແປງຄ່າເງິນຈາກຟອມໃຫ້ເປັນຮູບແບບທີ່ບັນທຶກໃນຖານຂໍ້ມູນ
     */
    public static function parseFormValue($value): float
    {
        if (is_null($value) || $value === '') {
            return 0.0;
        }

        // ລຶບ comma, spaces, currency symbols
        $cleaned = str_replace([',', ' ', '₭', 'ກີບ'], '', (string) $value);
        
        return is_numeric($cleaned) ? (float) $cleaned : 0.0;
    }

    /**
     * ✅ ເພີ່ມ: ກວດສອບວ່າຄ່າເງິນຖືກຕ້ອງຫຼືບໍ່
     */
    public static function isValidAmount($amount): bool
    {
        if (is_null($amount) || $amount === '') {
            return true; // Allow empty values
        }

        $cleaned = str_replace([',', ' ', '₭', 'ກີບ'], '', (string) $amount);
        
        return is_numeric($cleaned) && (float) $cleaned >= 0;
    }
}