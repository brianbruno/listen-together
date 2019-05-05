<?php

namespace App\Console\Commands;

use App\Fila;
use App\ItensFila;
use App\Job;
use Illuminate\Console\Command;

class PararTudo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:parartudo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Para o sistema e limpa as filas.';

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
    public function handle() {

        $jobs = Job::all();

        foreach ($jobs as $job) {
            $job->delete();
        }

        $itens = ItensFila::where('status', 'I')->get();
        foreach ($itens as $item) {
            $item->status = 'F';
            $item->save();
        }

        $filas = Fila::where('status', 'A')->get();
        foreach ($filas as $fila) {
            $fila->status = 'I';
            $fila->save();
        }
    }
}
