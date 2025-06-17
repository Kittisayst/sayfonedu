<?php
namespace App\Utils;
use App\Models\Discount;

class CalculatePay
{
    public static function Amount($cash, $transfer, $latefee): int
    {
        $total = $cash + $transfer + $latefee;
        return $total;
    }

    public static function DiscountAmount($discountId, $money): int
    {
        $discountAmount = 0;
        if ($discountId) {
            $discount = Discount::find($discountId);
            if ($discount) {
                $subtotal = $money;
                if ($discount->discount_type === 'percentage') {
                    $discountAmount = $subtotal * ($discount->discount_value / 100);
                } else {
                    $discountAmount = $discount->discount_value;
                }
            }
        }

        return $discountAmount;
    }
}
