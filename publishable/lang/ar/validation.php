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

    'accepted'             => 'الـ :attribute يجب أن تكون مقبول.',
    'active_url'           => 'الـ :attribute ليس رابط URL صالح.',
    'after'                => 'الـ :attribute يجب أن يكون تاريخ بعد :date.',
    'after_or_equal'       => 'الـ :attribute يجب أن يكون تاريخ بعد أو مساوي ل :date.',
    'alpha'                => 'الـ :attribute يمكن أن يحتوي على حروف فقط.',
    'alpha_dash'           => 'الـ :attribute يمكن أن يحتوي على حروف وأرقام وشرطات فقط.',
    'alpha_num'            => 'الـ :attribute يمكن أن يحتوي على حروف وأرقام فقط.',
    'array'                => 'الـ :attribute يجب أن يكون مصفوفة.',
    'before'               => 'الـ :attribute يجب أن يكون تاريخ قبل :date.',
    'before_or_equal'      => 'الـ :attribute يجب أن يكون تاريخ قبل أو مساوي ل :date.',
    'between'              => [
        'numeric' => 'الـ :attribute يجب أن يكون بين :min و :max.',
        'file'    => 'الـ :attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'string'  => 'الـ :attribute يجب أن يكون بين :min و :max حرف.',
        'array'   => 'الـ :attribute يجب أن يكون بين :min و :max عنصر.',
    ],
    'boolean'              => 'الحقل :attribute يجب أن يكون true أو false.',
    'confirmed'            => 'تأكيد الـ :attribute غير مطابق.',
    'date'                 => 'الـ :attribute ليس تاريخ صالح.',
    'date_format'          => 'الـ :attribute لا يطابق التنسيق :format.',
    'different'            => 'الـ :attribute و :other يجب أن يكونا مختلفين.',
    'digits'               => 'الـ :يجب أن يكون :digits رقم.',
    'digits_between'       => 'الـ :attribute يجب أن يكون بين :min و :max رقم.',
    'dimensions'           => 'الـ :attribute لديه قياسات صورة غير صالحة.',
    'distinct'             => 'الـ :attribute لديه قيمة متكررة.',
    'email'                => 'الـ :attribute يجب أن يكون بريد إلكتروني صالح.',
    'exists'               => 'الـ :attribute المختار غير صالح.',
    'file'                 => 'الـ :attribute يجب أن يكون ملف.',
    'filled'               => 'حقل الـ :attribute يجب أن يحتوي على قيمة.',
    'image'                => 'الـ :attribute يجب أن يكون صورة.',
    'in'                   => 'الـ :attribute المختار غير صالح.',
    'in_array'             => 'حقل الـ :attribute غير موجود في :other.',
    'integer'              => 'الـ :attribute يجب أن يكون رقم.',
    'ip'                   => 'الـ :attribute يجب أن يكون عنوان IP صالح IP.',
    'ipv4'                 => 'الـ :attribute يجب أن يكون عنوان IPv4 صالح.',
    'ipv6'                 => 'الـ :attribute يجب أن يكون عنوان IPv6 صالح.',
    'json'                 => 'الـ :attribute يجب أن يكون متغير JSON صالح.',
    'max'                  => [
        'numeric' => 'الـ :attribute لا يمكن أن يكون أكبر من :max.',
        'file'    => 'الـ :attribute لا يمكن أن يكون أكبر من :max كيلوبايت.',
        'string'  => 'الـ :attribute لا يمكن أن تكون أكبر من :max حرف.',
        'array'   => 'الـ :attribute لا يمكن أن تكون أكبر من :max عنصر.',
    ],
    'mimes'                => 'الـ :attribute يجب أن يكون ملف من صيغة: :values.',
    'mimetypes'            => 'الـ :attribute يجب أن يكون ملف من صيغة: :values.',
    'min'                  => [
        'numeric' => 'الـ :attribute يجب أن يكون على الأقل :min.',
        'file'    => 'الـ :attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'string'  => 'الـ :attribute يجب أن يكون على الأقل :min حرف.',
        'array'   => 'الـ :attribute يجب أن يكون على الأقل :min عنصر.',
    ],
    'not_in'               => 'الـ :attribute المحدد غير صالح.',
    'numeric'              => 'الـ :attribute يجب أن يكون رقم.',
    'present'              => 'الـ :attribute يجب أن يكون موجود.',
    'regex'                => 'الصيغة :attribute غير صالحة.',
    'required'             => 'الحقل :attribute مطلوب.',
    'required_if'          => 'الحقل :attribute مطلوب عندما يكون :other مساوي ل :value.',
    'required_unless'      => 'الحقل :attribute مطلوب إلا اذا كان :other في :values.',
    'required_with'        => 'الحقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_with_all'    => 'الحقل :attribute مطلوب عندما يكون :values موجود.',
    'required_without'     => 'الحقل :attribute عندما يكون :values غير موجود.',
    'required_without_all' => 'الحقل :attribute مطلوب عندنا لا توجود أي من :values.',
    'same'                 => 'يجب أن يتطابق الـ :attribute و :other.',
    'size'                 => [
        'numeric' => 'الـ :attribute يجب أن يكون :size.',
        'file'    => 'الـ :attribute يجب أن يكون :size كيلوبايت.',
        'string'  => 'الـ :attribute يجب أن يكون :size حرف.',
        'array'   => 'الـ :attribute يجب أن يحتوي :size عنصر.',
    ],
    'string'               => 'الـ :attribute يجب أن يكون متغير نصي.',
    'timezone'             => 'الـ :attribute يجب أن يكون zone صالح.',
    'unique'               => 'الـ :attribute مأخوذ من قبل.',
    'uploaded'             => 'الـ :attribute فشل في الرفع.',
    'url'                  => 'صيغة :attribute غير صالحة.',

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
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
