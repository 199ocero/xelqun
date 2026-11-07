<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Post;
use App\Xelqun\Admin\Infolists\RatingEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Xelqun\Forms\Components\Builder as BlockBuilder;
use Xelqun\Forms\Components\Builder\Block;
use Xelqun\Forms\Components\Checkbox;
use Xelqun\Forms\Components\CheckboxList;
use Xelqun\Forms\Components\CodeEditor;
use Xelqun\Forms\Components\CodeEditor\Language;
use Xelqun\Forms\Components\ColorPicker;
use Xelqun\Forms\Components\CustomField;
use Xelqun\Forms\Components\DateTimePicker;
use Xelqun\Forms\Components\FileUpload;
use Xelqun\Forms\Components\Hidden;
use Xelqun\Forms\Components\KeyValue;
use Xelqun\Forms\Components\MarkdownEditor;
use Xelqun\Forms\Components\Radio;
use Xelqun\Forms\Components\RichEditor;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Components\ToggleButtons;
use Xelqun\Forms\Conditions\When;
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
use Xelqun\Forms\State\Set;
use Xelqun\Panels\Infolists\CodeEntry;
use Xelqun\Panels\Infolists\ColorEntry;
use Xelqun\Panels\Infolists\KeyValueEntry;
use Xelqun\Panels\Infolists\RepeatableEntry;
use Xelqun\Panels\Infolists\TextEntry;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Panels\Resources\Tab as ListTab;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Table;

/**
 * The richest form in the demo: every editor, the block builder, and most layouts.
 */
class PostResource extends Resource
{
    protected static string $model = Post::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'newspaper';

    protected static ?string $navigationGroup = 'Blog';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('author');
    }

    public static function getNavigationBadge(): int
    {
        return Post::query()->where('status', 'review')->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug'];
    }

    public static function getTabs(): array
    {
        $byStatus = fn (string $status): ListTab => ListTab::make()
            ->badge(fn (): int => Post::query()->where('status', $status)->count())
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status));

        return ['all' => ListTab::make(), 'published' => $byStatus('published'), 'review' => $byStatus('review'), 'draft' => $byStatus('draft')];
    }

    public static function form(): array
    {
        return [
            Tabs::make('Post')->persistTabInQueryString()->columnSpanFull()->tabs([
                Tab::make('Content')->schema([
                    Callout::make('Every tab here is one PHP class')
                        ->description('Editors load only when a form needs them, and the server cleans the HTML before it saves.')
                        ->info(),
                    Grid::make(2)->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                    ]),
                    Select::make('author_id')
                        ->label('Author')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->preload()
                        ->manageOptionForm([
                            TextInput::make('name')->required(),
                            TextInput::make('email')->email()->required(),
                        ]),
                    MarkdownEditor::make('excerpt')->minHeight('8rem'),
                    RichEditor::make('body')->minHeight('14rem'),
                ]),
                Tab::make('Blocks')->schema([
                    BlockBuilder::make('blocks')
                        ->collapsible()
                        ->cloneable()
                        ->blocks([
                            'Text' => [
                                Block::make('heading')->icon('hash')->maxItems(1)->schema([TextInput::make('text')->required()]),
                                Block::make('paragraph')->icon('file')->schema([Textarea::make('text')->required()]),
                            ],
                            'Media' => [
                                Block::make('quote')->icon('star')->schema([
                                    Textarea::make('text')->required(),
                                    TextInput::make('author'),
                                ]),
                            ],
                        ]),
                ]),
                Tab::make('Code')->schema([
                    CodeEditor::make('snippet')->language(Language::Php)->minHeight('12rem')->wrap(),
                    Flex::make([
                        Icon::make('info')->color('info')->tooltip('Prime components are plain content.'),
                        Text::make('Text, icons, images and lists sit in a form too.')->color('gray'),
                        Text::make('Prime')->badge()->color('primary')->grow(false),
                    ])->from('sm'),
                    UnorderedList::make([
                        Text::make('Highlighting comes from CodeMirror.'),
                        Text::make('Each language loads when it is needed.'),
                    ])->size('sm'),
                    Image::make('/favicon.svg', 'The Xelqun mark')->imageWidth(48)->alignStart(),
                ]),
                Tab::make('Publishing')->columns(2)->schema([
                    ToggleButtons::make('status')
                        ->required()
                        ->default('draft')
                        ->grouped()
                        ->options(['draft' => 'Draft', 'review' => 'In review', 'published' => 'Published'])
                        ->colors(['draft' => 'gray', 'review' => 'warning', 'published' => 'success']),
                    DateTimePicker::make('published_at')
                        ->visibleWhen(When::eq('status', 'published'))
                        ->requiredWhen(When::eq('status', 'published')),
                    Radio::make('audience')
                        ->required()
                        ->default('everyone')
                        ->options(['everyone' => 'Everyone', 'members' => 'Members only']),
                    CheckboxList::make('topics')
                        ->options(['forms' => 'Forms', 'tables' => 'Tables', 'panels' => 'Panels', 'security' => 'Security', 'design' => 'Design'])
                        ->bulkToggleable()
                        ->columns(2),
                    Toggle::make('is_pinned')->label('Pin to the top'),
                    CustomField::make('rating')->label('Editor rating')->component('app/rating'),
                    Checkbox::make('notify_subscribers')->label('Email subscribers')->saved(false),
                    Hidden::make('source')->default('demo')->saved(false),
                ]),
                Tab::make('Appearance')->columns(2)->schema([
                    FileUpload::make('banner')
                        ->image()
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                        ->maxSize(2048)
                        ->disk('public')
                        ->directory('demo/posts')
                        ->columnSpanFull(),
                    Fieldset::make('Theme')->columns(1)->schema([
                        ColorPicker::make('accent')->hex(),
                    ]),
                    Group::make([
                        FusedGroup::make([
                            TextInput::make('seo_prefix')->label('Prefix')->placeholder('blog')->saved(false),
                            TextInput::make('seo_suffix')->label('Suffix')->placeholder('2026')->saved(false),
                        ])->columns(2),
                    ]),
                    KeyValue::make('meta')->keyLabel('Field')->valueLabel('Value')->columnSpanFull(),
                    EmptyState::make('No revisions yet')
                        ->description('Saved versions of this post would be listed here.')
                        ->icon('inbox')
                        ->columnSpanFull(),
                ]),
            ]),
        ];
    }

    public static function infolist(): array
    {
        return [
            Section::make('Post')->columns(2)->schema([
                TextEntry::make('title'),
                TextEntry::make('author.name')->label('Author'),
                TextEntry::make('status')->badge(),
                RatingEntry::make('rating')->label('Editor rating'),
                ColorEntry::make('accent'),
                TextEntry::make('published_at')->dateTime(),
            ]),
            Section::make('Details')->columns(2)->schema([
                KeyValueEntry::make('meta'),
                CodeEntry::make('snippet'),
            ]),
            Section::make('Comments')->schema([
                RepeatableEntry::make('comments')->label('')->schema([
                    TextEntry::make('name'),
                    TextEntry::make('body'),
                ]),
            ]),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('author.name')->label('Author')->sortable()->toggleable(),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'published' => 'success',
                'review' => 'warning',
                default => 'gray',
            }),
            TextColumn::make('topics')->badge()->toggleable(),
            TextColumn::make('published_at')->date()->sortable(),
        ];
    }

    public static function filters(): array
    {
        return [
            SelectFilter::make('author')->relationship('author', 'name')->searchable(),
            ...parent::filters(),
        ];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultSort('published_at', 'desc');
    }
}
