<?php

namespace App\Xelqun\Admin\Resources\AuthorResource;

use Illuminate\Database\Eloquent\Model;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Actions\AssociateAction;
use Xelqun\Panels\Actions\DissociateAction;
use Xelqun\Panels\Resources\RelationManager;
use Xelqun\Tables\Columns\TextColumn;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    protected static string $recordTitleAttribute = 'title';

    public static function form(): array
    {
        return [
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('status')->badge(),
        ];
    }

    public static function headerActions(Model $owner): array
    {
        return [...parent::headerActions($owner), AssociateAction::for(static::class, $owner)];
    }

    public static function recordActions(Model $owner): array
    {
        return [...parent::recordActions($owner), DissociateAction::for(static::class, $owner)];
    }
}
