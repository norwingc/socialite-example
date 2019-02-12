<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Provider;
use App\User;
use Socialite;
use Auth;

class SocialAuthController extends Controller
{
    /**
     * [handleProviderCallback description]
     * @param   [type]  $provider  [$provider description]
     * @return  [type]             [return description]
    */
    public function handleProviderCallback($provider)
    {
        $user = $this->createOrGetUser(Socialite::driver($provider));
        auth()->login($user);
        return redirect()->to('/');
    }

    /**
     * [redirectToProvider description]
     * @param   [type]  $provider  [$provider description]
     * @return  [type]             [return description]
    */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * [findOrCreateUser description]
     * @param   [type]  $user      [$user description]
     * @param   [type]  $provider  [$provider description]
     * @return  [type]             [return description]
     */
    public function createOrGetUser(Provider $provider)
    {
        $providerUser = $provider->user();

        $providerName = class_basename($provider);

        $user = User::whereProvider($providerName)
            ->whereProviderId($providerUser->getId())
            ->first();

        if (!$user) {
            $user = User::create([
                'email' => $providerUser->getEmail(),
                'name' => $providerUser->getName(),
                'provider_id' => $providerUser->getId(),
                'provider' => $providerName
            ]);
        }

        return $user;
    }
}
