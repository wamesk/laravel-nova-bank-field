const BigNumber = require('bignumber.js');

function extractIBAN(iban) {
    iban = _formatIBAN(iban)

    if (!validateIBAN(iban)) return { 'valid': false }

    let options = _getOptionsByIBAN(iban)

    let data = _matchIban(iban, options.iban_regex)
    if (data !== false) {
        data['valid'] = true
    } else {
        return { 'valid': false }
    }

    let bank = _getBankByCode(data.bank_code, options.bank)
    if (bank !== false) {
        data['name'] = bank.name
        data['bic'] = _formatBIC(bank.bic)
    }

    return data
}

function extractAccountNumberAndBIC(account_number, bic) {
    bic = _formatBIC(bic)

    let country = _getCountryFromBIC(bic)
    let options = _getOptionsByCountry(country)
    let bank = _getBankByBic(bic, options.bank)

    let number = bank.code + account_number.padStart(options.account_prefix + options.account_number, 0)

    let check_digits = _getCheckDigits(number + country + '00')
    let iban = country + check_digits + number

    let data = _matchIban(iban, options.iban_regex)
    if (data !== false) {
        data['valid'] = true
    } else {
        return { 'valid': false }
    }

    data['name'] = bank.name
    data['bic'] = bic

    return data
}

function validateIBAN(iban) {
    iban = _formatIBAN(iban)

    let ibrev = iban.substr(4) + iban.substr(0, 4)

    return _mod97(_replaceChars(ibrev)) == 1
}

function _matchIban(iban, regex) {
    let matches = iban.match(regex)
    if (!matches) return false

    return {
        'bank_code': matches[3],
        'bban': matches[5],
        'bic': null,
        'branch_code': matches[4],
        'check_digits': matches[2],
        'country_code': matches[1],
        'iban': matches[0],
        'name': null
    }
}

function _getBankByCode(bank_code, banks) {
    let bank = banks.find((bank) => bank.code === bank_code)

    return bank == undefined ? false : bank
}

function _getBankByBic(bic, banks) {
    let bank = banks.find((bank) => bic.startsWith(bank.bic))

    return bank == undefined ? false : bank
}

function _formatBIC(bic) {
    return bic.replaceAll(' ', '').toUpperCase().concat('XXXXXXXXXXX').substring(0, 11)
}

function _getCountryFromIBAN(iban) {
    return iban.substring(0,2)
}

function _getCountryFromBIC(bic) {
    return bic.substring(4,6)
}

function _getOptionsByIBAN(iban) {
    return _getOptionsByCountry(_getCountryFromIBAN(iban))
}

function _getOptionsByCountry(country) {
    return require('./country/' + country + '.json')
}

function _formatIBAN(iban) {
    return iban.replaceAll(' ', '').toUpperCase()
}

function _getCheckDigits(number) {
    for (let i = 0; i < number.length; i++) {
        let char = number[i]

        if (!isNumeric(char)) {
            let position = _mod10(char)

            number = number.replaceAll(char, position)
        }
    }

    let div = new BigNumber(number).div(97)
    let float = (div.toFixed(5) + '').split('.')[1].slice(0,5)
    let result = parseFloat('0.' + float).toFixed(5) * 97

    return 98 - Math.round(result)
}

function isNumeric(value) {
    return /^-?\d+$/.test(value)
}

function _mod10(char) {
    let alphabet = "ABCDEFGHIJKLMNOPQRSTUVXYZ"
    let check = alphabet.indexOf(char)

    return check !== -1 ? check + 10 : false
}

function _mod97(str) {
    let res = 0

    for (let i = 0; i < str.length; i++) {
        res = (res * 10 + parseInt(str[i], 10)) % 97
    }

    return res;
}

function _replaceChars(str) {
    let res = ''

    for (let i = 0; i < str.length; i++) {
        let cc = str.charCodeAt(i)

        if (cc >= 65 && cc <= 90) { res += (cc - 55).toString() }
        else if (cc >= 97 && cc <= 122) { res += (cc - 87).toString() }
        else if (cc >= 48 && cc <= 57) { res += str[i] }
    }

    return res;
}

module.exports = {
    extractIBAN: extractIBAN,
    extractAccountNumberAndBIC: extractAccountNumberAndBIC,
    validateIBAN: validateIBAN
}
