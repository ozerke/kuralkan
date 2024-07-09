<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\InstallmentRate;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    const BANKS = [
        'Akbank' => [
            'id' => 1,
            'logo' => 'akbank.png',
            'cc_group' => [
                'name' => 'AXESS',
                'logo' => 'axess.png',
            ],
            'vpos_bank_code' => '00046'
        ],
        'Finansbank' => [
            'id' => 2,
            'logo' => 'finansbank.png',
            'cc_group' => [
                'name' => 'CARDFINANS',
                'logo' => 'cardfinans.png',
            ],
            'vpos_bank_code' => '00111'
        ],
        'Garanti' => [
            'id' => 3,
            'logo' => 'garanti.png',
            'cc_group' => [
                'name' => 'BONUS',
                'logo' => 'bonus.png',
            ],
            'vpos_bank_code' => '00062'
        ],
        'T. Halk Bankası' => [
            'id' => 4,
            'logo' => 'halk_bankasi.png',
            'cc_group' => [
                'name' => 'PARAF',
                'logo' => 'paraf.png',
            ],
            'vpos_bank_code' => '00012'
        ],
        'T. İş Bankası' => [
            'id' => 5,
            'logo' => 'is_bankasi.png',
            'cc_group' => [
                'name' => 'MAXIMUM',
                'logo' => 'maximum.png',
            ],
            'vpos_bank_code' => '00064'
        ],
        'Türkiye Finans' => [
            'id' => 6,
            'logo' => 'turkiyefinans.png',
            'cc_group' => [
                'name' => 'TURKIYE FINANS',
                'logo' => 'turkiyefinans.png',
            ],
            'vpos_bank_code' => '00206'
        ],
        'Vakıf Bank' => [
            'id' => 7,
            'logo' => 'vakif.png',
            'cc_group' => [
                'name' => 'WORLD',
                'logo' => 'world.png',
            ],
            'vpos_bank_code' => '00015'
        ],
        'Ziraat Bankası' => [
            'id' => 8,
            'logo' => 'ziraat.png',
            'cc_group' => [
                'name' => 'BANKKART COMBO',
                'logo' => 'bankkart.png',
            ],
            'vpos_bank_code' => '00010'
        ],
        'Kuveyt Türk' => [
            'id' => 9,
            'logo' => 'kuveyt.png',
            'cc_group' => [
                'name' => 'KUVEYT TURK',
                'logo' => 'kuveyt.png',
            ],
            'vpos_bank_code' => '00205'
        ],
        'Yapı Kredi' => [
            'id' => 10,
            'logo' => 'yapikredi.png',
            'cc_group' => [
                'name' => '',
                'logo' => '',
            ],
            'vpos_bank_code' => '00067'
        ]
    ];

    const BANK_ACCOUNTS = [
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '9', 'branch_name' => 'İstanbul Anadolu Kurumsal Şubesi', 'branch_code' => '238', 'account_no' => '57226-12', 'iban' => 'TR97 0020 5000 0000 5722 6000 12', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '6', 'branch_name' => 'BOĞAZİÇİ KURUMSAL ŞUBESİ', 'branch_code' => '289', 'account_no' => '30020-15', 'iban' => 'TR46 0020 6001 9200 0300 2000 15', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '1', 'branch_name' => 'İMES TİCARİ', 'branch_code' => '0876', 'account_no' => '77021', 'iban' => 'TR37 0004 6008 7688 8000 0770 21', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '5', 'branch_name' => 'DUDULLU TİCARİ', 'branch_code' => '1381', 'account_no' => '19726', 'iban' => 'TR82 0006 4000 0011 3810 0197 26', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '4', 'branch_name' => 'İMES TİCARİ', 'branch_code' => '615', 'account_no' => '10100303', 'iban' => 'TR30 0001 2009 6150 0010 1003 03', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '8', 'branch_name' => 'TUZLA OSB GİRİŞİMCİ ŞUBESİ', 'branch_code' => '2283', 'account_no' => '2901076-5011', 'iban' => 'TR18 0001 0022 8302 9010 7650 11', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '2', 'branch_name' => 'ANADOLU TİCARİ MERKEZ', 'branch_code' => '00875', 'account_no' => '48891675', 'iban' => 'TR45 0011 1000 0000 0048 8916 75', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '7', 'branch_name' => 'IMES DUDULLU TİCARİ ŞUBESİ', 'branch_code' => '1244', 'account_no' => '158007292821038', 'iban' => 'TR72 0001 5001 5800 7292 8210 38', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '3', 'branch_name' => 'İMES TİCARİ', 'branch_code' => '1616', 'account_no' => '6296242', 'iban' => 'TR40 0006 2001 6160 0006 2962 42', 'swift_code' => ''],
        ['account_name' => 'Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.', 'country_id' => '1', 'currency_id' => '1', 'bank_id' => '10', 'branch_name' => 'GEBZE ORGANİZE SANAYİ TİCARİ ŞUBESİ', 'branch_code' => '1351', 'account_no' => '71410981', 'iban' => 'TR88 0006 7010 0000 0071 4109 81', 'swift_code' => ''],
    ];

    const INSTALLMENTS = [
        ['id' => '1', 'bank_id' => '1', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '2', 'bank_id' => '1', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '3', 'bank_id' => '1', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '4', 'bank_id' => '1', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '5', 'bank_id' => '1', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '6', 'bank_id' => '1', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '7', 'bank_id' => '1', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '8', 'bank_id' => '1', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '9', 'bank_id' => '1', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '10', 'bank_id' => '1', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '11', 'bank_id' => '1', 'number_of_months' => '12', 'rate' => '0.32'],
        ['id' => '12', 'bank_id' => '2', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '13', 'bank_id' => '2', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '14', 'bank_id' => '2', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '15', 'bank_id' => '2', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '16', 'bank_id' => '2', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '17', 'bank_id' => '2', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '18', 'bank_id' => '2', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '19', 'bank_id' => '2', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '20', 'bank_id' => '2', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '21', 'bank_id' => '2', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '22', 'bank_id' => '2', 'number_of_months' => '12', 'rate' => '0.32'],
        ['id' => '23', 'bank_id' => '3', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '24', 'bank_id' => '3', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '25', 'bank_id' => '3', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '26', 'bank_id' => '3', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '27', 'bank_id' => '3', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '28', 'bank_id' => '3', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '29', 'bank_id' => '3', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '30', 'bank_id' => '3', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '31', 'bank_id' => '3', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '32', 'bank_id' => '3', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '33', 'bank_id' => '3', 'number_of_months' => '12', 'rate' => '0.32'],
        ['id' => '34', 'bank_id' => '4', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '35', 'bank_id' => '4', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '36', 'bank_id' => '4', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '37', 'bank_id' => '4', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '38', 'bank_id' => '4', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '39', 'bank_id' => '4', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '40', 'bank_id' => '4', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '41', 'bank_id' => '4', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '42', 'bank_id' => '4', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '43', 'bank_id' => '4', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '44', 'bank_id' => '4', 'number_of_months' => '12', 'rate' => '0.32'],
        ['id' => '45', 'bank_id' => '5', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '46', 'bank_id' => '5', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '47', 'bank_id' => '5', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '48', 'bank_id' => '5', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '49', 'bank_id' => '5', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '50', 'bank_id' => '5', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '51', 'bank_id' => '5', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '52', 'bank_id' => '5', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '53', 'bank_id' => '5', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '54', 'bank_id' => '5', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '55', 'bank_id' => '5', 'number_of_months' => '12', 'rate' => '0.32'],
        ['id' => '56', 'bank_id' => '6', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '57', 'bank_id' => '6', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '58', 'bank_id' => '6', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '59', 'bank_id' => '6', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '60', 'bank_id' => '6', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '61', 'bank_id' => '6', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '62', 'bank_id' => '6', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '63', 'bank_id' => '6', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '64', 'bank_id' => '6', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '65', 'bank_id' => '6', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '66', 'bank_id' => '6', 'number_of_months' => '12', 'rate' => '0.32'],
        ['id' => '67', 'bank_id' => '7', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '68', 'bank_id' => '7', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '69', 'bank_id' => '7', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '70', 'bank_id' => '7', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '71', 'bank_id' => '7', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '72', 'bank_id' => '7', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '73', 'bank_id' => '7', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '74', 'bank_id' => '7', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '75', 'bank_id' => '7', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '76', 'bank_id' => '7', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '77', 'bank_id' => '7', 'number_of_months' => '12', 'rate' => '0.32'],
        ['id' => '78', 'bank_id' => '8', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '79', 'bank_id' => '8', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '80', 'bank_id' => '8', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '81', 'bank_id' => '8', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '82', 'bank_id' => '8', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '83', 'bank_id' => '8', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '84', 'bank_id' => '8', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '85', 'bank_id' => '8', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '86', 'bank_id' => '8', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '87', 'bank_id' => '8', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '88', 'bank_id' => '8', 'number_of_months' => '12', 'rate' => '0.32'],
        ['id' => '89', 'bank_id' => '9', 'number_of_months' => '2', 'rate' => '0.07'],
        ['id' => '90', 'bank_id' => '9', 'number_of_months' => '3', 'rate' => '0.09'],
        ['id' => '91', 'bank_id' => '9', 'number_of_months' => '4', 'rate' => '0.12'],
        ['id' => '92', 'bank_id' => '9', 'number_of_months' => '5', 'rate' => '0.14'],
        ['id' => '93', 'bank_id' => '9', 'number_of_months' => '6', 'rate' => '0.17'],
        ['id' => '94', 'bank_id' => '9', 'number_of_months' => '7', 'rate' => '0.19'],
        ['id' => '95', 'bank_id' => '9', 'number_of_months' => '8', 'rate' => '0.22'],
        ['id' => '96', 'bank_id' => '9', 'number_of_months' => '9', 'rate' => '0.25'],
        ['id' => '97', 'bank_id' => '9', 'number_of_months' => '10', 'rate' => '0.27'],
        ['id' => '98', 'bank_id' => '9', 'number_of_months' => '11', 'rate' => '0.30'],
        ['id' => '99', 'bank_id' => '9', 'number_of_months' => '12', 'rate' => '0.32']
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::BANKS as $bankName => $bankData) {
            $bank = Bank::create([
                'bank_name' => $bankName,
                'erp_bank_name' => $bankName,
                'logo' => $bankData['logo'],
                'vpos_bank_code' => $bankData['vpos_bank_code']
            ]);

            $bank->ccGroups()->create([
                'name' => $bankData['cc_group']['name'],
                'logo' => $bankData['cc_group']['logo']
            ]);
        }

        foreach (self::INSTALLMENTS as $installment) {
            InstallmentRate::create($installment);
        }

        foreach (self::BANK_ACCOUNTS as $account) {
            BankAccount::create($account);
        }
    }
}
