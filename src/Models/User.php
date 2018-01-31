<?php

declare(strict_types=1);

namespace Cortex\Fort\Models;

use Rinvex\Tenants\Traits\Tenantable;
use Cortex\Foundation\Traits\Auditable;
use Rinvex\Cacheable\CacheableEloquent;
use Rinvex\Fort\Models\User as BaseUser;
use Rinvex\Attributes\Traits\Attributable;
use Spatie\Activitylog\Traits\HasActivity;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Cortex\Fort\Notifications\PasswordResetNotification;
use Cortex\Fort\Notifications\EmailVerificationNotification;
use Cortex\Fort\Notifications\PhoneVerificationNotification;

/**
 * Cortex\Fort\Models\User.
 *
 * @property int                                                                                                            $id
 * @property string                                                                                                         $username
 * @property string                                                                                                         $password
 * @property string|null                                                                                                    $remember_token
 * @property string                                                                                                         $email
 * @property bool                                                                                                           $email_verified
 * @property \Carbon\Carbon                                                                                                 $email_verified_at
 * @property string                                                                                                         $phone
 * @property bool                                                                                                           $phone_verified
 * @property \Carbon\Carbon                                                                                                 $phone_verified_at
 * @property string                                                                                                         $name_prefix
 * @property string                                                                                                         $first_name
 * @property string                                                                                                         $middle_name
 * @property string                                                                                                         $last_name
 * @property string                                                                                                         $name_suffix
 * @property string                                                                                                         $title
 * @property string                                                                                                         $country_code
 * @property string                                                                                                         $language_code
 * @property array                                                                                                          $two_factor
 * @property string                                                                                                         $birthday
 * @property string                                                                                                         $gender
 * @property bool                                                                                                           $is_active
 * @property \Carbon\Carbon                                                                                                 $last_activity
 * @property \Carbon\Carbon|null                                                                                            $created_at
 * @property \Carbon\Carbon|null                                                                                            $updated_at
 * @property \Carbon\Carbon|null                                                                                            $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\Cortex\Fort\Models\Ability[]                                         $abilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\Cortex\Foundation\Models\Log[]                                  $activity
 * @property-read \Illuminate\Database\Eloquent\Collection|\Cortex\Foundation\Models\Log[]                                  $actions
 * @property-read \Illuminate\Support\Collection                                                                            $all_abilities
 * @property-read \Rinvex\Country\Country                                                                                   $country
 * @property mixed                                                                                                          $entity
 * @property-read \Rinvex\Language\Language                                                                                 $language
 * @property-read string                                                                                                    $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property \Illuminate\Database\Eloquent\Collection|\Cortex\Fort\Models\Role[]                                            $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Session[]                                    $sessions
 * @property \Illuminate\Database\Eloquent\Collection|\Cortex\Tenants\Models\Tenant[]                                       $tenants
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Socialite[]                                  $socialites
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User hasAttribute($key, $value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User role($roles)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereNamePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereNameSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User wherePhoneVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User wherePhoneVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereTwoFactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User withAllTenants($tenants, $group = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User withAnyTenants($tenants, $group = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User withTenants($tenants, $group = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User withoutAnyTenants()
 * @method static \Illuminate\Database\Eloquent\Builder|\Cortex\Fort\Models\User withoutTenants($tenants, $group = null)
 * @mixin \Eloquent
 */
class User extends BaseUser implements HasMedia
{
    // @TODO: Strangely, this issue happens only here!!!
    // Duplicate trait usage to fire attached events for cache
    // flush before other events in other traits specially LogsActivity,
    // otherwise old cached queries used and no changelog recorded on update.
    use CacheableEloquent;
    use Auditable;
    use Tenantable;
    use HasActivity;
    use Attributable;
    use HasMediaTrait;

    /**
     * Indicates whether to log only dirty attributes or all.
     *
     * @var bool
     */
    protected static $logOnlyDirty = true;

    /**
     * The attributes that are logged on change.
     *
     * @var array
     */
    protected static $logAttributes = [
        'username',
        'email',
        'email_verified',
        'phone',
        'phone_verified',
        'name_prefix',
        'first_name',
        'middle_name',
        'last_name',
        'name_suffix',
        'title',
        'country_code',
        'language_code',
        'birthday',
        'gender',
        'is_active',
        'abilities',
        'roles',
    ];

    /**
     * The attributes that are ignored on change.
     *
     * @var array
     */
    protected static $ignoreChangedAttributes = [
        'password',
        'two_factor',
        'email_verified_at',
        'phone_verified_at',
        'last_activity',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $passwordResetNotificationClass = PasswordResetNotification::class;

    /**
     * {@inheritdoc}
     */
    protected $emailVerificationNotificationClass = EmailVerificationNotification::class;

    /**
     * {@inheritdoc}
     */
    protected $phoneVerificationNotificationClass = PhoneVerificationNotification::class;

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    /**
     * Register media collections.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_picture')->singleFile();
        $this->addMediaCollection('cover_photo')->singleFile();
    }
}
