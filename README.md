<div class="filament-hidden">

![Laravel Trustpilot](https://raw.githubusercontent.com/jeffersongoncalves/laravel-trustpilot/main/art/jeffersongoncalves-laravel-trustpilot.png)

</div>

# Laravel Trustpilot

[![Tests](https://github.com/jeffersongoncalves/laravel-trustpilot/actions/workflows/tests.yml/badge.svg)](https://github.com/jeffersongoncalves/laravel-trustpilot/actions/workflows/tests.yml)
[![PHPStan](https://github.com/jeffersongoncalves/laravel-trustpilot/actions/workflows/phpstan.yml/badge.svg)](https://github.com/jeffersongoncalves/laravel-trustpilot/actions/workflows/phpstan.yml)
[![Code Style](https://github.com/jeffersongoncalves/laravel-trustpilot/actions/workflows/pint.yml/badge.svg)](https://github.com/jeffersongoncalves/laravel-trustpilot/actions/workflows/pint.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-trustpilot.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-trustpilot)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-trustpilot.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-trustpilot)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-trustpilot.svg?style=flat-square)](LICENSE.md)

A Laravel client for the [Trustpilot](https://trustpilot.com) API. A fluent `Trustpilot` facade groups the business unit, review, reply, invitation, and tag endpoints behind resource accessors, picks the right credentials per call — the `apikey` header for public data, an OAuth bearer token for everything private — and throws a `TrustpilotException` on a non-2xx response instead of returning a silent error array.

## Features

- **Business Units** — `businessUnits()->search()`, `get()`, `profile()`, `categories()`, `webLinks()`
- **Reviews** — `reviews()->list()`, `get()`, `latest()`, `private()`, `reply()`, `deleteReply()`
- **Invitations** — `invitations()->create()`, `link()`, `templates()`
- **Review Tags** — `tags()->get()`, `add()`, `remove()`
- **Both auth modes handled for you** — public calls use the API key, private calls mint and cache a client-credentials bearer token until just before it expires
- **Default business unit** — set it once in the config and omit the id everywhere
- **Thin by design** — every method returns the raw decoded JSON response as an array, no DTOs
- **Fails loud** — a non-2xx response throws `TrustpilotException` carrying the API's error message and HTTP status code

## Installation

```bash
composer require jeffersongoncalves/laravel-trustpilot
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="trustpilot-config"
```

## Configuration

Add to your `.env`:

```env
TRUSTPILOT_API_KEY=your-api-key
TRUSTPILOT_API_SECRET=your-api-secret
TRUSTPILOT_BUSINESS_UNIT_ID=your-business-unit-id
```

Create the application at [the Trustpilot Business app](https://businessapp.b2b.trustpilot.com/#/integrations/api-keys). The secret is only needed for the private endpoints (private reviews, replies, invitations, tags) — leave it unset if you only read public data. Find your business unit id with `Trustpilot::businessUnits()->search('example.com')`.

### Config Options

```php
// config/trustpilot.php
return [
    'api_key' => env('TRUSTPILOT_API_KEY'),
    'api_secret' => env('TRUSTPILOT_API_SECRET'),
    'business_unit_id' => env('TRUSTPILOT_BUSINESS_UNIT_ID'),
    'base_url' => env('TRUSTPILOT_BASE_URL', 'https://api.trustpilot.com/v1'),
    'cache_key' => env('TRUSTPILOT_CACHE_KEY', 'trustpilot.access_token'),
];
```

## Usage

```php
use JeffersonGoncalves\Trustpilot\Facades\Trustpilot;
use JeffersonGoncalves\Trustpilot\Exceptions\TrustpilotException;
```

Every method that takes a `$businessUnitId` falls back to `trustpilot.business_unit_id` when you omit it.

### Business Units

```php
Trustpilot::businessUnits()->search('example.com', limit: 20);
Trustpilot::businessUnits()->get();
Trustpilot::businessUnits()->profile();
Trustpilot::businessUnits()->categories();
Trustpilot::businessUnits()->webLinks('pt-BR');
```

### Reviews

```php
Trustpilot::reviews()->list(perPage: 20, orderBy: 'createdat.desc', stars: 5, language: 'pt');
Trustpilot::reviews()->get('review-id');
Trustpilot::reviews()->latest(20);

// private — needs TRUSTPILOT_API_SECRET
Trustpilot::reviews()->private(perPage: 20, stars: 1);
Trustpilot::reviews()->reply('review-id', 'Thanks for the feedback!');
Trustpilot::reviews()->deleteReply('review-id');
```

### Invitations

```php
Trustpilot::invitations()->create('ada@example.com', 'Ada Lovelace', reference: 'order-42');

// extra payload keys go through `attributes`
Trustpilot::invitations()->create('ada@example.com', 'Ada Lovelace', attributes: [
    'senderEmail' => 'noreply@example.com',
    'replyTo' => 'support@example.com',
    'templateId' => 'your-template-id',
]);

Trustpilot::invitations()->link('ada@example.com', 'Ada Lovelace', reference: 'order-42');
Trustpilot::invitations()->templates();
```

### Review Tags

```php
Trustpilot::tags()->get('review-id');
Trustpilot::tags()->add('review-id', 'topic', 'shipping');
Trustpilot::tags()->remove('review-id', 'topic', 'shipping');
```

### Handling errors

```php
try {
    $reviews = Trustpilot::reviews()->list();
} catch (TrustpilotException $e) {
    // $e->getMessage() — the API's message/error_description, or the raw response body
    // $e->statusCode  — the HTTP status code returned by Trustpilot (0 for config errors)
}
```

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## Code Formatting

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
