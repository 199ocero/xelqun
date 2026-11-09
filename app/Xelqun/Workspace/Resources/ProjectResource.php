<?php

namespace App\Xelqun\Workspace\Resources;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\Pages\ManageRecords;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Table;

/**
 * Like every resource here, it holds no team code. The panel's tenant() keeps its
 * rows, counts and new records in the team in the URL.
 */
class ProjectResource extends Resource
{
    protected static string $model = Project::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'folder-tree';

    protected static int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount(['tasks', 'tasks as open_tasks_count' => fn (Builder $query) => $query->where('status', '!=', 'done')]);
    }

    public static function form(): array
    {
        return [
            TextInput::make('name')->required()->maxLength(255)->scopedUnique(),
            TextInput::make('client')->required()->maxLength(255),
            Select::make('status')->required()->default('planning')->options(Project::STATUSES),
            TextInput::make('budget')->numeric()->minValue(0)->prefix('$'),
            DatePicker::make('due_on')->label('Due'),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('client')->searchable()->sortable(),
            TextColumn::make('status')->badge()
                ->formatStateUsing(fn (string $state): string => Project::STATUSES[$state] ?? $state)
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'on_hold' => 'warning',
                    'done' => 'gray',
                    default => 'info',
                }),
            TextColumn::make('open_tasks_count')->label('Open tasks')->sortable(),
            TextColumn::make('tasks_count')->label('Tasks')->sortable()->toggleable(isHiddenByDefault: true),
            TextColumn::make('budget')->money('USD')->sortable(),
            TextColumn::make('due_on')->label('Due')->date()->sortable(),
        ];
    }

    public static function filters(): array
    {
        return [SelectFilter::make('status')->options(Project::STATUSES)];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultSort('due_on');
    }

    public static function getPages(): array
    {
        return ['index' => ManageRecords::class];
    }
}
