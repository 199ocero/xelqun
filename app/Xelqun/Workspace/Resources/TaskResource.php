<?php

namespace App\Xelqun\Workspace\Resources;

use App\Models\Task;
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
 * The project and assignee lists only offer this team's projects and members,
 * because both models belong to a resource in this panel.
 */
class TaskResource extends Resource
{
    protected static string $model = Task::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'layers';

    protected static int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['project', 'assignee']);
    }

    public static function form(): array
    {
        return [
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Select::make('project_id')->label('Project')->required()->relationship('project', 'name')->searchable()->preload(),
            Select::make('user_id')->label('Assignee')->relationship('assignee', 'name')->searchable()->preload(),
            Select::make('status')->required()->default('todo')->options(Task::STATUSES),
            Select::make('priority')->required()->default('medium')->options(Task::PRIORITIES),
            DatePicker::make('due_on')->label('Due'),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('title')->searchable()->sortable()->wrap(),
            TextColumn::make('project.name')->label('Project')->sortable(),
            TextColumn::make('assignee.name')->label('Assignee'),
            TextColumn::make('status')->badge()
                ->formatStateUsing(fn (string $state): string => Task::STATUSES[$state] ?? $state)
                ->color(fn (string $state): string => match ($state) {
                    'doing' => 'info',
                    'done' => 'success',
                    default => 'gray',
                }),
            TextColumn::make('priority')->badge()
                ->formatStateUsing(fn (string $state): string => Task::PRIORITIES[$state] ?? $state)
                ->color(fn (string $state): string => match ($state) {
                    'high' => 'danger',
                    'medium' => 'warning',
                    default => 'gray',
                }),
            TextColumn::make('due_on')->label('Due')->date()->sortable(),
        ];
    }

    public static function filters(): array
    {
        return [
            SelectFilter::make('project_id')->label('Project')->relationship('project', 'name'),
            SelectFilter::make('user_id')->label('Assignee')->relationship('assignee', 'name'),
            SelectFilter::make('status')->options(Task::STATUSES),
            SelectFilter::make('priority')->options(Task::PRIORITIES),
        ];
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
