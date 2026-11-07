<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Author;
use App\Xelqun\Admin\Resources\AuthorResource\PostsRelationManager;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\TextColumn;

class AuthorResource extends Resource
{
    protected static string $model = Author::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'user';

    protected static ?string $navigationGroup = 'Blog';

    public static function getRelations(): array
    {
        return [PostsRelationManager::class];
    }

    public static function form(): array
    {
        return [
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            TextInput::make('github')->prefix('github.com/'),
            Textarea::make('bio')->rows(3)->maxLength(500)->columnSpanFull(),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('github')->toggleable(),
        ];
    }
}
