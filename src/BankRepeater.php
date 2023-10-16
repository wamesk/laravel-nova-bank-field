<?php

namespace Wame\Bank;

use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wame\Bank\Bank;

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
