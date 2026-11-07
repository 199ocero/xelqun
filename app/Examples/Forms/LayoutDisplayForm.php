<?php

namespace App\Examples\Forms;

use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Conditions\When;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Callout;
use Xelqun\Forms\Layouts\EmptyState;
use Xelqun\Forms\Layouts\Flex;
use Xelqun\Forms\Layouts\FusedGroup;
use Xelqun\Forms\Layouts\Icon;
use Xelqun\Forms\Layouts\Image;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\Layouts\Text;
use Xelqun\Forms\Layouts\UnorderedList;
use Xelqun\Forms\State\Get;

/**
 * Layouts that arrange fields, and components that only show something.
 */
class LayoutDisplayForm extends Form
{
    protected function boot(): void
    {
        $this->unsavedChangesAlerts();
    }

    public function schema(): array
    {
        return [
            Callout::make('Changes here need a review')
                ->description('Type in a field, then pick another example: the page warns you first.')
                ->warning(),

            Flex::make([
                Section::make('Article')->schema([
                    TextInput::make('title')
                        ->required()
                        ->afterLabel(Text::make('Shown in search')->size('xs'))
                        ->beforeContent(Icon::make('pencil')),
                    Select::make('category')->options(['news' => 'News', 'guide' => 'Guide']),
                    Textarea::make('summary')
                        ->rows(3)
                        ->belowContent(fn (Get $get): string => strlen((string) $get('title')).' characters in the title'),
                ]),
                Section::make('Publishing')->grow(false)->schema([
                    Toggle::make('is_published')->label('Published')->belowContent('Readers see it right away.'),
                    Image::make('/favicon.svg', 'The site logo')->imageSize(40)->alignCenter()->tooltip('Your logo'),
                ]),
            ])->from('lg')->columnSpanFull(),

            Section::make('Location')->schema([
                FusedGroup::make([
                    TextInput::make('city')->placeholder('City')->required()->columnSpan(2),
                    Select::make('country')->placeholder('Country')->options(['NO' => 'Norway', 'PH' => 'Philippines']),
                ])->label('Where it happened')->columns(3),
            ]),

            Section::make('Text, icons and lists')->schema([
                Text::make('A badge')->badge()->color('primary')->icon('star'),
                Text::make('Bold monospace text')->weight('bold')->fontFamily('mono')->color('neutral'),
                Icon::make('circle-check')->color('success')->size('lg')->tooltip('Checked'),
                UnorderedList::make(['Plain strings become items', Text::make('So do components')->color('danger')]),
            ]),

            EmptyState::make('No attachments yet')
                ->description('Files you add to this article show up here.')
                ->icon('inbox')
                ->visibleWhen(When::eq('is_published', false)),
        ];
    }
}
