<?php

namespace App\Console\Commands;

use App\Models\SystemLog;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanOldSystemLogs extends Command
{
    protected $signature = 'logs:clean {--days=30 : ຈຳນວນວັນທີ່ຈະເກັບຮັກສາ logs} {--only-info : ລຶບສະເພາະລະດັບ info}';
    protected $description = 'ອະນາໄມ system logs ເກົ່າອອກຈາກລະບົບ';

    public function handle()
    {
        $days = $this->option('days');
        $onlyInfo = $this->option('only-info');
        
        $cutoffDate = Carbon::now()->subDays($days);
        
        $query = SystemLog::where('created_at', '<', $cutoffDate);
        
        if ($onlyInfo) {
            $query->where('log_level', 'info');
        }
        
        $count = $query->count();
        
        if ($count === 0) {
            $this->info('ບໍ່ພົບ logs ເກົ່າທີ່ຈະລຶບ.');
            return;
        }
        
        if ($this->confirm("ພົບ {$count} logs ທີ່ເກົ່າກວ່າ {$days} ວັນ. ທ່ານຕ້ອງການລຶບຫຼືບໍ່?")) {
            $query->delete();
            $this->info("ລຶບທັງໝົດ {$count} logs ສຳເລັດແລ້ວ!");
        } else {
            $this->info('ຍົກເລີກການລຶບ logs.');
        }
    }
}