# ČVUT Socialite provider

This is a custom [Laravel Socialite](https://www.github.com/laravel/socialite) provider for the authentication server of the FIT CTU in Prague.

The authentication server can be found [here](https://auth.fit.cvut.cz).

## Installation

To install this package simply add `jirkavrba/cvut-socialite-provider` to your `composer.json` file.
This can be also done via the composer command
```
composer require jirkavrba/cvut-socialite-provider
```

Then, add your credentials to the `.env` 
```dotenv
CVUT_CLIENT_ID=""
CVUT_CLIENT_SECRET=""
CVUT_CALLBACK_URL=""

```
and `config/services.php`
```php
return [
    // Other services
    'cvut' => [
        'client_id'     => env('CVUT_CLIENT_ID'),
        'client_secret' => env('CVUT_CLIENT_SECRET'),
        'redirect'      => env('CVUT_CALLBACK_URL'),
    ],
];
```


You also need to extend the socialite service within a service provider, this
can be done eg. in the `AppServiceProvider` by creating a private method

```php
private function bootCvutSocialiteProvider()
{
    $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
    $socialite->extend(
        'cvut',
        function ($app) use ($socialite) {
            $config = $app['config']['services.cvut'];
            return $socialite->buildProvider(CvutProvider::class, $config);
        }
    );
}
```
and then call this method from within the `boot()` method.

This should be all you need to do, then you can simply use the provider within your controllers like:
```php
public function redirectToProvider(): RedirectResponse
{
    return Socialite::with('cvut')->redirect();
}

```

