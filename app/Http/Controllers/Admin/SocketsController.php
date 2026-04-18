<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class SocketsController extends Controller
{
    public function index()
    {
        $redis_status = 0;
        $echo_server_status = 0;

        $process = new Process(['pgrep', 'redis']);
        $process->run();

        if (!$process->isSuccessful()) {
            $redis_status = -1;
        } else {
            $redis_status = $process->getOutput();
        }

        ///
        $process = new Process(['pgrep', 'laravel-echo']);
        $process->run();

        if (!$process->isSuccessful()) {
            $echo_server_status = -1;
        } else {
            $echo_server_status = $process->getOutput();
        }


        return view('admin.sockets.index', [
            'redis_status' => (int)$redis_status,
            'echo_server_status' => (int)$echo_server_status,
        ]);
    }

    public function restart(Request $request)
    {
        $redisPid = $request->input('redis_pid', 0);

        $redis_status = 0;

        if($redisPid > 0)
        {
            $redis_status = \Artisan::call('redis:restart', [
                'redisPid' => $redisPid
            ]);
        }

        $restart_status = \Artisan::call('sockets:restart');


        if($redis_status == -1) {
            return redirect()->route('admin.sockets.index')->with('danger', 'Не удалось перзагрузить сервисы.');
        }

        if($restart_status == -1) {
            return redirect()->route('admin.sockets.index')->with('danger', 'Не удалось перезапустить Redis.');
        }
        if($restart_status == -2) {
            return redirect()->route('admin.sockets.index')->with('danger', 'Не удалось перезапустить Laravel Echo.');
        }

        return redirect()->route('admin.sockets.index')->with('success', 'Сокеты перезапущены успешно.');

    }
}
