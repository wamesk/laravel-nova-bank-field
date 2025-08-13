<?php

declare(strict_types = 1);

namespace Wame\LaravelNovaBankField\Fields;

use Laravel\Nova\Fields\Field;

class Bank extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'bank';
}
