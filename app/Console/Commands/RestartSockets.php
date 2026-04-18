<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RestartSockets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sockets:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $restart_status = 0;
        $processRedis = new Process(['/root/redis/redis-5.0.5/src/redis-server']);
        $processRedis->run();

        if (!$processRedis->isSuccessful()) {
            $restart_status = -1;
            return $restart_status;
        }

        $process = new Process(['supervisorctl', 'restart', 'all']);
        $process->run();

        if (!$process->isSuccessful()) {
            $restart_status = -2;
            return $restart_status;
        }
        return $restart_status;
    }
}
