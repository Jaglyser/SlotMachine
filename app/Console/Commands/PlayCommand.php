<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers;


class PlayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'play the slot machine';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        $playSlot = new Controllers\Rawcontroller;

        $playSlot->findWinner();
        
    }
}