<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Trustpilot\Facades\Trustpilot;

it('creates an email invitation', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/business-units/fake-business-unit/email-invitations' => Http::response(['id' => 'inv-1'], 200),
    ]);

    expect(Trustpilot::invitations()->create('ada@example.com', 'Ada Lovelace', reference: 'order-42'))
        ->toBe(['id' => 'inv-1']);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/email-invitations')
        && $request['consumerEmail'] === 'ada@example.com'
        && $request['consumerName'] === 'Ada Lovelace'
        && $request['referenceNumber'] === 'order-42'
        && $request['redirectUri'] === 'https://trustpilot.com');
});

it('merges extra invitation attributes into the payload', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/business-units/fake-business-unit/email-invitations' => Http::response([], 200),
    ]);

    Trustpilot::invitations()->create('ada@example.com', 'Ada', attributes: [
        'senderEmail' => 'noreply@example.com',
        'templateId' => 'tpl-1',
    ]);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/email-invitations')
        && $request['senderEmail'] === 'noreply@example.com'
        && $request['templateId'] === 'tpl-1');
});

it('generates an invitation link', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/business-units/fake-business-unit/invitation-links' => Http::response(['url' => 'https://x.test'], 200),
    ]);

    expect(Trustpilot::invitations()->link('ada@example.com', 'Ada', reference: 'order-42'))
        ->toBe(['url' => 'https://x.test']);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/invitation-links')
        && $request['email'] === 'ada@example.com'
        && $request['name'] === 'Ada'
        && $request['referenceId'] === 'order-42');
});

it('lists invitation templates', function () {
    fakeTrustpilot([
        'api.trustpilot.com/v1/private/business-units/fake-business-unit/templates' => Http::response(['templates' => []], 200),
    ]);

    expect(Trustpilot::invitations()->templates())->toBe(['templates' => []]);

    Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer fake-token'));
});
