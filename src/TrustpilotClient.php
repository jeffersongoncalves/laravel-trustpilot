<?php

namespace JeffersonGoncalves\Trustpilot;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Trustpilot\Exceptions\TrustpilotException;
use JeffersonGoncalves\Trustpilot\Resources\BusinessUnits;
use JeffersonGoncalves\Trustpilot\Resources\Invitations;
use JeffersonGoncalves\Trustpilot\Resources\Reviews;
use JeffersonGoncalves\Trustpilot\Resources\Tags;

/**
 * Thin fluent client for the Trustpilot REST API (https://api.trustpilot.com/v1).
 * Groups endpoints behind resource accessors and picks the right credentials per
 * call: public endpoints are signed with the `apikey` header, private ones with
 * an OAuth client-credentials bearer token minted from `TRUSTPILOT_API_KEY` /
 * `TRUSTPILOT_API_SECRET` and cached for the lifetime the API reports.
 */
class TrustpilotClient
{
    public function businessUnits(): BusinessUnits
    {
        return new BusinessUnits($this);
    }

    public function reviews(): Reviews
    {
        return new Reviews($this);
    }

    public function invitations(): Invitations
    {
        return new Invitations($this);
    }

    public function tags(): Tags
    {
        return new Tags($this);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws TrustpilotException
     */
    public function get(string $uri, array $query = [], string $auth = 'apikey'): array
    {
        return $this->handle($this->http($auth)->get($uri, $query));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws TrustpilotException
     */
    public function post(string $uri, array $body = [], string $auth = 'bearer'): array
    {
        return $this->handle($this->http($auth)->post($uri, $body));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws TrustpilotException
     */
    public function put(string $uri, array $body = [], string $auth = 'bearer'): array
    {
        return $this->handle($this->http($auth)->put($uri, $body));
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws TrustpilotException
     */
    public function delete(string $uri, array $query = [], string $auth = 'bearer'): array
    {
        // Laravel sends a DELETE payload as a JSON body; Trustpilot expects these
        // as query string parameters, so they go on the URI instead.
        if ($query !== []) {
            $uri .= (str_contains($uri, '?') ? '&' : '?').http_build_query($query);
        }

        return $this->handle($this->http($auth)->delete($uri));
    }

    /**
     * Resolves the business unit id for a call: the explicit argument wins, then
     * the configured default.
     *
     * @throws TrustpilotException
     */
    public function businessUnitId(?string $businessUnitId = null): string
    {
        $id = $businessUnitId ?? config('trustpilot.business_unit_id');

        if (! is_string($id) || $id === '') {
            throw new TrustpilotException('No business unit id given and trustpilot.business_unit_id is not configured.');
        }

        return $id;
    }

    /**
     * Exchanges the API key and secret for an access token. The token is cached
     * until shortly before it expires, so only the first call in a given window
     * pays for the round trip.
     *
     * @throws TrustpilotException
     */
    public function accessToken(): string
    {
        $key = (string) config('trustpilot.cache_key', 'trustpilot.access_token');

        $token = Cache::get($key);

        if (is_string($token) && $token !== '') {
            return $token;
        }

        $apiKey = (string) config('trustpilot.api_key');
        $apiSecret = config('trustpilot.api_secret');

        if (! is_string($apiSecret) || $apiSecret === '') {
            throw new TrustpilotException('trustpilot.api_secret is required for the private Trustpilot endpoints.');
        }

        $response = Http::asForm()
            ->acceptJson()
            ->withBasicAuth($apiKey, $apiSecret)
            ->post($this->baseUrl().'/oauth/oauth-business-users-for-applications/accesstoken', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            throw new TrustpilotException($this->errorMessage($response), $response->status());
        }

        $data = $response->json();
        $token = is_array($data) ? ($data['access_token'] ?? null) : null;

        if (! is_string($token) || $token === '') {
            throw new TrustpilotException($this->errorMessage($response), $response->status());
        }

        // Trustpilot returns expires_in as a string of seconds; shave 60s off so
        // a token is never used in the moments around its own expiry.
        $ttl = max(60, (int) ($data['expires_in'] ?? 3600) - 60);

        Cache::put($key, $token, $ttl);

        return $token;
    }

    /**
     * @throws TrustpilotException
     */
    private function http(string $auth): PendingRequest
    {
        $request = Http::baseUrl($this->baseUrl())->acceptJson();

        return $auth === 'bearer'
            ? $request->withToken($this->accessToken())
            : $request->withHeaders(['apikey' => (string) config('trustpilot.api_key')]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws TrustpilotException
     */
    private function handle(Response $response): array
    {
        if ($response->failed()) {
            throw new TrustpilotException($this->errorMessage($response), $response->status());
        }

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    private function errorMessage(Response $response): string
    {
        $data = $response->json();

        foreach (['message', 'error_description', 'error'] as $key) {
            if (is_array($data) && is_string($data[$key] ?? null)) {
                return $data[$key];
            }
        }

        return $response->body() !== ''
            ? $response->body()
            : "Trustpilot API request failed with status {$response->status()}.";
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('trustpilot.base_url', 'https://api.trustpilot.com/v1'), '/');
    }
}
