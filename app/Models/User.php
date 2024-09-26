<?php

namespace App\Models;

use App\Enums\EnumRoles;
use App\Models\SocialProfile;
use Auresbug\Media\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, HasMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Automatically load relationships.
     *
     * @var array
     */
    protected $with = [
        'media',
    ];

    /**
     * Get the avatar for the user or return a default one if not available.
     *
     * @return string
     */
    public function avatar()
    {
        $media = $this->getMedia('avatar')->last();

        if (!$media) {
            return asset('images/default-avatar.jpeg');
        }

        return route('getFile', [$media->name, 'avatar']);
    }

    /* -------------------------------------------------------------------------- */
    /*                                  Socialite                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Get the social profiles associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialProfiles()
    {
        return $this->hasMany(SocialProfile::class);
    }

    /* -------------------------------------------------------------------------- */
    /*                                 AdminLTE                                   */
    /* -------------------------------------------------------------------------- */

    /**
     * Get the AdminLTE image.
     *
     * @return string
     */
    public function adminlte_image()
    {
        return $this->avatar();
    }

    /**
     * Get the AdminLTE description.
     *
     * @return string
     */
    public function adminlte_desc()
    {
        return 'That\'s a nice guy'; // Consider personalizing this dynamically based on the user's role or data
    }

    /**
     * Get the AdminLTE profile URL.
     *
     * @return string
     */
    public function adminlte_profile_url()
    {
        // return route('profile.show', auth()->user()->id); // Dynamic profile URL

        return 'profile/username';

    }

    /* -------------------------------------------------------------------------- */
    /*                              Media Library                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Register media groups and perform conversions.
     *
     * @return void
     */
    public function registerMediaGroups()
    {
        $this->addMediaGroup('avatar')
            ->performConversions('avatar');
    }

    /* -------------------------------------------------------------------------- */
    /*                                 LaraTables                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Custom query conditions for Laratables.
     *
     * @param  \Illuminate\Database\Eloquent\Builder   $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function laratablesQueryConditions($query)
    {
        return $query->where('name', '<>', EnumRoles::SUDO);
    }

    /**
     * Custom actions for Laratables.
     *
     * @param  \App\Models\User $user
     * @return string
     */
    public static function laratablesCustomAction($user)
    {
        return view('admin.users.includes.index_action', compact('user'))->render();
    }

    /**
     * Custom name column for Laratables.
     *
     * @param  \App\Models\User $user
     * @return string
     */
    public static function laratablesCustomName($user)
    {
        return view('admin.users.includes.index_name', compact('user'))->render();
    }

    /**
     * Additional columns to be loaded for Laratables.
     *
     * @return array<string>
     */
    public static function laratablesAdditionalColumns()
    {
        return ['name'];
    }
}
