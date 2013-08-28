<?php

require __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$resource = fopen(__DIR__.'/somedata.csv', 'r');
stream_set_blocking($resource, 0);

$stream = new React\Csv\ReadableCsvStream($resource, $loop);

$stream->on('data', function($data, $stream) {
  echo 'column 2: ' . $data[2] . "\n";
});

$stream->on('end', function($stream) use ($loop) {
  echo "done.\n";
  $loop->stop();
});

$loop->run();