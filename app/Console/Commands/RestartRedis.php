<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RestartRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:restart {redisPid}';

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
        $redisPid = $this->argument('redisPid');
        $redis_status = 0;
        $process = new Process(['killall', '-9', $redisPid]);
        $process->run();

        if (!$process->isSuccessful()) {
            $redis_status = -1;
        }
        return $redis_status;
    }
}
