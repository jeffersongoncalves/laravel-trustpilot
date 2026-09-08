<?php

namespace JeffersonGoncalves\Trustpilot\Resources;

use JeffersonGoncalves\Trustpilot\TrustpilotClient;

/**
 * Review endpoints. `list`, `get` and `latest` read public data with the API
 * key; `private`, `reply` and `deleteReply` act as the business and need the
 * OAuth bearer token.
 */
class Reviews
{
    public function __construct(private readonly TrustpilotClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function list(
        ?string $businessUnitId = null,
        int $perPage = 20,
        string $orderBy = 'createdat.desc',
        ?int $stars = null,
        ?string $language = null,
    ): array {
        return $this->client->get(
            '/business-units/'.$this->client->businessUnitId($businessUnitId).'/reviews',
            array_filter([
                'perPage' => $perPage,
                'orderBy' => $orderBy,
                'stars' => $stars,
                'language' => $language,
            ], fn ($value) => $value !== null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $reviewId): array
    {
        return $this->client->get('/reviews/'.$reviewId);
    }

    /**
     * @return array<string, mixed>
     */
    public function latest(int $count = 20): array
    {
        return $this->client->get('/reviews/latest', ['count' => $count]);
    }

    /**
     * @return array<string, mixed>
     */
    public function private(?string $businessUnitId = null, int $perPage = 20, ?int $stars = null): array
    {
        return $this->client->get(
            '/private/business-units/'.$this->client->businessUnitId($businessUnitId).'/reviews',
            array_filter([
                'perPage' => $perPage,
                'stars' => $stars,
            ], fn ($value) => $value !== null),
            'bearer',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function reply(string $reviewId, string $message): array
    {
        return $this->client->post('/private/reviews/'.$reviewId.'/reply', ['message' => $message]);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteReply(string $reviewId): array
    {
        return $this->client->delete('/private/reviews/'.$reviewId.'/reply');
    }
}
