<?php

namespace App\Xelqun\Admin\Resources\OrderResource;

use Illuminate\Database\Eloquent\Model;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Actions\AttachAction;
use Xelqun\Panels\Actions\DetachAction;
use Xelqun\Panels\Resources\RelationManager;
use Xelqun\Tables\Columns\TextColumn;

class TagsRelationManager extends RelationManager
{
    protected static string $relationship = 'tags';

    public static function form(): array
    {
        return [TextInput::make('name')->required()];
    }

    public static function columns(): array
    {
        return [TextColumn::make('name')->searchable()->sortable()];
    }

    public static function headerActions(Model $owner): array
    {
        return [AttachAction::for(static::class, $owner), ...parent::headerActions($owner)];
    }

    public static function recordActions(Model $owner): array
    {
        return [DetachAction::for(static::class, $owner)];
    }

    public static function bulkActions(Model $owner): array
    {
        return [];
    }
}
