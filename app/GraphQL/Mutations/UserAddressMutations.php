<?php
namespace App\GraphQL\Mutations;

use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

class UserAddressMutations
{
    public function addAddress($_, array $args)
    {
        return UserAddress::create([
            'user_id' => Auth::id(),
            'full_name' => 'user name',
            'address' => $args['address'],
            'city' => $args['city'],
            'district' => 'district',
            'country' => $args['country'],
        ]);
    }

    public function updateAddress($_, array $args)
    {


        $address = UserAddress::where('id', $args['id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $address->update($args);

        return $address;
    }

    public function deleteAddress($_, array $args)
    {
        $address = UserAddress::where('id', $args['id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return $address->delete();
    }
}
