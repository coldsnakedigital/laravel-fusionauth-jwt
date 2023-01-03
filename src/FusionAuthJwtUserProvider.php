<?php

namespace DaniloPolani\FusionAuthJwt;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;

class FusionAuthJwtUserProvider implements UserProvider
{

    protected $config;

    protected $model;

    public function __construct(Application $app) {
        $this->config = $app['config']['auth.providers.fusionauth'];
        $this->model = $this->config['model'];
    }
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

        $decodedJwt = $this->setUrlPrefix($decodedJwt);

        return $this->createModel($decodedJwt);
    }

    private function setUrlPrefix($userData) {
        $prefix = '';
        if (strpos($userData->roles[0],'distributor') !== false) {
            $prefix = 'distributor';
        } else if (strpos($userData->roles[0],'dealer') !== false) {
            $prefix = 'dealer';
        } else if (strpos($userData->roles[0],'administrator') !== false) {
            $prefix = 'admin';
        } else if (strpos($userData->roles[0],'super_admin') !== false) {
            $prefix = 'super-admin';
        }

        $userData['url_prefix'] = $prefix;
        return $userData;
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

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel(array $data)
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class($data);
    }
}
