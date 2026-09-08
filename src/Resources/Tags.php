<?php

namespace JeffersonGoncalves\Trustpilot\Resources;

use JeffersonGoncalves\Trustpilot\TrustpilotClient;

/**
 * Private review tag endpoints — the group/value labels you use to organise
 * reviews inside the Trustpilot Business app.
 */
class Tags
{
    public function __construct(private readonly TrustpilotClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function get(string $reviewId): array
    {
        return $this->client->get('/private/reviews/'.$reviewId.'/tags', [], 'bearer');
    }

    /**
     * @return array<string, mixed>
     */
    public function add(string $reviewId, string $group, string $value): array
    {
        return $this->client->put('/private/reviews/'.$reviewId.'/tags', [
            'tags' => [
                ['group' => $group, 'value' => $value],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function remove(string $reviewId, string $group, string $value): array
    {
        return $this->client->delete('/private/reviews/'.$reviewId.'/tags', [
            'group' => $group,
            'value' => $value,
        ]);
    }
}
