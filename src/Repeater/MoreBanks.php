<?php

namespace Wame\Bank\Repeater;

use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wame\Bank\Bank;

class MoreBanks extends Repeatable
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
