<?php

namespace App\Xelqun\Workspace\Resources;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Panels\Resources\Pages\ListRecords;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\TextColumn;

/**
 * Users join a team through team_user, so they belong to it by the many-to-many
 * teams() instead of a team(). Read only: the demo has no invitations.
 */
class MemberResource extends Resource
{
    protected static string $model = User::class;

    protected static ?string $slug = 'members';

    protected static ?string $modelLabel = 'member';

    protected static ?string $tenantOwnershipRelationship = 'teams';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'users';

    protected static int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount(['assignedTasks as open_tasks_count' => fn (Builder $query) => $query->where('status', '!=', 'done')]);
    }

    public static function form(): array
    {
        return [];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('open_tasks_count')->label('Open tasks here')->sortable(),
        ];
    }

    public static function recordActions(): array
    {
        return [];
    }

    public static function bulkActions(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return ['index' => ListRecords::class];
    }
}
