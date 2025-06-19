<h1>ໃບບິນເລກທີ {{ $this->record->receipt_number }}</h1>
<h1>ວັນທີຈ່າຍ {{ $this->record->payment_date }}</h1>
<div class="flex justify-between mt-6">
    <h1>ເງິນສົດ {{ $this->data['cash'] }}</h1>
    <h1>ເງິນໂອນ {{ $this->data['transfer'] }}</h1>
</div>
<div class="flex justify-between mt-3">
    <h1>ເດືອນຄ່າຮຽນ {{ json_encode($this->data['tuition_months']) }}</h1>
    <h1>ເດືອນຄ່າອາຫານ {{ json_encode($this->data['food_months']) }}</h1>
</div>
<h1>ຄ່າອາຫານ {{ $this->data['food_money'] }}</h1>
<h1>ສ່ວນຫຼຸດ {{ $this->data['discount_amount'] }}</h1>
<h1>ປັບໃໝ {{ $this->data['late_fee'] }}</h1>
<h1>ລວມເງິນ {{ $this->data['total_amount_view'] }}</h1>
<h1>ໝາຍເຫດ {{ $this->data['note'] }}</h1>