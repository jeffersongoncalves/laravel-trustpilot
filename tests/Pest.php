<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Trustpilot\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        Cache::flush();
        Http::preventStrayRequests();
    })
    ->in('Feature');

/**
 * Private calls mint a bearer token first, so each fake needs the OAuth endpoint
 * stubbed alongside the endpoint under test. Public calls ignore it.
 */
function fakeTrustpilot(array $fakes, int $expiresIn = 3600): void
{
    Http::fake([
        'api.trustpilot.com/v1/oauth/*' => Http::response([
            'access_token' => 'fake-token',
            'expires_in' => $expiresIn,
        ], 200),
        ...$fakes,
    ]);
}
