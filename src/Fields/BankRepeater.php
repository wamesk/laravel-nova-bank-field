<?php

declare(strict_types = 1);

namespace Wame\LaravelNovaBankField\Fields;

use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wame\LaravelNovaBankField\Fields\Bank;

class BankRepeater extends Repeatable
{
    /**
     * Get the fields displayed by the repeatable.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Bank::make(__('bank.singular'), 'bank'),
        ];
    }
}
