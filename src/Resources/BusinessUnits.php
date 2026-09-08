<?php

namespace JeffersonGoncalves\Trustpilot\Resources;

use JeffersonGoncalves\Trustpilot\TrustpilotClient;

/**
 * Public Business Unit endpoints — finding a business unit and reading its
 * profile, categories and web links.
 */
class BusinessUnits
{
    public function __construct(private readonly TrustpilotClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function search(string $query, int $limit = 20): array
    {
        return $this->client->get('/business-units/search', [
            'query' => $query,
            'limit' => $limit,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(?string $businessUnitId = null): array
    {
        return $this->client->get('/business-units/'.$this->client->businessUnitId($businessUnitId));
    }

    /**
     * @return array<string, mixed>
     */
    public function profile(?string $businessUnitId = null): array
    {
        return $this->client->get('/business-units/'.$this->client->businessUnitId($businessUnitId).'/profileinfo');
    }

    /**
     * @return array<string, mixed>
     */
    public function categories(?string $businessUnitId = null): array
    {
        return $this->client->get('/business-units/'.$this->client->businessUnitId($businessUnitId).'/categories');
    }

    /**
     * @return array<string, mixed>
     */
    public function webLinks(string $locale = 'en-US', ?string $businessUnitId = null): array
    {
        return $this->client->get(
            '/business-units/'.$this->client->businessUnitId($businessUnitId).'/web-links',
            ['locale' => $locale],
        );
    }
}
