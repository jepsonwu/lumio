<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeJsonSchemeCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @desc 配合DB:listen 监听数据库查询
     * @var string
     */
    protected $signature = 'json:scheme {res}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test cli mode cache';


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
        $res = $this->argument('res');
        $content = file_get_contents($res);
        echo $schema = \JSONSchemaGenerator\Generator::fromJson($content,[]);
    }
}

