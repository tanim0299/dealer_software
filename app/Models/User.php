<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\FileUploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function createUser($request)
    {
        $userData = [
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ];
        if(!empty($request->file('image')))
        {
            $userData['image'] = FileUploader::upload($request->file('image'),'user');
        }
        $user = self::create($userData);
        $this->roleAssign($user, $request->role_id);

        return $user;
    }

    public function roleAssign($user, $roles)
    {
        
        $previous_roles = $user->roles()->pluck('id');
        if(is_array($previous_roles))
        {
            for ($i=0; $i < count($previous_roles) ; $i++)
            {
                $user->removeRole($previous_roles[$i]);
            }
        }
        $role = Role::find($roles);
        $user->assignRole($role);
        
    }
}
