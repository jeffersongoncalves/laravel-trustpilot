<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Trustpilot\Facades\Trustpilot;

it('reads the tags on a review', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/reviews/rev-1/tags*' => Http::response(['tags' => []], 200),
    ]);

    expect(Trustpilot::tags()->get('rev-1'))->toBe(['tags' => []]);
});

it('adds a tag as a group/value pair', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/reviews/rev-1/tags*' => Http::response([], 200),
    ]);

    Trustpilot::tags()->add('rev-1', 'topic', 'shipping');

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request['tags'] === [['group' => 'topic', 'value' => 'shipping']]);
});

it('removes a tag by group and value', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/reviews/rev-1/tags*' => Http::response([], 200),
    ]);

    Trustpilot::tags()->remove('rev-1', 'topic', 'shipping');

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE'
        && str_contains($request->url(), 'group=topic')
        && str_contains($request->url(), 'value=shipping'));
});
