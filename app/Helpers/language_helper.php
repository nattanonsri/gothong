<?php

use CodeIgniter\Config\Services;
use CodeIgniter\I18n\Time;

/* =========================
 *  ภาษาปัจจุบัน (อ่านจาก set_language/get_language ที่คุณมี)
 * ========================= */
if (! function_exists('_util_current_lang')) {
    function _util_current_lang(): string
    {
        // ใช้ get_language() ของคุณถ้ามี
        if (function_exists('get_language')) {
            $lang = get_language();
        } else {
            $req  = Services::request();
            $lang = $req->getLocale() ?: (config('App')->defaultLocale ?? 'en');
        }
        $lang = strtolower(substr((string) $lang, 0, 2));
        return in_array($lang, ['th','en'], true) ? $lang : 'en';
    }
}

/* =========================
 *  ตัวช่วยทั่วไป
 * ========================= */
if (! function_exists('_util_normalize_datetime')) {
    function _util_normalize_datetime($input, ?string $tz = 'Asia/Bangkok'): \DateTimeImmutable
    {
        $zone = new \DateTimeZone($tz ?? date_default_timezone_get());

        if ($input instanceof \DateTimeInterface) {
            $dt = new \DateTimeImmutable($input->format('Y-m-d H:i:s'), $input->getTimezone());
            return $dt->setTimezone($zone);
        }
        if (is_numeric($input)) {
            return (new \DateTimeImmutable('@' . $input))->setTimezone($zone);
        }
        return new \DateTimeImmutable((string) ($input ?: 'now'), $zone);
    }
}

if (! function_exists('_util_ensure_arabic_digits')) {
    // บังคับให้เป็นเลขอาราบิก 0-9 เสมอ (กันกรณี locale ส่งเลขไทย/เลขอารบิก-อินเดียมา)
    function _util_ensure_arabic_digits(string $s): string
    {
        $thai      = ['๐','๑','๒','๓','๔','๕','๖','๗','๘','๙'];
        $arabIndic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $arabic    = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($arabIndic, $arabic, str_replace($thai, $arabic, $s));
    }
}

if (! function_exists('_util_resolve_pattern')) {
    // รับ preset หรือ pattern โดยตรง
    function _util_resolve_pattern(string $presetOrPattern): string
    {
        $map = [
            'date_short'     => 'd/M/yy',
            'date_medium'    => 'd MMM y',
            'date_long'      => 'd MMMM y',
            'date_full'      => 'EEEE d MMMM y',
            'datetime_short' => 'd/M/yy HH:mm' . ' ' . 'น.',
            'datetime_medium'=> 'd MMM y HH:mm' . ' ' . 'น.',
            'datetime_long'  => 'd MMMM y HH:mm' . ' ' . 'น.',
            'datetime_full'  => 'EEEE d MMMM y HH:mm:ss' . ' ' . 'น.',
            // ใส่เพิ่มได้ตามต้องการ
        ];

        if (isset($map[$presetOrPattern])) {
            return $map[$presetOrPattern];
        }

        // ถ้าผู้ใช้ส่ง pattern ICU มาเอง (มี d/y/M อะไรพวกนี้) ก็ใช้ตามนั้น
        if (preg_match('~[dyMHEmsS]~', $presetOrPattern)) {
            return $presetOrPattern;
        }

        // ค่า default
        return $map['date_medium'];
    }
}

/* =========================
 *  ฟอร์แมต "วันที่/เวลา" i18n_date()
 *  - ผูกกับภาษาปัจจุบันจาก set_language() อัตโนมัติ
 *  - era: 'auto' (ไทย=พ.ศ., อังกฤษ=ค.ศ.), หรือ 'be'/'ce' บังคับได้
 *  - preset ใช้คำง่าย ๆ เช่น date_medium, datetime_long ฯลฯ
 *  - ตัวเลขเป็นอาราบิกเสมอ
 * ========================= */
