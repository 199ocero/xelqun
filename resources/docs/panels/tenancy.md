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

`tenantRegistration()` lets a user start a tenant, and is where someone with no tenants lands. Its form has to add the user in `afterSave()`.

`tenantProfile()` adds a page to change one, which needs `update` in the tenant's policy. Both take a `Form` class.

> [!NOTE]
> Notifications belong to the user, not the tenant. Tenant billing isn't built in.

[See it in the demo](demo:/workspace).
