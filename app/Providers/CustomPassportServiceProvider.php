<?php

namespace App\Providers;

use DateInterval;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\Bridge\PersonalAccessGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use App\Providers\OAuth2\Server\Grant\CustomGrant;

class CustomPassportServiceProvider extends PassportServiceProvider
{
    /**
     * Register the authorization server.
     *
     * @return void
     */
    protected function registerAuthorizationServer()
    {
        $this->app->singleton(AuthorizationServer::class, function () {
            return tap($this->makeAuthorizationServer(), function ($server) {
                $server->enableGrantType(
                    $this->makeAuthCodeGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    $this->makeRefreshTokenGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    $this->makePasswordGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    new PersonalAccessGrant, new DateInterval('P1Y')
                );

                $server->enableGrantType(
                   $this->makeCustomGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    new ClientCredentialsGrant, Passport::tokensExpireIn()
                );

                if (Passport::$implicitGrantEnabled) {
                    $server->enableGrantType(
                        $this->makeImplicitGrant(), Passport::tokensExpireIn()
                    );
                }
            });
        });
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return \App\Providers\OAuth2\Server\Grant\CustomGrant
     */
    protected function makeCustomGrant()
    {
        $grant = new CustomGrant(
            $this->app->make(\Laravel\Passport\Bridge\UserRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}