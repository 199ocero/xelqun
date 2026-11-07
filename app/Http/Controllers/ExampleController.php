<?php

namespace App\Http\Controllers;

use App\Examples\FormExample;
use App\Examples\Snippets\FormSnippets;
use App\Examples\Snippets\SnippetActions;
use App\Examples\Snippets\SnippetForm;
use App\Examples\Snippets\SnippetTable;
use App\Examples\Snippets\TableSnippets;
use App\Examples\TableExample;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use Xelqun\Actions\ActionSet;
use Xelqun\Notifications\Notification;

/**
 * The live examples on docs pages. They load in the browser, and nothing here saves.
 */
class ExampleController extends Controller
{
    public function show(string $example): JsonResponse
    {
        $form = FormExample::tryFrom($example);

        if ($form !== null) {
            $instance = $form->makeForm();

            return $this->example($instance instanceof ActionSet ? 'actions' : 'form', $instance->toSchema(), [
                'title' => $form->title(),
                'submits' => ! $instance instanceof ActionSet,
                'source' => $this->highlight($form->source()),
            ]);
        }

        $table = TableExample::tryFrom($example);

        if ($table !== null) {
            return $this->example('table', $table->makeTable()->toSchema(), [
                'title' => $table->title(),
                'secondSchema' => $table->makeSecondTable()?->toSchema(),
                'source' => $this->highlight($table->source()),
            ]);
        }

        return $this->snippet($example);
    }

    /**
     * A preview of one feature. The code is right there on the page, so it has no Code tab.
     */
    private function snippet(string $name): JsonResponse
    {
        [$group, $snippet] = array_pad(explode('-', $name, 2), 2, '');
        $context = ['snippet' => $snippet];

        return match (true) {
            $group === 'form' && isset(FormSnippets::all()[$snippet]) => $this->example('form', SnippetForm::make(null, $context)->toSchema()),
            $group === 'table' && isset(TableSnippets::all()[$snippet]) => $this->example('table', SnippetTable::make($context)->toSchema()),
            $group === 'actions' && in_array($snippet, SnippetActions::SNIPPETS, true) => $this->example('actions', SnippetActions::make(null, $context)->toSchema()),
            default => abort(404),
        };
    }

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $details
     */
    private function example(string $kind, array $schema, array $details = []): JsonResponse
    {
        return response()->json(['kind' => $kind, 'title' => 'Preview', 'submits' => false, 'source' => null, ...$details, 'schema' => $schema]);
    }

    public function submit(Request $request, FormExample $example): RedirectResponse
    {
        $example->makeForm()->validate($request);

        Notification::make()->title('Valid. Examples never save.')->success()->send();

        return back();
    }

    private function highlight(string $source): string
    {
        return (new Phiki)->codeToHtml($source, Grammar::Php, ['light' => Theme::GithubLight, 'dark' => Theme::GithubDarkDimmed])->toString();
    }
}
