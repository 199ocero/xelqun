<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Xelqun\Panels\Auth\Concerns\InteractsWithAppAuthentication;
use Xelqun\Panels\Contracts\HasAppAuthentication;
use Xelqun\Panels\Contracts\HasEmailAuthentication;
use Xelqun\Panels\Contracts\HasTenants;
use Xelqun\Panels\Contracts\PanelUser;
use Xelqun\Panels\Panel;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasAppAuthentication, HasEmailAuthentication, HasTenants, MustVerifyEmail, PanelUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, InteractsWithAppAuthentication, Notifiable;

    public const string DEMO_EMAIL = 'demo@xelqun.com';

    /** Deliberately public: it's prefilled on the demo's login page. */
    public const string DEMO_PASSWORD = 'demo-password';

    /** What nobody may change on the account everyone shares. */
    private const array LOCKED_ON_DEMO_ACCOUNT = [
        'name',
        'email',
        'password',
        'has_email_authentication',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    /**
     * One check here covers the profile form, two-factor, password reset and the
     * users resource, so none of them can lock everyone else out.
     */
    protected static function booted(): void
    {
        static::updating(function (User $user): void {
            if ($user->isDemoAccount() && $user->isDirty(self::LOCKED_ON_DEMO_ACCOUNT)) {
                throw self::refuseDemoAccountChange($user);
            }
        });

        static::deleting(function (User $user): void {
            if ($user->isDemoAccount()) {
                throw self::refuseDemoAccountChange($user);
            }
        });
    }

    public function isDemoAccount(): bool
    {
        return $this->getOriginal('email') === self::DEMO_EMAIL;
    }

    /**
     * On the field the screen has, because an error on any other shows nothing.
     */
    private static function refuseDemoAccountChange(User $user): ValidationException
    {
        $field = match (true) {
            $user->isDirty('password') => 'password',
            $user->isDirty('app_authentication_secret') => 'code',
            $user->isDirty('has_email_authentication') => 'current_password',
            default => 'email',
        };

        return ValidationException::withMessages([
            "data.{$field}" => "This is the shared demo account, so it can't be changed. Register an account of your own to try this.",
        ]);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

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

    /**
     * @return BelongsToMany<Team, $this>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams()->orderBy('name')->get()->toBase();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $tenant instanceof Team && $this->teams()->whereKey($tenant->getKey())->exists();
    }
}
