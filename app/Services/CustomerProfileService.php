<?php

namespace App\Services;

use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\User;
use App\Models\Wishlist;
use App\Repositories\Interfaces\AddressRepositoryInterface;
use App\Repositories\Interfaces\CustomerProfileRepositoryInterface;
use App\Repositories\Interfaces\WishlistRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CustomerProfileService
{
    protected CustomerProfileRepositoryInterface $customerProfileRepository;

    protected AddressRepositoryInterface $addressRepository;

    protected WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        CustomerProfileRepositoryInterface $customerProfileRepository,
        AddressRepositoryInterface $addressRepository,
        WishlistRepositoryInterface $wishlistRepository
    ) {
        $this->customerProfileRepository = $customerProfileRepository;
        $this->addressRepository = $addressRepository;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function getProfile(User $user): CustomerProfile
    {
        return $user->customerProfile ?? $this->customerProfileRepository->create([
            'user_id' => $user->id,
        ]);
    }

    public function updateProfile(User $user, array $data): CustomerProfile
    {
        $profile = $this->getProfile($user);

        $userUpdate = [];
        if (isset($data['name'])) {
            $userUpdate['name'] = $data['name'];
        }
        if (isset($data['email'])) {
            $userUpdate['email'] = $data['email'];
        }
        if (isset($data['phone'])) {
            $userUpdate['phone'] = $data['phone'];
        }
        if (isset($data['address'])) {
            $userUpdate['address'] = $data['address'];
        }
        if (isset($data['profile_pic']) && $data['profile_pic'] instanceof UploadedFile) {
            if ($user->profile_pic && !str_starts_with($user->profile_pic, 'data:image')) {
                Storage::disk('public')->delete($user->profile_pic);
            }
            $userUpdate['profile_pic'] = $data['profile_pic']->store('profile_pics', 'public');
        } elseif (isset($data['avatar']) && is_string($data['avatar'])) {
            $userUpdate['profile_pic'] = $data['avatar'];
        } elseif (isset($data['profile_pic']) && is_string($data['profile_pic'])) {
            $userUpdate['profile_pic'] = $data['profile_pic'];
        }

        if (! empty($userUpdate)) {
            $user->update($userUpdate);
        }

        $this->customerProfileRepository->update($profile->id, [
            'gender' => $data['gender'] ?? $profile->gender,
            'date_of_birth' => $data['date_of_birth'] ?? $profile->date_of_birth,
            'shipping_address' => $data['shipping_address'] ?? ($data['address'] ?? $profile->shipping_address),
            'billing_address' => $data['billing_address'] ?? $profile->billing_address,
        ]);

        return $profile->refresh();
    }

    // Addresses
    public function getAddresses(User $user): Collection
    {
        return $user->addresses;
    }

    public function addAddress(User $user, array $data): Address
    {
        if (! empty($data['is_default']) && $data['is_default']) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        /** @var Address */
        return $this->addressRepository->create(array_merge($data, ['user_id' => $user->id]));
    }

    public function updateAddress(User $user, int|string $id, array $data): bool
    {
        $address = $this->addressRepository->findOrFail($id);
        if ($address->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (! empty($data['is_default']) && $data['is_default']) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        return $this->addressRepository->update($id, $data);
    }

    // Wishlist
    public function getWishlist(User $user): Collection
    {
        return Wishlist::where('user_id', $user->id)->with(['product.images'])->get();
    }

    public function addToWishlist(User $user, array $data): Wishlist
    {
        return Wishlist::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
        ]);
    }

    public function removeFromWishlist(User $user, int|string $id): bool
    {
        $wishlist = Wishlist::findOrFail($id);
        if ($wishlist->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return $wishlist->delete();
    }
}
