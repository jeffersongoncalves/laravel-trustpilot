<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Trustpilot\Facades\Trustpilot;

it('lists public reviews with the default ordering', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/business-units/fake-business-unit/reviews*' => Http::response(['reviews' => []], 200),
    ]);

    expect(Trustpilot::reviews()->list())->toBe(['reviews' => []]);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'perPage=20')
        && str_contains($request->url(), 'orderBy=createdat.desc'));
});

it('omits the optional review filters when they are not given', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/business-units/fake-business-unit/reviews*' => Http::response([], 200),
    ]);

    Trustpilot::reviews()->list();

    Http::assertSent(fn (Request $request) => ! str_contains($request->url(), 'stars')
        && ! str_contains($request->url(), 'language'));
});

it('passes the star and language filters through', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/business-units/fake-business-unit/reviews*' => Http::response([], 200),
    ]);

    Trustpilot::reviews()->list(stars: 5, language: 'pt');

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'stars=5')
        && str_contains($request->url(), 'language=pt'));
});

it('gets a single review', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/reviews/rev-1' => Http::response(['id' => 'rev-1'], 200),
    ]);

    expect(Trustpilot::reviews()->get('rev-1'))->toBe(['id' => 'rev-1']);
});

it('reads the latest reviews across Trustpilot', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/reviews/latest*' => Http::response([], 200),
    ]);

    Trustpilot::reviews()->latest(5);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'reviews/latest?count=5'));
});

it('reads private reviews with the bearer token', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/business-units/fake-business-unit/reviews*' => Http::response([], 200),
    ]);

    Trustpilot::reviews()->private(stars: 1);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), '/private/business-units/fake-business-unit/reviews')
        && $request->hasHeader('Authorization', 'Bearer fake-token')
        && str_contains($request->url(), 'stars=1'));
});

it('replies to a review', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/reviews/rev-1/reply' => Http::response([], 200),
    ]);

    Trustpilot::reviews()->reply('rev-1', 'Thanks for the feedback!');

    Http::assertSent(fn (Request $request) => $request->url() === 'https://api.trustpilot.com/v1/private/reviews/rev-1/reply'
        && $request->method() === 'POST'
        && $request['message'] === 'Thanks for the feedback!');
});

it('deletes a reply', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/reviews/rev-1/reply' => Http::response([], 200),
    ]);

    Trustpilot::reviews()->deleteReply('rev-1');

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});
