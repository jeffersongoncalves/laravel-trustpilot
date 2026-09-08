<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Trustpilot\Facades\Trustpilot;

it('searches business units', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/business-units/search*' => Http::response(['businessUnits' => []], 200),
    ]);

    expect(Trustpilot::businessUnits()->search('example.com', limit: 5))->toBe(['businessUnits' => []]);

    Http::assertSent(fn (Request $request) => str_starts_with($request->url(), 'https://api.trustpilot.com/v1/business-units/search')
        && str_contains($request->url(), 'query=example.com')
        && str_contains($request->url(), 'limit=5'));
});

it('falls back to the configured business unit', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/business-units/fake-business-unit' => Http::response(['id' => 'fake-business-unit'], 200),
    ]);

    Trustpilot::businessUnits()->get();

    Http::assertSent(fn (Request $request) => $request->url() === 'https://api.trustpilot.com/v1/business-units/fake-business-unit');
});

it('prefers an explicit business unit over the configured one', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/business-units/other-unit/profileinfo' => Http::response([], 200),
    ]);

    Trustpilot::businessUnits()->profile('other-unit');

    Http::assertSent(fn (Request $request) => $request->url() === 'https://api.trustpilot.com/v1/business-units/other-unit/profileinfo');
});

it('reads categories', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/business-units/fake-business-unit/categories' => Http::response(['categories' => []], 200),
    ]);

    expect(Trustpilot::businessUnits()->categories())->toBe(['categories' => []]);
});

it('reads web links for a locale', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/business-units/fake-business-unit/web-links*' => Http::response([], 200),
    ]);

    Trustpilot::businessUnits()->webLinks('pt-BR');

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'web-links?locale=pt-BR'));
});
