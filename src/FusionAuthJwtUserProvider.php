<?php

namespace DaniloPolani\FusionAuthJwt;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class FusionAuthJwtUserProvider implements UserProvider
{
    /**
     * {@inheritDoc}
     */
    public function retrieveByCredentials(array $credentials)
    {
        $jwt = $credentials['jwt'] ?? null;

        if (!$jwt) {
            return null;
        }

        try {
            $leeway = env('JWT_LEEWAY',(60 * 60 * 24)); // 24 hrs to test
            $decodedJwt = FusionAuthJwt::decode($jwt, $leeway);
        } catch (Exception $e) {
            return null;
        }

        return new FusionAuthJwtUser($decodedJwt);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveById($identifier)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return false;
    }
}
