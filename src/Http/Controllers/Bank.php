<?php

declare(strict_types = 1);

namespace Wame\LaravelNovaBankField\Http\Controllers;

class Bank
{
    public static function extractIBAN($iban)
    {
        $iban = self::formatIBAN($iban);

        if (!self::validateIBAN($iban)) return ['valid' => false];

        $options = self::getOptionsByIBAN($iban);

        $data = self::matchIban($iban, $options['iban_regex']);
        if ($data !== false) {
            $data['valid'] = true;
        } else {
            return ['valid' => false];
        }

        $bank = self::getBankByCode($data['bank_code'], $options['bank']);
        if ($bank !== false) {
            $data['name'] = $bank['name'];
            $data['bic'] = self::formatBIC($bank['bic']);
        }

        return $data;
    }

    public static function extractAccountNumberAndBIC($account_number, $bic)
    {
        $bic = self::formatBIC($bic);

        $country = self::getCountryFromBIC($bic);
        $options = self::getOptionsByCountry($country);
        $bank = self::getBankByBic($bic, $options['bank']);

        $number = $bank['code'] . str_pad($account_number, $options['account_prefix'] + $options['account_number'], '0', STR_PAD_LEFT);

        $check_digits = self::getCheckDigits($number . $country . '00');
        $iban = $country . $check_digits . $number;

        $data = self::matchIban($iban, $options['iban_regex']);
        if ($data !== false) {
            $data['valid'] = true;
        } else {
            return ['valid' => false];
        }

        $data['name'] = $bank['name'];
        $data['bic'] = $bic;

        return $data;
    }

    public static function extractAccountNumberAndBankCode($country, $account_number, $bank_code)
    {
        $options = self::getOptionsByCountry($country);
        $bank = self::getBankByCode($bank_code, $options['bank']);
        if (!$bank) return;

        $number = $bank['code'] . str_pad($account_number, $options['account_prefix'] + $options['account_number'], '0', STR_PAD_LEFT);

        $check_digits = self::getCheckDigits($number . $country . '00');
        $iban = $country . $check_digits . $number;

        $data = self::matchIban($iban, $options['iban_regex']);
        if ($data !== false) {
            $data['valid'] = true;
        } else {
            return ['valid' => false];
        }

        $data['name'] = $bank['name'];
        $data['bic'] = $bank['bic'];

        return $data;
    }

    public static function validateIBAN($iban)
    {
        $iban = self::formatIBAN($iban);

        $ibrev = Bank . phpsubstr($iban, 4) . substr($iban, 0, 4);

        return self::mod97(self::replaceChars($ibrev)) == 1;
    }

    private static function matchIban($iban, $regex)
    {
        preg_match('/' . $regex . '/', $iban, $matches);
        if (empty($matches)) return false;

        return [
            'bank_code' => $matches[3],
            'bban' => $matches[5],
            'bic' => null,
            'branch_code' => $matches[4],
            'check_digits' => $matches[2],
            'country_code' => $matches[1],
            'iban' => $matches[0],
            'name' => null
        ];
    }

    private static function getBankByCode($bank_code, $banks)
    {
        foreach ($banks as $bank) {
            if ($bank['code'] === $bank_code) {
                return $bank;
            }
        }

        return false;
    }

    private static function getBankByBic($bic, $banks)
    {
        foreach ($banks as $bank) {
            if (strpos($bic, $bank['bic']) === 0) {
                return $bank;
            }
        }

        return false;
    }

    private static function formatBIC($bic)
    {
        return substr(str_replace(' ', '', strtoupper($bic)) . 'XXXXXXXXXXX', 0, 11);
    }

    private static function getCountryFromIBAN($iban)
    {
        return substr($iban, 0, 2);
    }

    private static function getCountryFromBIC($bic)
    {
        return substr($bic, 4, 6);
    }

    private static function getOptionsByIBAN($iban)
    {
        return self::getOptionsByCountry(self::getCountryFromIBAN($iban));
    }

    private static function getOptionsByCountry($country)
    {
        return json_decode(file_get_contents(__DIR__ . '/../../resources/js/country/' . strtoupper($country) . '.json'), true);
    }

    private static function formatIBAN($iban)
    {
        return str_replace(' ', '', strtoupper($iban));
    }

    private static function getCheckDigits($number)
    {
        for ($i = 0; $i < strlen($number); $i++) {
            $char = $number[$i];

            if (!is_numeric($char)) {
                $position = self::mod10($char);
                $number = str_replace($char, $position, $number);
            }
        }

        $div = (string) bcdiv($number, '97', 10);
        $float = substr(explode('.', $div)[1], 0, 5);
        $result = (float) ('0.' . $float) * 97;

        return 98 - round($result);
    }

    static function isNumeric($value)
    {
        return preg_match('/^-?\d+$/', $value);
    }

    private static function mod10($char)
    {
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVXYZ";
        $check = strpos($alphabet, $char);

        return $check !== false ? $check + 10 : false;
    }

    private static function mod97($str)
    {
        $res = 0;

        for ($i = 0; $i < strlen($str); $i++) {
            $res = ($res * 10 + intval($str[$i])) % 97;
        }

        return $res;
    }

    private static function replaceChars($str)
    {
        $res = '';

        for ($i = 0; $i < strlen($str); $i++) {
            $cc = ord($str[$i]);

            if ($cc >= 65 && $cc <= 90) {
                $res .= ($cc - 55);
            } else if ($cc >= 97 && $cc <= 122) {
                $res .= ($cc - 87);
            } else if ($cc >= 48 && $cc <= 57) {
                $res .= $str[$i];
            }
        }

        return $res;
    }

}
