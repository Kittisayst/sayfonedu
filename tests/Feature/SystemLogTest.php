<?php

namespace Tests\Feature;

use App\Facades\SysLog;
use App\Models\SystemLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_log_entry()
    {
        SysLog::info('This is a test log', 'TestSource', ['test' => true]);
        
        $this->assertDatabaseHas('system_logs', [
            'log_level' => 'info',
            'log_source' => 'TestSource',
            'message' => 'This is a test log',
        ]);
    }
    
    public function test_can_retrieve_logs_by_level()
    {
        SysLog::info('Info log', 'TestSource');
        SysLog::warning('Warning log', 'TestSource');
        SysLog::error('Error log', 'TestSource');
        
        $this->assertEquals(1, SystemLog::where('log_level', 'info')->count());
        $this->assertEquals(1, SystemLog::where('log_level', 'warning')->count());
        $this->assertEquals(1, SystemLog::where('log_level', 'error')->count());
    }
}