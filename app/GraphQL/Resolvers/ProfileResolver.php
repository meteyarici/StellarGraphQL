<?php


namespace App\GraphQL\Resolvers;

use Illuminate\Support\Facades\Auth;

class ProfileResolver
{
    // PROFİLİ GÖSTERME (me)
    public function me($_, array $args)
    {
        return Auth::user();
    }

    // PROFİL GÜNCELLEME
    public function updateProfile($_, array $args)
    {
        $user = Auth::user();

        $allowed = ['name', 'phone', 'birth_date', 'address'];

        foreach ($allowed as $field) {
            if (isset($args[$field])) {
                $user->{$field} = $args[$field];
            }
        }

        $user->save();

        return $user;
    }
}
