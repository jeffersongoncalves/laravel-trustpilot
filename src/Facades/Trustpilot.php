<?php

namespace JeffersonGoncalves\Trustpilot\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\Trustpilot\Resources\BusinessUnits;
use JeffersonGoncalves\Trustpilot\Resources\Invitations;
use JeffersonGoncalves\Trustpilot\Resources\Reviews;
use JeffersonGoncalves\Trustpilot\Resources\Tags;
use JeffersonGoncalves\Trustpilot\TrustpilotClient;

/**
 * @method static BusinessUnits businessUnits()
 * @method static Reviews reviews()
 * @method static Invitations invitations()
 * @method static Tags tags()
 * @method static string businessUnitId(?string $businessUnitId = null)
 * @method static string accessToken()
 *
 * @see TrustpilotClient
 */
class Trustpilot extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'trustpilot';
    }
}
