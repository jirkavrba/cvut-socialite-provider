<?php

namespace JirkaVrba\CvutSocialiteProvider;


use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

/**
 * Class CvutProvider
 * @package JirkaVrba\CvutSocialiteProvider
 */
class CvutProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://auth.fit.cvut.cz/oauth/authorize', $state);
    }

    /**
     * @return string
     */
    protected function getTokenUrl(): string
    {
        return 'https://auth.fit.cvut.cz/oauth/token';
    }

    /**
     * @param string $code
     * @return array
     */
    public function getAccessTokenResponse($code): array
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(),
            [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => $this->getTokenFields($code),
            ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $code
     * @return array
     */
    protected function getTokenFields($code): array
    {
        return Arr::add(parent::getTokenFields($code), 'grant_type', 'authorization_code');
    }

    /**
     * @param string $token
     * @return array
     */
    protected function getUserByToken($token): array
    {
        $parameters = ['query' => ['token' => $token]];
        $response = $this->getHttpClient()->get('https://auth.fit.cvut.cz/oauth/check_token', $parameters);

        return json_decode($response->getBody(), true);
    }

    /**
     * @param array $data
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $data): User
    {
        $user = new User;
        $user->setRaw($data);

        return $user->map(['username' => $data['user_name']]);
    }

}
