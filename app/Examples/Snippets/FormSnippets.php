<?php

namespace App\Examples\Snippets;

use App\Examples\Tables\CategoriesTable;
use App\Models\Post;
use App\Models\Product;
use Closure;
use Illuminate\Support\Str;
use Xelqun\Actions\Action;
use Xelqun\Forms\Components\Checkbox;
use Xelqun\Forms\Components\CheckboxList;
use Xelqun\Forms\Components\CodeEditor;
use Xelqun\Forms\Components\CodeEditor\Language;
use Xelqun\Forms\Components\ColorPicker;
use Xelqun\Forms\Components\Component;
use Xelqun\Forms\Components\CustomField;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\DateTimePicker;
use Xelqun\Forms\Components\FileUpload;
use Xelqun\Forms\Components\KeyValue;
use Xelqun\Forms\Components\MarkdownEditor;
use Xelqun\Forms\Components\MorphToSelect;
use Xelqun\Forms\Components\MorphToSelect\Type;
use Xelqun\Forms\Components\Radio;
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\RichEditor;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Slider;
use Xelqun\Forms\Components\TagsInput;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\TimePicker;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Components\ToggleButtons;
use Xelqun\Forms\Layouts\Callout;
use Xelqun\Forms\Layouts\EmptyState;
use Xelqun\Forms\Layouts\Fieldset;
use Xelqun\Forms\Layouts\Flex;
use Xelqun\Forms\Layouts\FusedGroup;
use Xelqun\Forms\Layouts\Grid;
use Xelqun\Forms\Layouts\Group;
use Xelqun\Forms\Layouts\Icon;
use Xelqun\Forms\Layouts\Image;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\Layouts\Tab;
use Xelqun\Forms\Layouts\Tabs;
use Xelqun\Forms\Layouts\Text;
use Xelqun\Forms\Layouts\UnorderedList;
use Xelqun\Forms\State\Get;
use Xelqun\Forms\State\Set;
use Xelqun\Tables\Forms\ModalTableSelect;

/**
 * The preview of each docs page, matching the code on that page.
 */
