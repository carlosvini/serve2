<?php

namespace CarlosVini\Serve2;

use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;

class Serve2Command extends \Illuminate\Foundation\Console\ServeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'serve2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application on serve2 development server';

}
