<?php

namespace App\Examples\Forms;

use Xelqun\Forms\Components\CodeEditor;
use Xelqun\Forms\Components\CodeEditor\Language;
use Xelqun\Forms\Components\MarkdownEditor;
use Xelqun\Forms\Components\RichEditor;
use Xelqun\Forms\Form;

/**
 * Three editors, each in its own chunk. The server cleans rich text on the way
 * in and on the way out, and renders the Markdown preview.
 */
class EditorsForm extends Form
{
    public function schema(): array
    {
        return [
            RichEditor::make('article')
                ->required()
                ->hint('Try pasting a <script>: the result panel shows what the server kept.')
                ->default('<h2>Hello</h2><p>This is <strong>rich</strong> text.</p>'),

            MarkdownEditor::make('notes')->default("## Notes\n\nThe **preview** comes from the server."),

            CodeEditor::make('snippet')
                ->language(Language::Php)
                ->default("<?php\n\necho 'Hello';\n"),
        ];
    }
}
