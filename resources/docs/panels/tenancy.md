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

<!-- screenshot: tenancy | The tenant switcher open in the sidebar, on a team's dashboard -->

```php
$panel->tenantRegistration(RegisterTeam::class)->tenantProfile(EditTeam::class);
```

`tenantRegistration()` lets a user start a tenant, and is where someone with no tenants lands. Its form has to fill the slug, and add the user in `afterSave()`.

```php
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;

/**
 * @extends Form<Team>
 */
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

## In the demo

[The demo's workspace](demo:/workspace) is a second panel, made multi-tenant by `tenant()`. It looks like the admin panel and has the switcher at the top of the sidebar. The demo account is in three teams: Acme Studio, Harbor Support and Orbit Labs. Each has its own projects, tasks, notes and members, and some staff are in two teams.

None of its resources has team code. Switch teams and watch what follows:

- The tables, search and the dashboard's numbers only count the current team.
- A task's project and assignee lists only offer this team's projects and members.
- A new project or note lands in the current team.
- `/workspace/{team}` for a team you aren't in is a 404.

Members show how a many-to-many owner works. Users join a team through `team_user`, so the resource names `teams()`, the relationship it belongs by:

```php
class MemberResource extends Resource
{
    protected static string $model = User::class;

    protected static ?string $tenantOwnershipRelationship = 'teams';
}
```

With that resource in the panel, `User` is kept to the team too, which is what limits the assignee list.

Sign up for your own account and you land on the page to start a team, with nothing in it but you.
