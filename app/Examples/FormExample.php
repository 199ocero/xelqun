<?php

namespace App\Examples;

use App\Examples\Forms\ActionsForm;
use App\Examples\Forms\AllFieldsForm;
use App\Examples\Forms\BasicFieldsForm;
use App\Examples\Forms\ConditionalFieldsForm;
use App\Examples\Forms\DependentFieldsForm;
use App\Examples\Forms\EditorsForm;
use App\Examples\Forms\FileUploadsForm;
use App\Examples\Forms\LayoutDisplayForm;
use App\Examples\Forms\NotificationsActions;
use App\Examples\Forms\PageEditorForm;
use App\Examples\Forms\RepeatersForm;
use App\Examples\Forms\RepeaterTotalsForm;
use App\Examples\Forms\StandaloneActions;
use App\Examples\Forms\ValidationErrorsForm;
use App\Examples\Forms\ViewOnlyForm;
use App\Examples\Forms\WizardForm;
use ReflectionClass;
use Xelqun\Forms\Form;

/**
 * The live form examples on the docs pages.
 */
enum FormExample: string
{
    case BasicFields = 'basic-fields';
    case AllFields = 'all-fields';
    case ConditionalFields = 'conditional-fields';
    case DependentFields = 'dependent-fields';
    case RepeaterTotals = 'repeater-totals';
    case Repeaters = 'repeaters';
    case FileUploads = 'file-uploads';
    case LayoutDisplay = 'layout-display';
    case Wizard = 'wizard';
    case Actions = 'actions';
    case StandaloneActions = 'standalone-actions';
    case Notifications = 'notifications';
    case ViewOnly = 'view-only';
    case PageEditor = 'page-editor';
    case Editors = 'editors';
    case ValidationErrors = 'validation-errors';

    public function title(): string
    {
        return match ($this) {
            self::BasicFields => 'Basic fields',
            self::AllFields => 'All fields',
            self::ConditionalFields => 'Conditional fields',
            self::DependentFields => 'Live dependent fields',
            self::RepeaterTotals => 'Repeater with totals',
            self::Repeaters => 'Repeaters',
            self::FileUploads => 'File uploads',
            self::LayoutDisplay => 'Layout and display',
            self::Wizard => 'Wizard',
            self::Actions => 'Actions',
            self::StandaloneActions => 'Standalone actions',
            self::Notifications => 'Notifications',
            self::ViewOnly => 'View only',
            self::PageEditor => 'Page editor',
            self::Editors => 'Editors',
            self::ValidationErrors => 'Validation errors',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BasicFields => 'Every field type and layout in the MVP, rendered from a PHP class with no Vue written for it.',
            self::AllFields => 'The everyday fields: dates and times, lists, tags, sliders, colors and key-value rows, plus the extras on selects and text inputs. Every limit here is checked on the server too.',
            self::ConditionalFields => 'visibleWhen, requiredWhen and disabledWhen rules run in the browser with no server call, then the server re-checks them and drops hidden values on submit.',
            self::DependentFields => 'Server closures load options and set values. The browser only calls the server when a value a closure read actually changes. Watch the Network tab.',
            self::RepeaterTotals => 'Rows keyed by stable UUIDs, with qty × price subtotals and totals calculated in the browser and recalculated on the server.',
            self::Repeaters => 'The same field in other shapes: a flat list, a table, a grid of cards you can copy, collapse and drag, and a repeater inside a repeater. Submit it to see distinct() catch the duplicates in the table.',
            self::FileUploads => 'Files upload as soon as they are picked. State only holds a short token until the form is saved.',
            self::LayoutDisplay => 'Flex, fused fields, callouts, empty states, plain text, icons, images and lists, plus extra content around a field. Add ?locale=es to see the browser text in Spanish, and leave with changes to see the warning.',
            self::Wizard => 'Next checks the step in the browser, then on the server. The server keeps no record of the steps, so Submit checks all of them again. The email checks itself when you leave the field. Watch the Network tab.',
            self::Actions => 'Buttons that run PHP. A row action opens a modal with its own fields, and its result lands in that row. Watch the Network tab: each click rebuilds the form on the server and checks the action again.',
            self::StandaloneActions => 'Buttons on a page, with no form or table around them. Each click rebuilds the class on the server and checks the action again, then the page reloads to show what changed. The menu holds more actions, and each one reports back with a notification.',
            self::Notifications => 'Each button runs PHP that sends a notification. Only five show at a time, and hovering pauses their timers.',
            self::ViewOnly => 'A form nobody can change, as on a View page. The lock travels in the token, so a tampered request is refused on the server too. Actions in the form still work.',
            self::PageEditor => 'A page built from blocks. Each block has its own fields and rules, and the rows land in one JSON column as {type, data}. A heading can only be used once, and the quote shows a preview rendered by Blade.',
            self::Editors => 'RichEditor, MarkdownEditor and CodeEditor. Each loads only when a form needs it. The server cleans rich text and renders the Markdown preview.',
            self::ValidationErrors => 'This form starts with invalid data. Submit it to see Laravel errors on top-level fields, inside tabs and on nested repeater rows, with custom messages and rules that compare fields.',
        };
    }

