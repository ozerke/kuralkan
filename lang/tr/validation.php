<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute kabul edilmelidir.',
    'accepted_if' => ':other :value iken, :attribute kabul edilmelidir',
    'active_url' => ':attribute geçerli bir URL olmalıdır.',
    'after' => ':attribute değeri :date tarihinden sonra olmalıdır.',
    'after_or_equal' => ':attribute değeri :date tarihi ile aynı veya sonra olmalıdır.',
    'alpha' => ':attribute için sadece harf giriniz.',
    'alpha_dash' => ':attribute için sadece harfler, rakamlar, tire veya alt tire girilmelidir.',
    'alpha_num' => ':attribute için sadece harfler ve rakamlar girilmelidir.',
    'array' => ':attribute bir dizin olması gereklidir.',
    'ascii' => ':attribute sadece tek byte alfanümerik karakter veya sembol içermelidir.',
    'before' => ':attribute değeri :date tarihinden önceki bir tarih olmalıdır.',
    'before_or_equal' => ':attribute değeri :date tarihi ile aynı veya önce olmalıdır.',
    'between' => [
        'array' => ':attribute :min ile :max arasında olmalıdır.',
        'file' => ':attribute :min ile :max kilobyte arasında olmalıdır.',
        'numeric' => ':attribute :min ile :max arasında olmalıdır.',
        'string' => ':attribute :min ile :max karakter arasında olmalıdır.',
    ],
    'boolean' => ':attribute doğru veya yanlış olmalıdır.',
    'can' => ':attribute geçersiz bir değer taşıyor.',
    'confirmed' => ':attribute onayı hatalı.',
    'current_password' => 'Şifre hatalı.',
    'date' => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals' => ':attribute :date tarihine eşit olmalıdır.',
    'date_format' => ':attribute :format formatında olmalıdır.',
    'decimal' => ':attribute :decimal basamaklarında olmalıdır.',
    'declined' => ':attribute reddedilmiş olmalıdır.',
    'declined_if' => ':other :value iken, :attribute reddedilmiş olmalıdır.',
    'different' => ':attribute ile :other farklı olmalıdır.',
    'digits' => ':attribute :digits rakam olmalıdır.',
    'digits_between' => ':attribute :min ile :max rakam arasında olmalıdır.',
    'dimensions' => ':attribute geçersiz resim boyutu.',
    'distinct' => ':attribute tekrarlanan bir değer var.',
    'doesnt_end_with' => ':attribute :values değerleri ille bitemez.',
    'doesnt_start_with' => ':attribute :values değerleri ile başlayamaz.',
    'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
    'ends_with' => ':attribute :values değerlerinden biri ile bitmelidir.',
    'enum' => 'Seçili :attribute geçersizdir.',
    'exists' => 'Seçili :attribute geçersizdir.',
    'file' => ':attribute bir dosya olmalıdır.',
    'filled' => ':attribute bir değere saahip olmalıdır.',
    'gt' => [
        'array' => ':attribute :value değerinden daha büyük olmalıdır.',
        'file' => ':attribute :value kilobyte üzerinde olmalıdır.',
        'numeric' => ':attribute :value değerinden büyük olmalıdır.',
        'string' => ':attribute :value karakterden fazla olmalıdır.',
    ],
    'gte' => [
        'array' => ':attribute :value veya daha fazla değere sahip olmalıdır.',
        'file' => ':attribute :value veya daha fazla kilobyte olmalıdır.',
        'numeric' => ':attribute :value veya daha büyük olmalıdır.',
        'string' => ':attribute :value veya daha fazla karakter olmaldır.',
    ],
    'image' => ':attribute bir resim olmalı.',
    'in' => 'Seçili :attribute geçersiz.',
    'in_array' => ':attribute :other geçiyor olmalı.',
    'integer' => ':attribute tamsaayı olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':attribute geçerli bir JSON olmalıdır.',
    'lowercase' => ':attribute küçük harf olmalıdır.',
    'lt' => [
        'array' => ':attribute :value adetten az değere olmalıdır.',
        'file' => ':attribute :value daha az kilobyte olmalıdır.',
        'numeric' => ':attribute :value daha az olmalıdır.',
        'string' => ':attribute :value karakterden daha az olmalıdır.',
    ],
    'lte' => [
        'array' => ':attribute :value daha az veya eşit değere sahip olmalıdır .',
        'file' => ':attribute :value kilobyttan daha az veya eşit değere sahip olmalıdır.',
        'numeric' => ':attribute :value daha az veya eşit değere sahip olmalıdır.',
        'string' => ':attribute :value  daha az veya eşit karaktere sahip olmalıdır.',
    ],
    'mac_address' => ':attribute geçerli bir MAC adresi olmalıdır.',
    'max' => [
        'array' => ':attribute :max üzerinde değere olmamalıdır.',
        'file' => ':attribute :max kiobyte üzerinde olnamalıdır.',
        'numeric' => ':attribute :max üzerinde olmamalıdır.',
        'string' => ':attribute :max karakter üzerinde olmamalıdır.',
    ],
    'max_digits' => ':attribute :max basmak üzerinde olmamalıdır.',
    'mimes' => ':attribute :values tipinde bir dosya olmalıdır.',
    'mimetypes' => ':attribute :values tiplerinde bir dosya olmalıdır.',
    'min' => [
        'array' => ':attribute en az :min adet olmalıdır.',
        'file' => ':attribute en az :min kilobyte olmalıdır.',
        'numeric' => ':attribute en az :min olmalıdır.',
        'string' => ':attribute en az :min karakter olmalıdır.',
    ],
    'min_digits' => ':attribute en az :min basamak olmalıdır.',
    'missing' => ':attribute geçersiz olmalıdır.',
    'missing_if' => ':other :value değerinde iken :attribute geçersiz olmalıdır.',
    'missing_unless' => ':other :value değilse, :attribute geçersiz olmalıdır.',
    'missing_with' => ':values olduğunda, :attribute geçersiz olmalıdır.',
    'missing_with_all' => ':values varsa, :attribute geçersiz olmalıdır.',
    'multiple_of' => ':attribute :value katı olmalıdır.',
    'not_in' => 'Seçili :attribute geçersizdir.',
    'not_regex' => ':attribute formatı hatalıdır.',
    'numeric' => ':attribute rakam olmalıdır.',
    'password' => [
        'letters' => ':attribute en az bir harf içermelidir.',
        'mixed' => ':attribute en az bir küçük ve bir büyük harf içermelidir.',
        'numbers' => ':attribute en az bir harf içermelidir.',
        'symbols' => ':attribute en az bir sembol içermelidir.',
        'uncompromised' => ':attribute bir veri sızıntısında belirlendi. Lütfen, başka bir :attribute seçiniz.',
    ],
    'present' => ':attribute bulunması gereklidirs.',
    'present_if' => ':other :value iken :attribute bulunması gereklidir.',
    'present_unless' => ':other :value değilse :attribute bulunması gereklidir.',
    'present_with' => ':values olduğunda :attribute bulunması gereklidir.',
    'present_with_all' => ':values olduğunda :attribute bulunması gereklidir.',
    'prohibited' => ':attribute yasaklıdır.',
    'prohibited_if' => ':other :value iken :attribute yasaklıdır.',
    'prohibited_unless' => ':other :values iken :attribute yasaklıdır.',
    'prohibits' => ':attribute, :other bulunmasını engellemektedir.',
    'regex' => ':attribute formatı geçersizdir.',
    'required' => ':attribute zorunludur.',
    'required_array_keys' => ':attribute :values değerlerini içermelidir.',
    'required_if' => ':other :value iken :attribute zorunludur.',
    'required_if_accepted' => ':attribute is required when :other is accepted.',
    'required_unless' => ':other :values omadığı sürece :attribute zorunludur.',
    'required_with' => ':values olduğu sürece :attribute zorunludur.',
    'required_with_all' => ':values varsa :attribute zorunludur.',
    'required_without' => ':values bulunmadığı zaman :attribute zorunludur.',
    'required_without_all' => ':values hiç birisi yoksa :attribute zorunludur.',
    'same' => ':attribute :other ile eşit olmalıdır.',
    'size' => [
        'array' => ':attribute :size adet değer içermelidir.',
        'file' => ':attribute :size kilobyte olmalıdır.',
        'numeric' => ':attribute :size değerinde olmaıdır.',
        'string' => ':attribute :size karakter olmalıdır.',
    ],
    'starts_with' => ':attribute :values değerlerinden birisi ile başlamalıdır.',
    'string' => ':attribute bir metin olmalıdır.',
    'timezone' => ':attribute geçerli bir zaman dilimi olmalıdır.',
    'unique' => ':attribute halihazırda kayıtıdır.',
    'uploaded' => ':attribute yüklenemedi.',
    'uppercase' => ':attribute büyük harf olmalıdır.',
    'url' => ':attribute geçerli bir URL olmalıdır.',
    'ulid' => ':attribute geçerli bir ULID olmalıdır.',
    'uuid' => ':attribute geçeçrli bir UUID olmalıdır.',
    'phone' => ':attribute numerik olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'payment_amount' => [
            'in' => 'Ödeme miktarı ya "Tam" ya da "Kısmi" olmalıdır.',
            'required' => 'Ödeme miktarı alanı zorunludur.',
        ],
        'custom_amount' => [
            'required_if' => 'Özel miktar alanı, ödeme miktarı kısmi olduğunda zorunludur.',
            'numeric' => 'Özel miktar bir sayı olmalıdır.',
        ],
        'name' => [
            'required_if' => 'Ad alanı, ödeme türü kredi kartı olduğunda zorunludur.',
            'string' => 'Ad bir metin olmalıdır.',
        ],
        'number' => [
            'required_if' => 'Kredi kartı numarası alanı, ödeme türü kredi kartı olduğunda zorunludur.',
            'string' => 'Kredi kartı numarası yanlıştır.',
            'size' => 'Kredi kartı numarası 16 haneli olmalıdır.',
        ],
        'expiry' => [
            'required_if' => 'Son kullanma tarihi alanı, ödeme türü kredi kartı olduğunda zorunludur.',
            'string' => 'Son kullanma tarihi bir metin olmalıdır.',
        ],
        'cvc' => [
            'required_if' => 'CVC alanı, ödeme türü kredi kartı olduğunda zorunludur.',
            'numeric' => 'CVC bir sayı olmalıdır.',
        ],
        'payment_type' => [
            'required' => 'Ödeme türü alanı zorunludur.',
            'in' => 'Ödeme türü ya "Banka" ya da "Kredi Kartı" olmalıdır.',
        ],
        'selected_bank' => [
            'required_if' => 'Seçilen banka alanı, ödeme türü banka transferi olduğunda zorunludur.',
            'numeric' => 'Seçilen banka bir sayı olmalıdır.',
        ],
        'number_of_installments' => [
            'required_if' => 'Taksit sayısı alanı, ödeme türü kredi kartı olduğunda zorunludur.',
            'numeric' => 'Taksit sayısı bir sayı olmalıdır.',
        ],
        'notary-document-validation' => 'Bu alan zorunludur, lütfen sadece belirtilen formatlarda belge yükleyiniz.'
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
