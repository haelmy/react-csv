<?php

namespace React\Tests\Csv;

use React\Csv\ReadableCsvStream;

class ReadableCsvStreamTest extends \PHPUnit_Framework_TestCase
{

  private $resource;

  public function setUp()
  {
    $this->resource = fopen('php://temp', 'r+');
  }

  /**
   * @test
   */
  public function itShouldAllowToReadACsvStream()
  {
    $capturedData = null;

    $stream = new ReadableCsvStream($this->resource, $this->createLoopMock());
    $stream->on('data', function ($data) use (&$capturedData) {
        $capturedData = $data;
    });

    $this->writeToResource('foo,"bar",1,"""escaped"' . "\n");

    $stream->handleData($this->resource);
    $this->assertSame(array('foo', 'bar', '1', '"escaped'), $capturedData);
  }

  /**
   * @test
   */
  public function itShouldUseOptionalArgumentsIfProvided()
  {
    $capturedData = null;

    $stream = new ReadableCsvStream($this->resource, $this->createLoopMock(), 50, ';', '|');
    $stream->on('data', function ($data) use (&$capturedData) {
        $capturedData = $data;
    });

    $this->writeToResource('foo;|bar|;1;|||escaped|' . "\n");

    $stream->handleData($this->resource);
    $this->assertSame(array('foo', 'bar', '1', '|escaped'), $capturedData);
  }

  /**
   * @test
   */
  public function itShouldNotifyOfTheStreamEnd()
  {
    $endOfStreamWasReached = false;

    $stream = new ReadableCsvStream($this->resource, $this->createLoopMock());
    $stream->on('end', function() use (&$endOfStreamWasReached) {
      $endOfStreamWasReached = true;
    });

    fwrite($this->resource, "");
    rewind($this->resource);

    $stream->handleData($this->resource);
    $this->assertTrue($endOfStreamWasReached);
  }

  private function writeToResource($data)
  {
    fwrite($this->resource, $data);
    rewind($this->resource);
  }

  private function createLoopMock()
  {
      return $this->getMock('React\EventLoop\LoopInterface');
  }
}