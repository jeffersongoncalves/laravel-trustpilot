<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Trustpilot\Exceptions\TrustpilotException;
use JeffersonGoncalves\Trustpilot\Facades\Trustpilot;

it('signs public calls with the apikey header and never mints a token', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/reviews/latest*' => Http::response(['reviews' => []], 200),
    ]);

    Trustpilot::reviews()->latest();

    Http::assertSent(fn (Request $request) => $request->hasHeader('apikey', 'fake-api-key'));
    Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/oauth/'));
});

it('mints a bearer token for private calls and caches it', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/reviews/*/tags' => Http::response(['tags' => []], 200),
    ]);

    Trustpilot::tags()->get('rev-1');
    Trustpilot::tags()->get('rev-2');

    expect(Cache::get('trustpilot.access_token'))->toBe('fake-token');

    Http::assertSentCount(3);
    Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer fake-token'));
});

it('sends the client credentials as basic auth on the token endpoint', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/reviews/*/tags' => Http::response([], 200),
    ]);

    Trustpilot::tags()->get('rev-1');

    Http::assertSent(fn (Request $request) => str_contains($request->url(), '/oauth/oauth-business-users-for-applications/accesstoken')
        && $request->hasHeader('Authorization', 'Basic '.base64_encode('fake-api-key:fake-api-secret'))
        && $request['grant_type'] === 'client_credentials');
});

it('caches the token until shortly before it expires', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/reviews/*/tags' => Http::response([], 200),
    ], expiresIn: 600);

    Trustpilot::tags()->get('rev-1');

    expect(Cache::get('trustpilot.access_token'))->toBe('fake-token');
});

it('refuses private calls when no api secret is configured', function () {
    config()->set('trustpilot.api_secret', null);

    Trustpilot::tags()->get('rev-1');
})->throws(TrustpilotException::class, 'trustpilot.api_secret is required for the private Trustpilot endpoints.');

it('throws when a business unit id is neither given nor configured', function () {
    config()->set('trustpilot.business_unit_id', null);

    Trustpilot::businessUnits()->get();
})->throws(TrustpilotException::class, 'No business unit id given and trustpilot.business_unit_id is not configured.');

it('throws the api error message on a failed response', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/reviews/latest*' => Http::response(['message' => 'Invalid api key'], 401),
    ]);

    Trustpilot::reviews()->latest();
})->throws(TrustpilotException::class, 'Invalid api key');

it('falls back to the status code when the error body carries no message', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/reviews/latest*' => Http::response('', 503),
    ]);

    try {
        Trustpilot::reviews()->latest();
    } catch (TrustpilotException $e) {
        expect($e->getMessage())->toBe('Trustpilot API request failed with status 503.')
            ->and($e->statusCode)->toBe(503);
    }
});
