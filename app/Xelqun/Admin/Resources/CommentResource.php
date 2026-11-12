<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Product;
use Xelqun\Forms\Components\MorphToSelect;
use Xelqun\Forms\Components\MorphToSelect\Type;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\CheckboxColumn;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Table;

/**
 * Grouped, striped rows, with a message of our own when nothing matches.
 */
class CommentResource extends Resource
{
    protected static string $model = Comment::class;

    protected static ?string $navigationIcon = 'message-square';

    protected static ?string $navigationGroup = 'Blog';

    public static function form(): array
    {
        return [
            MorphToSelect::make('commentable')
                ->label('On')
                ->columnSpanFull()
                ->required()
                ->searchable()
                ->types([
                    Type::make(Post::class)->titleAttribute('title'),
                    Type::make(Product::class)->titleAttribute('name'),
                ]),
            TextInput::make('name')->required()->maxLength(255),
            Select::make('status')->required()->default('pending')->options(['pending' => 'Pending', 'approved' => 'Approved', 'spam' => 'Spam']),
            Textarea::make('body')->required()->rows(4)->columnSpanFull(),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('body')->searchable()->wrap(),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'approved' => 'success',
                'spam' => 'danger',
                default => 'warning',
            }),
            CheckboxColumn::make('is_approved')->label('Approved')->authorize('update'),
        ];
    }

    public static function filters(): array
    {
        return [SelectFilter::make('status')->options(['pending' => 'Pending', 'approved' => 'Approved', 'spam' => 'Spam'])];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultGroup('status')->striped()->emptyState('No comments here', 'Try clearing your search or filters.');
    }
}