if (! function_exists('i18n_date')) {
    function i18n_date(
        $datetime,
        string $presetOrPattern = 'date_medium',
        string $era = 'auto',
        ?string $timezone = 'Asia/Bangkok'
    ): string {
        $dt     = _util_normalize_datetime($datetime, $timezone);
        $locale = _util_current_lang();                // 'th' หรือ 'en'
        $base   = $locale === 'th' ? 'th-TH' : 'en-US';
        $pattern= _util_resolve_pattern($presetOrPattern);

        if ($era === 'auto') {
            $era = ($locale === 'th') ? 'be' : 'ce';
        }

        // ใช้ ICU ถ้ามี
        if (class_exists(\IntlDateFormatter::class)) {
            // -nu-latn = บังคับเลขอาราบิก
            // -ca-buddhist / -ca-gregory = เลือกศักราช
            $loc = $base . ($era === 'be' ? '-u-ca-buddhist-nu-latn' : '-u-ca-gregory-nu-latn');
            $calendar = ($era === 'be') ? \IntlDateFormatter::TRADITIONAL : \IntlDateFormatter::GREGORIAN;

            $fmt = new \IntlDateFormatter(
                $loc,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::NONE,
                $timezone,
                $calendar,
                $pattern
            );
            $out = $fmt->format($dt);
            return _util_ensure_arabic_digits($out ?: '');
        }

        // Fallback แบบง่าย (ไม่มี ICU)
        return _util_fallback_date($dt, $pattern, $locale, $era);
    }
}

/* ฟังก์ชันลัด: ระบุภาษาชัดเจน (มีประโยชน์เวลาอยาก override ภาษาปัจจุบัน) */
if (! function_exists('th_date')) {
    function th_date($datetime, string $presetOrPattern = 'date_medium', string $era = 'be', ?string $timezone = 'Asia/Bangkok'): string
    {
        // บังคับ locale เป็นไทย: ทำผ่านการเรียก i18n_date ด้วยการสลับภาษาแบบชั่วคราว
        $original = _util_current_lang();
        $session  = Services::session();
        try { $session->set('language', 'th'); } catch (\Throwable $e) {}
        $out = i18n_date($datetime, $presetOrPattern, $era, $timezone);
        try { $session->set('language', $original); } catch (\Throwable $e) {}
        return $out;
    }
}

if (! function_exists('en_date')) {
    function en_date($datetime, string $presetOrPattern = 'date_medium', string $era = 'ce', ?string $timezone = 'Asia/Bangkok'): string
    {
        $original = _util_current_lang();
        $session  = Services::session();
        try { $session->set('language', 'en'); } catch (\Throwable $e) {}
        $out = i18n_date($datetime, $presetOrPattern, $era, $timezone);
        try { $session->set('language', $original); } catch (\Throwable $e) {}
        return $out;
    }
}

/* =========================
 *  ฟอร์แมต "ตัวเลข/เงิน" ให้เลขอาราบิกเสมอ
 * ========================= */
if (! function_exists('i18n_number')) {
    /**
     * $style: 'decimal' หรือ 'currency'
     */
    function i18n_number(
        $number,
        int $decimals = 0,
        string $style = 'decimal',
        ?string $currency = 'THB',
        ?string $locale = null
    ): string {
        $locale = $locale ?: _util_current_lang();      // 'th' | 'en'
        $base   = $locale === 'th' ? 'th-TH' : 'en-US';

        if (class_exists(\NumberFormatter::class)) {
            $fmt = new \NumberFormatter($base . '-u-nu-latn', $style === 'currency' ? \NumberFormatter::CURRENCY : \NumberFormatter::DECIMAL);
            $fmt->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
            $fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
            $out = ($style === 'currency')
                ? $fmt->formatCurrency((float)$number, $currency ?: 'THB')
                : $fmt->format((float)$number);
            return _util_ensure_arabic_digits($out ?: '');
        }

        // Fallback: PHP ปกติ
        $out = number_format((float)$number, $decimals, '.', ',');
        if ($style === 'currency') {
            $symbol = ($currency === 'THB') ? '฿' : ($currency . ' ');
            $out = $symbol . $out;
        }
        return _util_ensure_arabic_digits($out);
    }
}

