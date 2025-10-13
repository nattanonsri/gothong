// daterangepicker.th.js
moment.updateLocale('th', {
    months : [
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    ],
    monthsShort : [
        "ม.ค.",
        "ก.พ.",
        "มี.ค.",
        "เม.ย.",
        "พ.ค.",
        "มิ.ย.",
        "ก.ค.",
        "ส.ค.",
        "ก.ย.",
        "ต.ค.",
        "พ.ย.",
        "ธ.ค."
    ],
    weekdays : [
        "วันอาทิตย์",
        "วันจันทร์",
        "วันอังคาร",
        "วันพุธ",
        "วันพฤหัสบดี",
        "วันศุกร์",
        "วันเสาร์"
    ],
    weekdaysShort : [
        "อา.",
        "จ.",
        "อ.",
        "พ.",
        "พฤ.",
        "ศ.",
        "ส."
    ],
    weekdaysMin : [
        "อา.",
        "จ.",
        "อ.",
        "พ.",
        "พฤ.",
        "ศ.",
        "ส."
    ],
    weekStart : 0,
    yearStart: 543,
    ordinal : function (number) {
        return '';
    },
    formats: {
        LT: 'HH:mm',
        LTS: 'HH:mm:ss',
        L: 'YYYY/MM/DD',
        LL: 'D MMMM YYYY',
        LLL: 'D MMMM YYYY HH:mm',
        LLLL: 'วันddddที่ D MMMM YYYY HH:mm',
        l: 'YYYY/M/D',
        ll: 'D MMM YYYY',
        lll: 'D MMM YYYY HH:mm',
        llll: 'วันdddที่ D MMM YYYY HH:mm'
    },
    meridiemParse: /ก่อนเที่ยง|หลังเที่ยง/,
    isPM: function (input) {
        return input === 'หลังเที่ยง';
    },
    meridiem : function (hour, minute, isLower) {
        if (hour < 12) {
            return 'ก่อนเที่ยง';
        } else {
            return 'หลังเที่ยง';
        }
    },
    firstDay : 1,
    relativeTime : {
        future : 'อีก %s',
        past : '%s ที่แล้ว',
        s : 'ไม่กี่วินาที',
        ss : '%d วินาที',
        m : '1 นาที',
        mm : '%d นาที',
        h : '1 ชั่วโมง',
        hh : '%d ชั่วโมง',
        d : '1 วัน',
        dd : '%d วัน',
        M : '1 เดือน',
        MM : '%d เดือน',
        y : '1 ปี',
        yy : '%d ปี'
    }
});
