<?php
namespace App\Sourcing\Bol\Authentication;

use App\Entity\Channel\Channel;

interface AccessTokenProviderInterface
{

    public function getAccessTokenForChannel(Channel $channel): string;

    public function getAccessTokenByCredentials(
        string $clientId,
        string $clientSecret,
    ): string;
}
