<?php

namespace Butler\Health;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Traits\Macroable;
use PHPUnit\Framework\Assert as PHPUnit;

class Heartbeat
{
    use Macroable {
        __call as macroCall;
    }

    protected bool $recording = false;

    protected array $recorded = [];

    public function fake()
    {
        $this->recording = true;

        return $this;
    }

    public function send(string $slug, int $minutes = 1): void
    {
        $url = $this->url($slug, $minutes);

        if ($this->recording) {
            $this->recorded[] = $url;
        } else {
            $config = config('butler.health.heartbeat');

            Http::withToken($config['token'] ?? null)
                ->timeout(5)
                ->acceptJson()
                ->post($url)
                ->onError(fn ($response) => report_if(
                    $config['report'] ?? false,
                    $response->toException(),
                ));
        }
    }

    public function assertSent(string $slug, int $minutes = 1): void
    {
        PHPUnit::assertTrue(
            $this->recorded($slug, $minutes)->isNotEmpty(),
            "The expected heartbeat [$slug] was not sent."
        );
    }

    public function assertNotSent(string $slug, $minutes = 1): void
    {
        PHPUnit::assertCount(
            0,
            $this->recorded($slug, $minutes),
            "A unexpected heartbeat [$slug] was sent."
        );
    }

    public function assertNothingSent(): void
    {
        PHPUnit::assertEmpty($this->recorded, 'Heartbeats were sent unexpectedly.');
    }

    public function assertSentCount(int $count): void
    {
        PHPUnit::assertCount($count, $this->recorded);
    }

    public function recorded(string $slug, int $minutes = 1): Collection
    {
        return collect($this->recorded)
            ->filter(fn ($url) => $url === $this->url($slug, $minutes));
    }

    private function url(string $slug, int $minutes): string
    {
        return str(config('butler.health.heartbeat.url'))
            ->finish('/')
            ->append(str($slug)->slug())
            ->append("/$minutes");
    }
}
