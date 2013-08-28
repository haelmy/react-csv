<?php

namespace React\Csv;

use Evenement\EventEmitter;

class ReadableCsvStream extends EventEmitter
{
  private $stream;

  public function __construct($stream, $loop)
  {
    $this->stream = $stream;
    $this->loop = $loop;

    $this->loop->addReadStream($this->stream, array($this, 'handleData'));
  }

  public function handleData($stream)
  {
    $data = fgetcsv($stream);

    if(!$data) {
      $this->end();
      return;
    }

    $this->emit('data', array($data, $this));
  }

  private function end()
  {
    $this->emit('end', array($this));
  }
}