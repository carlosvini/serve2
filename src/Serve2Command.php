<?php

namespace CarlosVini\Serve2;

use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Env;

class Serve2Command extends \Illuminate\Foundation\Console\ServeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'serve2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application on serve2 development server';

    /**
     * Get the full server command.
     *
     * @return string
     */
    protected function serverCommand()
    {
        return sprintf('%s %s --port %s %s',
            ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false)),
            dirname(__DIR__).'/index.php',
            // $this->host(),
            $this->port(),
            ProcessUtils::escapeArgument(base_path('server.php'))
        );
    }
}
