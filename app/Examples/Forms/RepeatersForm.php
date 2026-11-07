<?php

namespace App\Examples\Forms;

use Xelqun\Forms\Components\Checkbox;
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\Repeater\TableColumn;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Section;

/**
 * The ways a repeater can look and behave. Rows of an order nest two levels on
 * the order example.
 */
class RepeatersForm extends Form
{
    public function schema(): array
    {
        return [
            Section::make('Simple')
                ->description('One field per row, and the data is a flat list.')
                ->schema([
                    Repeater::make('invitations')
                        ->hiddenLabel()
                        ->simple(TextInput::make('email')->email()->placeholder('ada@example.com'))
                        ->itemLabel('Invitation')
                        ->defaultItems(2),
                ]),

            Section::make('Table')
                ->description('Rows as a table. Roles must be distinct, and there can only be one owner.')
                ->schema([
                    Repeater::make('members')
                        ->hiddenLabel()
                        ->itemLabel('Member')
                        ->table([
                            TableColumn::make('Name')->markAsRequired(),
                            TableColumn::make('Role')->width('180px'),
                            TableColumn::make('Owner')->width('80px'),
                        ])
                        ->default([
                            ['name' => 'Ada', 'role' => 'admin', 'owner' => true],
                            ['name' => 'Grace', 'role' => 'admin', 'owner' => true],
                        ])
                        ->requiresDeleteConfirmation()
                        ->schema([
                            TextInput::make('name')->required(),
                            Select::make('role')->options(['admin' => 'Admin', 'editor' => 'Editor', 'viewer' => 'Viewer'])->distinct(),
                            Checkbox::make('owner')->distinct(),
                        ]),
                ]),

            Section::make('Cards')
                ->description('A grid of rows that can be copied and collapsed. Rows here can be dragged by their handle.')
                ->schema([
                    Repeater::make('plans')
                        ->hiddenLabel()
                        ->itemLabel('Plan')
                        ->grid(2)
                        ->cloneable()
                        ->collapsible()
                        ->defaultItems(2)
                        ->schema([
                            TextInput::make('name')->required()->validationMessages(['required' => 'Name this plan.']),
                            Textarea::make('notes')->rows(2),
                        ]),
                ]),

            Section::make('Nested')
                ->description('A repeater inside a repeater, each level with its own rows.')
                ->schema([
                    Repeater::make('sections')
                        ->hiddenLabel()
                        ->itemLabel('Section')
                        ->defaultItems(1)
                        ->schema([
                            TextInput::make('title'),
                            Repeater::make('questions')
                                ->itemLabel('Question')
                                ->itemNumbers(false)
                                ->schema([TextInput::make('text')]),
                        ]),
                ]),
        ];
    }
}
