<?php

namespace App\Examples\Snippets;

use App\Models\Comment;
use Xelqun\Forms\Form;

/**
 * One small preview on a docs page. Which one travels in the token, so all of them
 * can share this class and still work with live fields and search.
 */
class SnippetForm extends Form
{
    public function model(): ?string
    {
        return $this->context['snippet'] === 'morph-to-select' ? Comment::class : null;
    }

    public function schema(): array
    {
        return FormSnippets::all()[$this->context['snippet']]();
    }
}
