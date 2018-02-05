<?php

namespace App\Console;

use App\Console\Commands\CacheTestCommand;
use App\Console\Commands\MakeJsonSchemeCommand;
use App\Console\Commands\TestCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

use Jiuyan\LumenCommand\VendorPublishCommand;
use Jiuyan\LumenCommand\ProviderMakeCommand;
use Jiuyan\LumenCommand\RequestMakeCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */

    protected $commands = [
        VendorPublishCommand::class,
        ProviderMakeCommand::class,
        RequestMakeCommand::class,
        MakeJsonSchemeCommand::class,
        TestCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
