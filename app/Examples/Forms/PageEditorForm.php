<?php

namespace App\Examples\Forms;

use Xelqun\Forms\Components\Builder;
use Xelqun\Forms\Components\Builder\Block;
use Xelqun\Forms\Components\FileUpload;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Section;

/**
 * A page made of blocks. Each row is {type, data}, in one JSON column.
 */
class PageEditorForm extends Form
{
    public function schema(): array
    {
        return [
            Section::make('Page')->schema([
                TextInput::make('title')->required(),

                Builder::make('content')
                    ->label('Blocks')
                    ->itemLabel('Block')
                    ->addActionLabel('Add a block')
                    ->collapsible()
                    ->cloneable()
                    ->blockPreviews()
                    ->minItems(1)
                    ->default([['type' => 'quote', 'data' => ['quote' => 'Simple things should be simple.', 'author' => 'Alan Kay']]])
                    ->blocks([
                        'Text' => [
                            Block::make('heading')
                                ->icon('hash')
                                ->maxItems(1)
                                ->columns(2)
                                ->schema([
                                    TextInput::make('text')->required(),
                                    Select::make('level')->options(['h2' => 'Heading 2', 'h3' => 'Heading 3'])->default('h2')->required(),
                                ]),
                            Block::make('paragraph')->icon('pencil')->schema([Textarea::make('text')->required()]),
                            Block::make('quote')
                                ->icon('star')
                                ->preview('examples.quote-preview')
                                ->schema([Textarea::make('quote')->required(), TextInput::make('author')]),
                        ],
                        'Media' => [
                            Block::make('image')->icon('image')->schema([
                                FileUpload::make('url')->label('Image')->image()->required(),
                                TextInput::make('alt')->required(),
                            ]),
                        ],
                    ]),
            ]),
        ];
    }
}
