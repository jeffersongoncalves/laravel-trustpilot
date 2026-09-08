<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trustpilot API Key
    |--------------------------------------------------------------------------
    |
    | The application key from your Trustpilot Business account. It authenticates
    | every public endpoint on its own. Create the application at
    | https://businessapp.b2b.trustpilot.com/#/integrations/api-keys
    |
    */
    'api_key' => env('TRUSTPILOT_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Trustpilot API Secret
    |--------------------------------------------------------------------------
    |
    | Paired with the API key above to mint an OAuth access token. Only the
    | private endpoints (private reviews, replies, invitations, tags) need it;
    | leave it unset if you only read public data.
    |
    */
    'api_secret' => env('TRUSTPILOT_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Default Business Unit
    |--------------------------------------------------------------------------
    |
    | Most endpoints are scoped to a business unit. Set the default here and the
    | resource methods will fall back to it whenever you omit the id. Look it up
    | with `Trustpilot::businessUnits()->search('example.com')`.
    |
    */
    'business_unit_id' => env('TRUSTPILOT_BUSINESS_UNIT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The Trustpilot REST API base URL. Override only if Trustpilot gives you a
    | dedicated endpoint.
    |
    */
    'base_url' => env('TRUSTPILOT_BASE_URL', 'https://api.trustpilot.com/v1'),

    /*
    |--------------------------------------------------------------------------
    | Access Token Cache
    |--------------------------------------------------------------------------
    |
    | The OAuth access token used by the private endpoints is cached under this
    | key so a token is not minted on every request. It is stored for the
    | lifetime the API reports, minus a small safety margin.
    |
    */
    'cache_key' => env('TRUSTPILOT_CACHE_KEY', 'trustpilot.access_token'),
];
