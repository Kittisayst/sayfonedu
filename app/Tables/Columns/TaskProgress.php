<?php

namespace App\Tables\Columns;

use Closure;
use Filament\Tables\Columns\Column;

class TaskProgress extends Column
{
    protected string $view = 'filament.tables.columns.task-progress';
    // ເພີ່ມຕົວແປຕ່າງໆສຳລັບການກຳນົດຄ່າສູງສຸດ
    protected int | Closure | null $maxValue = 100;
    
    // ສ້າງເມທອດເພື່ອກຳນົດຄ່າສູງສຸດ
    public function maxValue(int | Closure | null $maxValue): static
    {
        $this->maxValue = $maxValue;
        
        return $this;
    }
    
    // ສ້າງເມທອດເພື່ອດຶງຄ່າສູງສຸດ
    public function getMaxValue(): ?int
    {
        return $this->evaluate($this->maxValue);
    }
    
    // ທຸກໆຄັ້ງທີ່ column ນີ້ຖືກໃຊ້, ຈະມີການ setup ຄ່າເລີ່ມຕົ້ນ
    protected function setUp(): void
    {
        parent::setUp();
        
        // ຕັ້ງໃຫ້ column ຈັດກາງໂດຍພື້ນຖານ
        $this->alignCenter();
    }
}