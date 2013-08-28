<?php

namespace React\Tests\Csv;

use React\Csv\ReadableCsvStream;

class ReadableCsvStreamTest extends \PHPUnit_Framework_TestCase
{

  private $resource;

  private $stream;

  public function setUp()
  {
    $this->resource = fopen('php://temp', 'r+');

    $loop = $this->createLoopMock();
    $this->stream = new ReadableCsvStream($this->resource, $loop);
  }

  /**
   * @test
   */
  public function itShouldAllowToReadACsvStream()
  {
    $capturedData = null;

    $this->stream->on('data', function ($data) use (&$capturedData) {
        $capturedData = $data;
    });

    $this->writeToResource("foo,bar\n");

    $this->stream->handleData($this->resource);
    $this->assertSame(array('foo', 'bar'), $capturedData);
  }

  /**
   * @test
   */
  public function itShouldNotifyOfTheStreamEnd()
  {
    $endOfStreamWasReached = false;

    $this->stream->on('end', function() use (&$endOfStreamWasReached) {
      $endOfStreamWasReached = true;
    });

    fwrite($this->resource, "");
    rewind($this->resource);

    $this->stream->handleData($this->resource);
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