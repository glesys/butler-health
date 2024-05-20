<?php

namespace Butler\Health\Tests;

use Butler\Health\Facades\Heartbeat;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\ExpectationFailedException;

class HeartbeatTest extends AbstractTestCase
{
    public function test_send_happy_path()
    {
        Http::fake();

        Heartbeat::send('foobar', 2);

        Http::assertSent(fn (Request $request)
            => $request->method() === 'POST'
            && $request->isJson()
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Authorization', 'Bearer secret')
            && $request->url() === 'http://localhost/foobar/2');
    }

    public function test_send_sad_path_do_not_report_exception()
    {
        Http::fakeSequence()->pushStatus(500);

        Heartbeat::send('foobar', 2);

        Http::assertSentCount(1);
    }

    public function test_send_sad_path_reports_exception_when_configured()
    {
        config(['butler.health.heartbeat.report' => true]);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('HTTP request returned status code 500');

        app(ExceptionHandler::class)->reportable(function (RequestException $exception) {
            throw $exception;
        });

        Http::fakeSequence()->pushStatus(500);

        Heartbeat::send('foobar', 2);
    }

    public function test_assertSent_happy_path()
    {
        Heartbeat::fake()->send('foobar', 2);
        Heartbeat::assertSent('foobar', 2);
    }

    public function test_assertSent_sad_path()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The expected heartbeat [foobar] was not sent.');

        Heartbeat::fake();
        Heartbeat::assertSent('foobar', 2);
    }

    public function test_assertNotSent_happy_path()
    {
        Heartbeat::fake()->send('foobar', 2);
        Heartbeat::assertNotSent('foobaz', 2);
    }

    public function test_assertNotSent_sad_path()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('A unexpected heartbeat [foobar] was sent.');

        Heartbeat::fake()->send('foobar', 2);
        Heartbeat::assertNotSent('foobar', 2);
    }

    public function test_assertNothingSent_happy_path()
    {
        Heartbeat::fake();
        Heartbeat::assertNothingSent();
    }

    public function test_assertNothingSent_sad_path()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Heartbeats were sent unexpectedly.');

        Heartbeat::fake()->send('foobar');
        Heartbeat::assertNothingSent();
    }

    public function test_assertSentCount_happy_path()
    {
        Heartbeat::fake()->send('foobar');
        Heartbeat::assertSentCount(1);
    }

    public function test_assertSentCount_sad_path()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that actual size 1 matches expected size 2.');

        Heartbeat::fake()->send('foobar');
        Heartbeat::assertSentCount(2);
    }

    public function test_recorded()
    {
        Heartbeat::fake()->send('foobar', 2);

        $recorded = Heartbeat::recorded('foobar', 2);

        $this->assertInstanceOf(Collection::class, $recorded);
        $this->assertEquals(1, $recorded->count());
    }
}
