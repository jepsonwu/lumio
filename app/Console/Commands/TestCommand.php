<?php
/**
 * Created by PhpStorm.
 * User: feraner
 * Date: 2017/11/29
 * Time: 下午4:25
 */

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Auth;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test cli code';


    protected $repository;

    /**
     * Create a new command instance.
     *
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

    }

}