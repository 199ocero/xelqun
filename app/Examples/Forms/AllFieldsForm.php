<?php

namespace App\Examples\Forms;

use Xelqun\Forms\Components\CheckboxList;
use Xelqun\Forms\Components\ColorPicker;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\DateTimePicker;
use Xelqun\Forms\Components\KeyValue;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Slider;
use Xelqun\Forms\Components\TagsInput;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\TimePicker;
use Xelqun\Forms\Components\ToggleButtons;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Section;

/**
 * The everyday fields, with the options people reach for first.
 */
class AllFieldsForm extends Form
{
    public function schema(): array
    {
        return [
            Section::make('Text inputs')->columns(2)->schema([
                TextInput::make('phone')
                    ->mask('(999) 999-9999')
                    ->stripCharacters(['(', ')', ' ', '-'])
                    ->prefixIcon('phone')
                    ->hint('Masked while you type, stored as digits.'),
                TextInput::make('secret')->label('API secret')->password()->revealable(),
                TextInput::make('api_key')->label('API key')->default('xq_live_8f3a')->copyable()->readOnly(),
                TextInput::make('car')->datalist(['BMW', 'Ford', 'Porsche'])->hint('Suggestions, not a fixed list.'),
                TextInput::make('code')->length(6)->trim()->hint('Exactly six characters.'),
                TextInput::make('email')->email()->suffixIcon('mail'),
            ]),

            Section::make('Dates and times')->columns(2)->schema([
                DateTimePicker::make('launch_at')
                    ->label('Launch')
                    ->seconds(false)
                    ->minutesStep(15)
                    ->timezone('Asia/Manila')
                    ->displayFormat('D, j M Y')
                    ->hint('Shown in Manila, stored in UTC.'),
                DatePicker::make('review_on')
                    ->label('Review day')
                    ->disabledDates([now()->addDay()->format('Y-m-d')])
                    ->hint('Tomorrow is taken.'),
                TimePicker::make('opens_at')->label('Opens at')->seconds(false)->minDate('08:00')->maxDate('18:00'),
            ]),

            Section::make('Lists')->columns(2)->schema([
                Select::make('priority')
                    ->options([
                        'Soon' => ['today' => 'Today', 'tomorrow' => 'Tomorrow'],
                        'Later' => ['next_week' => 'Next week', 'someday' => 'Someday'],
                    ])
                    ->disableOptionWhen(fn (string $value): bool => $value === 'someday'),
                Select::make('stack')
                    ->multiple()
                    ->reorderable()
                    ->options(['vue' => 'Vue', 'laravel' => 'Laravel', 'inertia' => 'Inertia'])
                    ->hint('Move picked items with their arrows.'),
                Select::make('newsletter')->boolean(placeholder: 'Would you like the newsletter?'),
                ToggleButtons::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])
                    ->colors(['draft' => 'warning', 'published' => 'success', 'archived' => 'danger'])
                    ->icons(['draft' => 'pencil', 'published' => 'check', 'archived' => 'folder'])
                    ->disableOptionWhen(fn (string $value): bool => $value === 'archived')
                    ->inline(),
                ToggleButtons::make('reviewed')->boolean()->grouped(),
                CheckboxList::make('perks')
                    ->options(['shipping' => 'Free shipping', 'returns' => 'Free returns', 'support' => 'Priority support', 'priority' => 'Early access'])
                    ->descriptions(['support' => 'Replies within a day.'])
                    ->disableOptionWhen(fn (string $value): bool => $value === 'priority')
                    ->columns(2)
                    ->bulkToggleable()
                    ->searchable()
                    ->columnSpanFull(),
            ]),

            Section::make('Everything else')->columns(2)->schema([
                TagsInput::make('labels')->suggestions(['urgent', 'internal', 'bug'])->splitKeys(['Tab', ','])->reorderable(),
                ColorPicker::make('accent')->default('#6366f1'),
                Slider::make('volume')->range(0, 100)->step(10)->tooltips()->default(30),
                Slider::make('budget')->range(0, 1000)->step(50)->tooltips()->default([200, 600]),
                KeyValue::make('meta')
                    ->label('Metadata')
                    ->keyPlaceholder('color')
                    ->valuePlaceholder('blue')
                    ->default(['size' => 'large'])
                    ->reorderable()
                    ->columnSpanFull(),
            ]),
        ];
    }
}
