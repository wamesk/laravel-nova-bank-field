# Laravel Nova Bank field

## Countries support and sources
[Czech](https://www.cnb.cz/en/payments/accounts-bank-codes/)
[Slovakia](https://nbs.sk/en/payments/general-information/directories-and-registers/directory-identification-codes-domestic-payment-system-in-sr/)

## Requirements

- `laravel/nova: ^4.0`


## Installation

```bash
composer require wamesk/laravel-nova-country
```

## Usage

```php
Bank::make(__('publisher.field.bank'), 'bank')
```

## Flexible content
Package is compatible with [whitecube/nova-flexible-content](https://novapackages.com/packages/whitecube/nova-flexible-content)

```php
public function fields(NovaRequest $request): array
{
    return Tabs::make(__('publisher.detail', ['name' => $this->address?->name]), [
        Tab::make(__('publisher.singular'), [
            Bank::make(__('publisher.field.bank'), 'bank')
                ->exceptOnForms()
                ->showOnPreview()
                ->showOnDetail()
                ->hideFromIndex()
        ]),

        Tab::make(__('publisher.tab.bank'), [
            Flexible::make(__('publisher.field.bank'), 'bank')
                ->help(__('publisher.field.bank.help'))
                ->button(__('publisher.field.bank.add'))
                ->addLayout(__('publisher.field.bank.title'), 'bank', [
                    Bank::make(__('publisher.field.bank.number'), 'bank')
                        ->required()
                        ->rules('required')
                ])->onlyOnForms(),
        ])
    ]);
}
```