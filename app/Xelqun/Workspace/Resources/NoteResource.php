<?php

namespace App\Xelqun\Workspace\Resources;

use App\Models\Note;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\Pages\ManageRecords;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\TextColumn;

/**
 * Its records stay in the current team with no code here. The panel's tenant()
 * does that.
 */
class NoteResource extends Resource
{
    protected static string $model = Note::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'file-text';

    public static function form(): array
    {
        return [
            TextInput::make('title')->required()->scopedUnique(),
            Textarea::make('body')->rows(4),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('created_at')->date()->sortable(),
        ];
    }

    public static function getPages(): array
    {
        return ['index' => ManageRecords::class];
    }
}
