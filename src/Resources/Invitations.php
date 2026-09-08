<?php

namespace JeffersonGoncalves\Trustpilot\Resources;

use JeffersonGoncalves\Trustpilot\TrustpilotClient;

/**
 * Private Invitations endpoints — asking a customer for a review by email, or
 * generating a link you deliver yourself.
 */
class Invitations
{
    public function __construct(private readonly TrustpilotClient $client) {}

    /**
     * @param  array<string, mixed>  $attributes  extra payload keys such as senderEmail, replyTo or templateId
     * @return array<string, mixed>
     */
    public function create(
        string $email,
        string $name,
        string $reference = '',
        string $redirectUri = 'https://trustpilot.com',
        array $attributes = [],
        ?string $businessUnitId = null,
    ): array {
        return $this->client->post(
            '/private/business-units/'.$this->client->businessUnitId($businessUnitId).'/email-invitations',
            [
                'consumerEmail' => $email,
                'consumerName' => $name,
                'referenceNumber' => $reference,
                'redirectUri' => $redirectUri,
                ...$attributes,
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function link(
        string $email,
        string $name,
        string $reference = '',
        string $redirectUri = 'https://trustpilot.com',
        ?string $businessUnitId = null,
    ): array {
        return $this->client->post(
            '/private/business-units/'.$this->client->businessUnitId($businessUnitId).'/invitation-links',
            [
                'email' => $email,
                'name' => $name,
                'referenceId' => $reference,
                'redirectUri' => $redirectUri,
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function templates(?string $businessUnitId = null): array
    {
        return $this->client->get(
            '/private/business-units/'.$this->client->businessUnitId($businessUnitId).'/templates',
            [],
            'bearer',
        );
    }
}
