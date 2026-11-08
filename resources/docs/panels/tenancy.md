---
title: Tenancy
description: Split a panel between teams, shops or companies, with records kept in their tenant for every query.
---

Every page moves under `/admin/{tenant}`.

```php
$panel->tenant(Team::class, slugAttribute: 'slug');
```

Say who belongs where on your user model.

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Xelqun\Panels\Contracts\HasTenants;
use Xelqun\Panels\Contracts\PanelUser;
use Xelqun\Panels\Panel;

class User extends Authenticatable implements HasTenants, PanelUser
{
    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }
}
```

## A minimal setup

The tenant needs a slug column, and users join it through a pivot table:

```php
Schema::create('teams', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->timestamps();
});

Schema::create('team_user', function (Blueprint $table) {
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->primary(['team_id', 'user_id']);
});
```

The user model has the `teams()` that `getTenants()` reads, and `Team` has `users()` the other way:

```php
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

public function teams(): BelongsToMany
{
    return $this->belongsToMany(Team::class);
}
```

## Ownership

Each resource's model needs a relationship to the tenant, named after it, such as `team()`, unless you pass `ownershipRelationship`, or set `$tenantOwnershipRelationship` on the resource. It can be a belongs-to or a many-to-many.

Set `$isScopedToTenant = false` on a resource that isn't owned by a tenant.

## What stays inside

Inside a tenant, those models stay in it for every query of the request: tables, search, Select options, widgets, `scopedUnique()`, `scopedExists()` and your own code.

New records join the tenant on their own, and it never comes from the browser. A tenant the user isn't in is a 404, and taking someone out works at once, even on an open page.

## Registering and editing

A switcher moves between tenants, in the sidebar or, with `topNavigation()`, the top bar.

<!-- screenshot: tenancy | The tenant switcher open in the top bar -->

```php
$panel->tenantRegistration(RegisterTeam::class)->tenantProfile(EditTeam::class);
```

`tenantRegistration()` lets a user start a tenant, and is where someone with no tenants lands. Its form has to fill the slug, and add the user in `afterSave()`.

```php
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;

class RegisterTeam extends Form
{
    public function model(): string
    {
        return Team::class;
    }

    public function schema(): array
    {
        return [
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->alphaDash()->unique(),
        ];
    }

    public function afterSave(Model $record, array $data): void
    {
        assert($record instanceof Team);

        $record->users()->attach(auth()->user());
    }
}
```

`tenantProfile()` adds a page to change one, which needs `update` in the tenant's policy. Both take a `Form` class.

```php
public function update(User $user, Team $team): bool
{
    return $user->teams()->whereKey($team)->exists();
}
```

The switcher lists 20 tenants, always with the current one, and a search box finds the rest.

> [!NOTE]
> Notifications belong to the user, not the tenant. Tenant billing isn't built in.

[See it in the demo](demo:/workspace).