/* =========================
 *  Fallback format (กรณีเครื่องไม่มี Intl)
 * ========================= */
if (! function_exists('_util_fallback_date')) {
    function _util_fallback_date(\DateTimeImmutable $dt, string $pattern, string $locale, string $era): string
    {
        // ชุดชื่อเดือน/วันแบบย่อ-เต็ม
        $maps = [
            'th' => [
                'months_short' => [1=>'ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'],
                'months_full'  => [1=>'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],
                'days_short'   => ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'],
                'days_full'    => ['วันอาทิตย์','วันจันทร์','วันอังคาร','วันพุธ','วันพฤหัสบดี','วันศุกร์','วันเสาร์'],
            ],
            'en' => [
                'months_short' => [1=>'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                'months_full'  => [1=>'January','February','March','April','May','June','July','August','September','October','November','December'],
                'days_short'   => ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
                'days_full'    => ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
            ],
        ];
        $lang = in_array($locale, ['th','en'], true) ? $locale : 'en';

        $y = (int) $dt->format('Y');
        if ($era === 'be') { $y += 543; }

        $rep = [
            'dd'   => str_pad($dt->format('j'), 2, '0', STR_PAD_LEFT),
            'd'    => (string) (int) $dt->format('j'),
            'MMMM' => $maps[$lang]['months_full'][(int) $dt->format('n')],
            'MMM'  => $maps[$lang]['months_short'][(int) $dt->format('n')],
            'MM'   => str_pad($dt->format('n'), 2, '0', STR_PAD_LEFT),
            'EEEE' => $maps[$lang]['days_full'][(int) $dt->format('w')],
            'EEE'  => $maps[$lang]['days_short'][(int) $dt->format('w')],
            'yyyy' => (string) $y,
            'y'    => (string) $y,
            'HH'   => $dt->format('H'),
            'mm'   => $dt->format('i'),
            'ss'   => $dt->format('s'),
        ];

        // ใช้ preset map ถ้ามี
        $pattern = _util_resolve_pattern($pattern);

        // แทนจาก token ยาวไปสั้น
        uksort($rep, fn($a,$b) => strlen($b) <=> strlen($a));
        $out = $pattern;
        foreach ($rep as $k => $v) {
            $out = str_replace($k, $v, $out);
        }
        return _util_ensure_arabic_digits($out);
    }
}

/* =========================
 * (ออปชัน) set_language / get_language ชุดพื้นฐาน
 * - ถ้าคุณมีของเดิมอยู่แล้ว โค้ดนี้จะถูกข้าม (ไม่ชน)
 * ========================= */
if (! function_exists('set_language')) {
    function set_language(string $lang): bool
    {
        $valid = ['en','th'];
        if (!in_array($lang, $valid, true)) return false;

        try {
            $session = Services::session();
            $session->set('language', $lang);
            Services::request()->setLocale($lang);
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Language setting error: '.$e->getMessage());
            return false;
        }
    }
}
if (! function_exists('get_language')) {
    function get_language(): string
    {
        try {
            $lang = Services::session()->get('language');
            if (!$lang) $lang = config('App')->defaultLocale ?? 'en';
            $lang = strtolower(substr($lang, 0, 2));
            return in_array($lang, ['en','th'], true) ? $lang : 'en';
        } catch (\Throwable $e) {
            log_message('error', 'Language getting error: '.$e->getMessage());
            return 'en';
        }
    }
}
if (! function_exists('current_language')) {
    function current_language(): string
    {
        return Services::request()->getLocale() ?: get_language();
    }
}
if (! function_exists('is_language')) {
    function is_language(string $lang): bool
    {
        return current_language() === strtolower(substr($lang,0,2));
    }
}
