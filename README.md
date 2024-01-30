# Laravel Nova Bank field

## Countries support and sources
- [Czech](https://www.cnb.cz/en/payments/accounts-bank-codes/)
- [Slovakia](https://nbs.sk/en/payments/general-information/directories-and-registers/directory-identification-codes-domestic-payment-system-in-sr/)

## Requirements

- `laravel/nova: ^4.0`


## Installation

```bash
composer require wamesk/laravel-nova-bank-field
```

## Usage

```php
Bank::make(__('publisher.field.bank'), 'bank')
```

## Repeatable field

Package is also compatible with [Nova repeatable field](https://nova.laravel.com/docs/4.0/resources/repeater-fields.html)

```php
Repeater::make('Bank', 'bank')
    ->repeatables([\Wame\Bank\BankRepeater::make()])
    ->asJson()
```