final class FormSnippets
{
    /**
     * @return array<string, Closure(): array<int, Component>>
     */
    public static function all(): array
    {
        return [
            'overview' => fn (): array => [
                TextInput::make('customer')->required(),
                Select::make('country')->options(['ph' => 'Philippines', 'jp' => 'Japan', 'de' => 'Germany'])->searchable(),
                Repeater::make('items')->defaultItems(1)->columns(3)->schema([
                    TextInput::make('qty')->numeric()->default(1),
                    TextInput::make('price')->numeric()->default(20),
                    TextInput::make('subtotal')->computed('qty * price')->disabled(),
                ]),
                TextInput::make('total')->computed('sum(items.*.subtotal)')->disabled(),
            ],
            'field-options' => fn (): array => [
                TextInput::make('name')->label('Full name')->placeholder('Ada Lovelace')->helperText('As it appears on your ID.')->required(),
                TextInput::make('reference')->default('ORD-1042')->readOnly(),
                TextInput::make('slug')->default('summer-sale')->disabled(),
            ],
            'hints' => fn (): array => [
                TextInput::make('password')
                    ->password()
                    ->hint('Case sensitive')
                    ->hintColor('warning')
                    ->hintIcon('info', tooltip: 'Ask an admin to reset it.')
                    ->helperText('At least twelve characters.')
                    ->markAsRequired(),
                TextInput::make('api_key')
                    ->hintAction(
                        Action::make('generate')
                            ->authorize(true)
                            ->link()
                            ->action(fn (Set $set) => $set('api_key', Str::random(32))),
                    ),
            ],
            'text-input' => fn (): array => [
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->prefixIcon('mail'),
                TextInput::make('domain')->prefix('https://')->suffix('.com'),
                TextInput::make('password')->password()->revealable(),
                TextInput::make('card')->mask('9999 9999 9999 9999')->placeholder('4242 4242 4242 4242'),
                TextInput::make('api_key')->default('xq_live_8f2a1c')->readOnly()->copyable(copyMessage: 'Copied'),
            ],
            'textarea' => fn (): array => [Textarea::make('notes')->rows(6)->maxLength(2000)],
            'select' => fn (): array => [
                Select::make('status')->options(['draft' => 'Draft', 'reviewing' => 'Reviewing', 'published' => 'Published']),
                Select::make('technologies')->multiple()->searchable()->options(['laravel' => 'Laravel', 'vue' => 'Vue', 'inertia' => 'Inertia', 'tailwind' => 'Tailwind CSS']),
                Select::make('plan')
                    ->options(['free' => 'Free', 'pro' => 'Pro'])
                    ->disableOptionWhen(fn (string $value): bool => $value === 'pro'),
            ],
            'checkbox' => fn (): array => [
                Checkbox::make('terms')->label('I accept the terms')->description('You can read them at any time from your account.'),
            ],
            'checkbox-list' => fn (): array => [
                CheckboxList::make('technologies')
                    ->options(['laravel' => 'Laravel', 'vue' => 'Vue', 'inertia' => 'Inertia', 'tailwind' => 'Tailwind CSS'])
                    ->columns(2)
                    ->searchable()
                    ->bulkToggleable(),
            ],
            'radio' => fn (): array => [
                Radio::make('plan')
                    ->options(['free' => 'Free', 'pro' => 'Pro'])
                    ->descriptions(['free' => 'For side projects.', 'pro' => 'For teams.'])
                    ->default('free'),
            ],
            'toggle' => fn (): array => [
                Toggle::make('is_visible')->label('Visible to customers')->description('Hidden products stay in reports.')->default(true),
            ],
            'toggle-icons' => fn (): array => [
                Toggle::make('is_published')
                    ->onIcon('check')
                    ->offIcon('x')
                    ->onColor('success')
                    ->offColor('danger'),
            ],
            'toggle-buttons' => fn (): array => [
                ToggleButtons::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->colors(['draft' => 'warning', 'published' => 'success'])
                    ->icons(['draft' => 'pencil', 'published' => 'check'])
                    ->default('draft')
                    ->inline(),
                ToggleButtons::make('size')->options(['s' => 'S', 'm' => 'M', 'l' => 'L'])->grouped(),
            ],
            'date-time' => fn (): array => [
                DatePicker::make('date_of_birth'),
                DateTimePicker::make('published_at'),
                TimePicker::make('opens_at')->seconds(false),
            ],
            'file-upload' => fn (): array => [
                FileUpload::make('photo')->image()->avatar()->maxSize(2048)->directory('examples/avatars'),
                FileUpload::make('gallery')->image()->multiple()->reorderable()->maxFiles(6)->maxSize(2048)->directory('examples/gallery'),
            ],
            'rich-editor' => fn (): array => [RichEditor::make('content')->minHeight('16rem')->default('<p>Try <strong>bold</strong>, lists and links.</p>')],
            'markdown-editor' => fn (): array => [MarkdownEditor::make('body')->minHeight('12rem')->default("## Hello\n\nTry the **preview** button.")],
            'code-editor' => fn (): array => [CodeEditor::make('snippet')->language(Language::Php)->minHeight('14rem')->wrap()->default("<?php\n\necho 'Hello';\n")],
            'color-picker' => fn (): array => [ColorPicker::make('brand_color')->hex()->default('#0071e3')],
            'slider' => fn (): array => [Slider::make('discount')->range(0, 100)->step(5)->tooltips()->default(25)],
            'tags-input' => fn (): array => [TagsInput::make('tags')->suggestions(['laravel', 'vue', 'inertia'])->default(['laravel'])],
            'key-value' => fn (): array => [
                KeyValue::make('specs')->keyLabel('Spec')->valueLabel('Detail')->addActionLabel('Add spec')->default(['Weight' => '240 g', 'Warranty' => '2 years']),
            ],
            'modal-table-select' => fn (): array => [
                ModalTableSelect::make('category_id')
                    ->relationship('category', 'name')
                    ->tableConfiguration(CategoriesTable::class),
            ],
            'morph-to-select' => fn (): array => [
                MorphToSelect::make('commentable')->searchable()->types([
                    Type::make(Post::class)->titleAttribute('title'),
                    Type::make(Product::class)->titleAttribute('name'),
                ]),
            ],
            'custom-field' => fn (): array => [CustomField::make('rating')->component('app/rating')->default(3)],
            'layouts' => fn (): array => [
                Section::make('Contact')->columns(2)->schema([
                    TextInput::make('name'),
                    TextInput::make('email'),
                ]),
            ],
            'grid' => fn (): array => [
                Grid::make(3)->schema([
                    TextInput::make('first_name'),
                    TextInput::make('middle_name'),
                    TextInput::make('last_name'),
                ]),
                Flex::make([
                    TextInput::make('title'),
                    Toggle::make('is_published')->grow(false),
                ])->from('md'),
                Group::make([
                    TextInput::make('company'),
                    TextInput::make('vat_number'),
                ])->columns(2),
                FusedGroup::make([
                    TextInput::make('amount')->numeric(),
                    Select::make('currency')->options(['usd' => 'USD', 'eur' => 'EUR'])->default('usd'),
                ])->columns(2),
            ],
            'sections' => fn (): array => [
                Section::make('Shipping')->description('Where the order goes.')->columns(2)->collapsible()->schema([
                    TextInput::make('street'),
                    TextInput::make('city'),
                ]),
                Fieldset::make('Billing address')->columns(2)->schema([
                    TextInput::make('billing_street'),
                    TextInput::make('billing_city'),
                ]),
            ],
            'section-extras' => fn (): array => [
                Section::make('Shipping')->columns(2)->schema([
                    TextInput::make('shipping_street'),
                    TextInput::make('shipping_city')->default('Manila'),
                ]),
                Section::make('Billing')
                    ->icon('credit-card')
                    ->afterHeader(Text::make('Optional')->badge()->color('gray'))
                    ->footer([
                        Action::make('copy_shipping')
                            ->authorize(true)
                            ->link()
                            ->label('Same as shipping')
                            ->action(fn (Get $get, Set $set) => $set('billing_city', $get('shipping_city'))),
                    ])
                    ->columns(2)
                    ->schema([
                        TextInput::make('billing_street'),
                        TextInput::make('billing_city'),
                    ]),
            ],
            'section-aside' => fn (): array => [
                Section::make('Profile')
                    ->description('Shown on your public page.')
                    ->aside()
                    ->schema([
                        TextInput::make('display_name'),
                        Textarea::make('bio')->rows(3),
                    ]),
            ],
            'tabs' => fn (): array => [
                Tabs::make('Details')->tabs([
                    Tab::make('General')->schema([TextInput::make('name')]),
                    Tab::make('Pricing')->columns(2)->schema([TextInput::make('price')->numeric(), TextInput::make('stock')->integer()]),
                    Tab::make('Notes')->schema([Textarea::make('notes')]),
                ]),
            ],
            'tabs-icons' => fn (): array => [
                Tabs::make('Details')->tabs([
                    Tab::make('General')->icon('settings')->schema([TextInput::make('name')]),
                    Tab::make('Pricing')->icon('dollar-sign')->schema([TextInput::make('price')->numeric()]),
                    Tab::make('Reviews')->icon('star')->badge(3)->badgeColor('warning')->schema([Textarea::make('reply')]),
                ]),
            ],
            'tabs-vertical' => fn (): array => [
                Tabs::make('Settings')->vertical()->contained()->tabs([
                    Tab::make('Profile')->schema([TextInput::make('name'), TextInput::make('email')->email()]),
                    Tab::make('Notifications')->schema([Toggle::make('email_updates'), Toggle::make('weekly_digest')]),
                    Tab::make('Security')->schema([TextInput::make('password')->password()]),
                ]),
            ],
            'callouts' => fn (): array => [
                Callout::make('Your plan ends soon')->description('Renew it to keep your data.')->warning(),
                Callout::make('Everything is saved')->success(),
                EmptyState::make('No items yet')->description('Add one to get started.')->icon('inbox'),
            ],
            'primes' => fn (): array => [
                Text::make('Prices include tax.')->color('gray')->size('sm')->weight('medium'),
                Text::make('Beta')->badge()->icon('star')->color('warning'),
                Icon::make('info')->color('info')->size('lg')->tooltip('More details'),
                Image::make('/favicon.svg', 'The Xelqun mark')->imageWidth(64),
                UnorderedList::make([
                    Text::make('Twelve characters or more'),
                    Text::make('One number'),
                ])->size('sm'),
            ],
        ];
    }
}
