<?php

declare(ticks=1);

use Clue\React\Buzz\Browser;
use RingCentral\Psr7\Uri;
use RingCentral\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

// use function GuzzleHttp\Psr7\stream_for;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Serve2Server.php';

// LimitConcurrentRequestsMiddleware:
//ini_set('memory_limit', -1);

$workerPids = [];
$workers = [];

// signal handler function
function sig_handler($signo)
{
    global $workerPids;
    switch ($signo) {
        case SIGINT:
            // handle shutdown tasks
            echo 'Closing workers...' . PHP_EOL;
            foreach ($workerPids as $pid) {
                posix_kill($pid, SIGINT);
            }
            exit;
            break;
        default:
            // handle all other signals
    }
}
// setup signal handlers
pcntl_signal(SIGINT, 'sig_handler');

$docRoot = is_dir('public') ? 'public' : '';

function startWorker(int $port, string $docRoot)
{
    $cmd = 'php -S localhost:' . $port . ($docRoot ? ' -t ' . $docRoot : '') . ' > /dev/null 2>&1 & echo $!';
    return exec($cmd);
}

function getNextWorkerPort()
{
    global $workers;
    $next = array_search(min($workers), $workers);
    $workers[$next]++;
    return $next;
}

$port = 8000;
if ($portPos = array_search('--port', $argv)) {
    $port = $argv[$portPos + 1];
    if (!is_numeric($port)) {
        throw new InvalidArgumentException('Port must be numeric');
    }
}

for ($i = 0; $i < 5; $i++) {
    $workerPort = $port + 10000 + $i;
    $workerPids[] = startWorker($workerPort, $docRoot);
    $workers[$workerPort] = 0;
}

$loop = React\EventLoop\Factory::create();

$browser = (new Browser($loop))->withOptions([
    //'timeout' => null,
    'followRedirects' => true,
    'maxRedirects' => 30,
    'obeySuccessCode' => false,
]);

$server = new Serve2Server(function (ServerRequestInterface $request) use ($browser, $port) {
    try {
        $workerPort = getNextWorkerPort();
        $msg = $workerPort . ' - ' . $request->getMethod() . ' ' . $request->getUri();
        echo $msg . PHP_EOL;
        $target = new Uri('http://localhost:' . $workerPort);

        // Overwrite target scheme, host and port.
        $uri = $request->getUri()
            ->withScheme($target->getScheme())
            ->withHost($target->getHost())
            ->withPort($target->getPort());

        // Check for subdirectory.
        if ($path = $target->getPath()) {
            $uri = $uri->withPath(rtrim($path, '/') . '/' . ltrim($uri->getPath(), '/'));
        }
        $request = $request->withUri($uri)
            ->withHeader('Host', 'localhost:' . $port);

        /*
        $contentType = $request->getHeader('Content-Type')[0] ?? null;
        if ($contentType === 'application/x-www-form-urlencoded' || strpos($contentType, 'multipart/form-data') !== false) {
            $request = $request->withBody(stream_for(http_build_query($request->getParsedBody())));
        }*/

        // Forward the request and get the response.
        return $browser->send($request)
            ->then(
                function (Response $response) use ($workerPort) {
                    global $workers;
                    $workers[$workerPort]--;
                    echo $workerPort . ' - ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . PHP_EOL;
                    return $response;
                },
                function ($e) {
                    echo $e->getMessage() . $e->getTraceAsString().PHP_EOL;
                }
            );
    } catch (Throwable $e) {
        echo $e->getMessage() . $e->getTraceAsString().PHP_EOL;
    }
});

$server->on('error', function (Exception $e) {
    echo $e->getMessage() . $e->getTraceAsString() . PHP_EOL;
    if ($e->getPrevious() !== null) {
        echo 'Previous: ' . $e->getPrevious()->getTraceAsString() . PHP_EOL;
    }
});

$socket = new React\Socket\Server($port, $loop);
$server->listen($socket);

echo "Server running at http://localhost:$port \n";

$loop->run();