    /**
     * Short labels for the features an example demonstrates.
     *
     * @return array<int, string>
     */
    public function highlights(): array
    {
        return match ($this) {
            self::BasicFields => ['TextInput', 'Textarea', 'Select', 'Checkbox', 'Toggle', 'Radio', 'DatePicker', 'Hidden', 'Section', 'Grid', 'Fieldset', 'Tabs'],
            self::AllFields => ['DateTimePicker', 'TimePicker', 'CheckboxList', 'ToggleButtons', 'TagsInput', 'Slider', 'KeyValue', 'ColorPicker', 'option groups', 'disableOptionWhen', 'mask', 'revealable', 'copyable'],
            self::ConditionalFields => ['visibleWhen', 'requiredWhen', 'disabledWhen', 'hiddenWhen', 'When::and', 'When::in', 'When::gte'],
            self::DependentFields => ['->options(fn (Get $get))', 'afterStateUpdated', 'live(onBlur: true)', 'dependency tracking'],
            self::RepeaterTotals => ['Repeater', 'computed()', 'sum()', 'count()', 'round()'],
            self::Repeaters => ['simple()', 'table()', 'grid()', 'cloneable()', 'collapsible()', 'distinct()', 'requiresDeleteConfirmation()', 'drag and drop', 'nested rows'],
            self::FileUploads => ['FileUpload', 'temporary uploads', 'uploads in rows', 'multiple()', 'avatar()', 'reorderable()', 'imageEditor()', 'automaticallyResizeImagesToWidth()'],
            self::LayoutDisplay => ['Flex', 'FusedGroup', 'Callout', 'EmptyState', 'Text', 'Icon', 'Image', 'UnorderedList', 'field slots', 'unsavedChangesAlerts', 'translations'],
            self::Wizard => ['Wizard', 'Step', 'afterValidation', 'persistStepInQueryString', 'frontendValidation', 'liveValidation'],
            self::Actions => ['Action', 'Actions', 'schema()', 'fillForm()', 'requiresConfirmation()', 'authorize()', 'row actions', 'nested forms', 'extraItemActions', 'prefixAction', 'suffixAction'],
            self::StandaloneActions => ['ActionSet', 'XqActions', 'requiresConfirmation()', 'schema()', 'authorize()', 'url()', 'page reload', 'modalIcon', 'modalIconColor', 'modalAlignment', 'modalFooterActionsAlignment', 'modalCloseButton', 'closeModalByClickingAway', 'successRedirectUrl', 'ActionGroup', 'Notification', 'successNotificationTitle'],
            self::Notifications => ['Notification', 'success()', 'info()', 'warning()', 'danger()', 'persistent()', 'body()', 'successNotificationTitle', 'NotificationAction'],
            self::ViewOnly => ['Form::disabled()', 'locked in the token', 'save() refuses'],
            self::PageEditor => ['Builder', 'Block', 'grouped blocks', 'blockPreviews', 'maxItems'],
            self::Editors => ['RichEditor', 'MarkdownEditor', 'CodeEditor', 'HTML sanitizing', 'lazy chunks'],
            self::ValidationErrors => ['nested errors', 'error bags', 'closure rules', 'tab error badges', 'validationMessages', 'validationAttribute', 'alphaDash', 'confirmed', 'after'],
        };
    }

    /**
     * @return class-string<Form>
     */
    public function formClass(): string
    {
        return match ($this) {
            self::BasicFields => BasicFieldsForm::class,
            self::AllFields => AllFieldsForm::class,
            self::ConditionalFields => ConditionalFieldsForm::class,
            self::DependentFields => DependentFieldsForm::class,
            self::RepeaterTotals => RepeaterTotalsForm::class,
            self::Repeaters => RepeatersForm::class,
            self::FileUploads => FileUploadsForm::class,
            self::LayoutDisplay => LayoutDisplayForm::class,
            self::Wizard => WizardForm::class,
            self::Actions => ActionsForm::class,
            self::StandaloneActions => StandaloneActions::class,
            self::Notifications => NotificationsActions::class,
            self::ViewOnly => ViewOnlyForm::class,
            self::PageEditor => PageEditorForm::class,
            self::Editors => EditorsForm::class,
            self::ValidationErrors => ValidationErrorsForm::class,
        };
    }

    public function makeForm(): Form
    {
        return $this->formClass()::make();
    }

    /**
     * The example's PHP source, shown in the "PHP" panel.
     */
    public function source(): string
    {
        return (string) file_get_contents((string) (new ReflectionClass($this->formClass()))->getFileName());
    }

    public function sourcePath(): string
    {
        return str_replace(base_path().'/', '', (string) (new ReflectionClass($this->formClass()))->getFileName());
    }

    /**
     * @return array<int, array{slug: string, title: string}>
     */
    public static function navigation(): array
    {
        return array_map(
            fn (self $example): array => ['slug' => $example->value, 'title' => $example->title()],
            self::cases(),
        );
    }
}
