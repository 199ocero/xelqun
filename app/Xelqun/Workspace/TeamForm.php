<?php

namespace App\Xelqun\Workspace;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;

/**
 * Starts a team and changes one.
 */
class TeamForm extends Form
{
    public function model(): string
    {
        return Team::class;
    }

    public function schema(): array
    {
        return [
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255)->alphaDash()->unique(),
        ];
    }

    public function afterSave(Model $record, array $data): void
    {
        $user = auth()->user();

        if ($record instanceof Team && $user instanceof User) {
            $record->users()->syncWithoutDetaching([$user->id]);
        }
    }
}
