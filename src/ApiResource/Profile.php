<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\DTO\UserProfile;
use App\State\Provider\CurrentUserProfileProvider;

#[ApiResource(
    operations: [new Get(
        uriTemplate: '/users/me',
        provider: CurrentUserProfileProvider::class,
        output: USerProfile::class,
    )],
)]
class Profile
{

}
