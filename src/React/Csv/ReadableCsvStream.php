<?php

namespace React\Csv;

use Evenement\EventEmitter;

class ReadableCsvStream extends EventEmitter
{
  private $stream;

  public function __construct($stream, $loop, $length = 0, $delimiter = ',', $enclosure = '"', $escape = '\\')
  {
    $this->stream = $stream;
    $this->loop = $loop;

    $this->length = $length;
    $this->delimiter = $delimiter;
    $this->enclosure = $enclosure;
    $this->escape = $escape;

    $this->loop->addReadStream($this->stream, array($this, 'handleData'));
  }

  public function handleData($stream)
  {
    $data = fgetcsv($stream, $this->length, $this->delimiter, $this->enclosure, $this->escape);

    if(!$data) {
      $this->end();
      return;
    }

    $this->emit('data', array($data, $this));
  }

  private function end()
  {
    $this->emit('end', array($this));

    $this->loop->removeStream($this->stream);
    $this->removeAllListeners();

    if (is_resource($this->stream)) {
      fclose($this->stream);
    }
  }
}