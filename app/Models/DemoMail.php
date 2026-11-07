<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * An email the demo caught instead of sending.
 */
#[Fillable(['inbox', 'recipient', 'subject', 'html'])]
class DemoMail extends Model {}
