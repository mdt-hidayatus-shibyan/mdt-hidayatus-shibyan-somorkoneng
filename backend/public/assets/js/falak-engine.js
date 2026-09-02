const FALAK_NATIVE_ANDROID = /FalakAppProAndroid\//.test(navigator.userAgent);
if (FALAK_NATIVE_ANDROID)
    document.documentElement.classList.add("native-android");

const pasaran = ["Legi", "Pahing", "Pon", "Wage", "Kliwon"];
const bulanMasehi = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "Mei",
    "Jun",
    "Jul",
    "Agt",
    "Sep",
    "Okt",
    "Nov",
    "Des",
];
const bulanMasehiLengkap = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
];
const hariIndo = ["Ahad", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
const API_BASE = "https://api.aladhan.com/v1";
const PRAYER_METHOD_ID = 20;
const USE_LOCAL_PRAYER_ENGINE = true; // Lebih dekat ke Accurate Times: hitung waktu sholat lokal, bukan mengambil jadwal siap pakai dari API.
const PRAYER_FAJR_ANGLE = 20; // Ubah ke 18 bila pengaturan Accurate Times Anda memakai Fajr 18°.
const PRAYER_ISHA_ANGLE = 18;
const PRAYER_ASR_SHADOW_FACTOR = 1; // 1 = Syafi'i/Jumhur, 2 = Hanafi.
const PRAYER_IMSAK_OFFSET_MINUTES = 10;
const PRAYER_DHUHR_OFFSET_MINUTES = 0;
const PRAYER_IHTIYAT_MINUTES = 2;
const PRAYER_IHTIYAT_KEYS = new Set([
    "Fajr",
    "Dhuhr",
    "Asr",
    "Maghrib",
    "Isha",
]);
const STANDARD_SUNSET_ALTITUDE = -0.8333;
const OBSERVER_ELEVATION_METERS = 0;
const HIJRI_MONTHS = [
    null,
    { en: "Muḥarram", id: "Muharram", sheet: "01 Muharram", ar: "مُحَرَّم" },
    { en: "Ṣafar", id: "Safar", sheet: "02 Safar", ar: "صَفَر" },
    {
        en: "Rabīʿ al-awwal",
        id: "Rabiul Awal",
        sheet: "03 Rabiul Awal",
        ar: "رَبِيع ٱلْأَوَّل",
    },
    {
        en: "Rabīʿ al-thānī",
        id: "Rabiul Akhir",
        sheet: "04 Rabiul Akhir",
        ar: "رَبِيع ٱلثَّانِي",
    },
    {
        en: "Jumādá al-ūlá",
        id: "Jumadil Awal",
        sheet: "05 Jumadil Awal",
        ar: "جُمَادَىٰ ٱلْأُولَىٰ",
    },
    {
        en: "Jumādá al-ākhirah",
        id: "Jumadil Akhir",
        sheet: "06 Jumadil Akhir",
        ar: "جُمَادَىٰ ٱلْآخِرَة",
    },
    { en: "Rajab", id: "Rajab", sheet: "07 Rajab", ar: "رَجَب" },
    { en: "Shaʿbān", id: "Syaban", sheet: "08 Syaban", ar: "شَعْبَان" },
    { en: "Ramaḍān", id: "Ramadan", sheet: "09 Ramadan", ar: "رَمَضَان" },
    { en: "Shawwāl", id: "Syawal", sheet: "10 Syawal", ar: "شَوَّال" },
    {
        en: "Dhū al-Qaʿdah",
        id: "Zulkaidah",
        sheet: "11 Zulkaidah",
        ar: "ذُو ٱلْقَعْدَة",
    },
    {
        en: "Dhū al-Ḥijjah",
        id: "Zulhijah",
        sheet: "12 Zulhijah",
        ar: "ذُو ٱلْحِجَّة",
    },
];

function cleanExcelText(value) {
    return String(value ?? "")
        .normalize("NFKD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[ʿʻ‘’`´]/g, "")
        .replace(/\s+/g, " ")
        .trim();
}

function getHijriMonthLabel(monthNumber, field = "id") {
    const month = HIJRI_MONTHS[Number(monthNumber)] || {};
    return cleanExcelText(
        month[field] || month.id || month.en || `Bulan ${monthNumber}`,
    );
}

function getCalendarMode() {
    const select = document.getElementById("calendarMode");
    return select?.value === CALENDAR_MODE_GREGORIAN
        ? CALENDAR_MODE_GREGORIAN
        : CALENDAR_MODE_HIJRI;
}

function getCalendarModeLabel(mode = getCalendarMode()) {
    return mode === CALENDAR_MODE_GREGORIAN ? "Masehi" : "Hijriyah";
}

function getCalendarYearSuffix(mode = getCalendarMode()) {
    return mode === CALENDAR_MODE_GREGORIAN ? "M" : "H";
}

function updateCalendarModeUI() {
    const mode = getCalendarMode();
    const yearInput = document.getElementById("hijriYear");
    const yearLabel = document.getElementById("yearInputLabel");
    const processLabel = document.getElementById("processButtonLabel");

    if (mode === CALENDAR_MODE_GREGORIAN) {
        if (yearLabel) yearLabel.innerText = "Tahun M";
        if (processLabel) processLabel.innerText = "PROSES KALENDER MASEHI";
        if (
            yearInput &&
            (!yearInput.dataset.lastMode ||
                yearInput.dataset.lastMode !== CALENDAR_MODE_GREGORIAN)
        ) {
            const currentValue = parseInt(yearInput.value, 10);
            if (
                !Number.isInteger(currentValue) ||
                currentValue < 1700 ||
                currentValue > 2500
            ) {
                yearInput.value = new Date().getFullYear();
            }
        }
    } else {
        if (yearLabel) yearLabel.innerText = "Tahun H";
        if (processLabel) processLabel.innerText = "AMBIL LOKASI & PROSES";
        if (
            yearInput &&
            (!yearInput.dataset.lastMode ||
                yearInput.dataset.lastMode !== CALENDAR_MODE_HIJRI)
        ) {
            const currentValue = parseInt(yearInput.value, 10);
            if (
                !Number.isInteger(currentValue) ||
                currentValue < 1 ||
                currentValue > 2500
            ) {
                yearInput.value = 1448;
            }
        }
    }

    if (yearInput) yearInput.dataset.lastMode = mode;
}

const DEFAULT_MARKAZ = {
    name: "Pasuruan default",
    lat: -7.6453,
    lng: 112.9075,
};

let currentLat = DEFAULT_MARKAZ.lat;
let currentLng = DEFAULT_MARKAZ.lng;
let currentMarkazName = DEFAULT_MARKAZ.name;
let isGpsLocation = false;
let generatedMonthsData = [];
let prayerCalendarCache = new Map();
let mabimsYearBuildCache = new Map();

const CALENDAR_MODE_HIJRI = "hijri";
const CALENDAR_MODE_GREGORIAN = "gregorian";
const GREGORIAN_PRAYER_SAMPLE_DAYS = [1, 6, 11, 16, 21, 26];

const MABIMS_MIN_ALTITUDE = 3;
const MABIMS_MIN_ELONGATION = 6.4;
const MABIMS_CRITERIA_LABEL = "MABIMS 3°-6,4°";
const NATIONAL_MABIMS_POLICY = "Wilayatul Hukmi Indonesia";
const INDONESIA_MABIMS_MARKAZ = [
    { name: "Banda Aceh", lat: 5.5483, lng: 95.3238 },
    { name: "Medan", lat: 3.5952, lng: 98.6722 },
    { name: "Padang", lat: -0.9471, lng: 100.4172 },
    { name: "Pekanbaru", lat: 0.5071, lng: 101.4478 },
    { name: "Jambi", lat: -1.6101, lng: 103.6131 },
    { name: "Palembang", lat: -2.9761, lng: 104.7754 },
    { name: "Bengkulu", lat: -3.8004, lng: 102.2655 },
    { name: "Bandar Lampung", lat: -5.3971, lng: 105.2668 },
    { name: "Pangkalpinang", lat: -2.1291, lng: 106.1138 },
    { name: "Batam", lat: 1.1301, lng: 104.0529 },
    { name: "Serang", lat: -6.1201, lng: 106.1503 },
    { name: "Jakarta", lat: -6.2, lng: 106.8167 },
    { name: "Bandung", lat: -6.9175, lng: 107.6191 },
    { name: "Semarang", lat: -6.9667, lng: 110.4167 },
    { name: "Yogyakarta", lat: -7.7956, lng: 110.3695 },
    { name: "Surabaya", lat: -7.2575, lng: 112.7521 },
    { name: "Denpasar", lat: -8.6705, lng: 115.2126 },
    { name: "Mataram", lat: -8.5833, lng: 116.1167 },
    { name: "Kupang", lat: -10.1772, lng: 123.607 },
    { name: "Pontianak", lat: -0.0263, lng: 109.3425 },
    { name: "Palangkaraya", lat: -2.2096, lng: 113.9213 },
    { name: "Banjarmasin", lat: -3.3186, lng: 114.5944 },
    { name: "Samarinda", lat: -0.5022, lng: 117.1536 },
    { name: "Tanjung Selor", lat: 2.8375, lng: 117.3653 },
    { name: "Manado", lat: 1.4748, lng: 124.8421 },
    { name: "Gorontalo", lat: 0.5435, lng: 123.0568 },
    { name: "Palu", lat: -0.9003, lng: 119.878 },
    { name: "Mamuju", lat: -2.6748, lng: 118.8885 },
    { name: "Makassar", lat: -5.1477, lng: 119.4327 },
    { name: "Kendari", lat: -3.9985, lng: 122.512 },
    { name: "Ambon", lat: -3.6954, lng: 128.1814 },
    { name: "Ternate", lat: 0.7893, lng: 127.3757 },
    { name: "Sofifi", lat: 0.7373, lng: 127.5588 },
    { name: "Manokwari", lat: -0.8615, lng: 134.062 },
    { name: "Sorong", lat: -0.8762, lng: 131.2558 },
    { name: "Jayapura", lat: -2.5489, lng: 140.7197 },
    { name: "Merauke", lat: -8.4991, lng: 140.404 },
];

function toArabicNum(num) {
    const arabicNumbers = ["٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩"];
    const normalized = Number.isFinite(Number(num))
        ? String(parseInt(num, 10))
        : String(num);
    return normalized
        .split("")
        .map((d) => arabicNumbers[d] ?? d)
        .join("");
}

function toLatinNum(value) {
    const latinNumbers = {
        "٠": "0",
        "١": "1",
        "٢": "2",
        "٣": "3",
        "٤": "4",
        "٥": "5",
        "٦": "6",
        "٧": "7",
        "٨": "8",
        "٩": "9",
        "۰": "0",
        "۱": "1",
        "۲": "2",
        "۳": "3",
        "۴": "4",
        "۵": "5",
        "۶": "6",
        "۷": "7",
        "۸": "8",
        "۹": "9",
    };
    return String(value ?? "")
        .replace(/[٠-٩۰-۹]/g, (digit) => latinNumbers[digit] ?? digit)
        .replace(/\s+/g, " ")
        .trim();
}

function getGregorianMonthYearLabel(monthIndex, year) {
    const monthName =
        bulanMasehiLengkap[Number(monthIndex) - 1] ||
        bulanMasehi[Number(monthIndex) - 1] ||
        "Bulan";
    return `${monthName} ${toLatinNum(year)}`;
}

function parseDate(dateStr) {
    const [day, month, year] = String(dateStr)
        .split("-")
        .map((part) => parseInt(part, 10));
    return new Date(year, month - 1, day, 0, 0, 0, 0);
}

function formatDateDDMMYYYY(dateObj) {
    const dd = String(dateObj.getDate()).padStart(2, "0");
    const mm = String(dateObj.getMonth() + 1).padStart(2, "0");
    const yyyy = dateObj.getFullYear();
    return `${dd}-${mm}-${yyyy}`;
}

function addGregorianMonths(dateObj, count) {
    return new Date(
        dateObj.getFullYear(),
        dateObj.getMonth() + count,
        1,
        0,
        0,
        0,
        0,
    );
}

function getPasaran(dateStr) {
    const date = parseDate(dateStr);
    const utcDate = Date.UTC(
        date.getFullYear(),
        date.getMonth(),
        date.getDate(),
    );
    const utcEpoch = Date.UTC(1970, 0, 1);
    const diffDays = Math.floor((utcDate - utcEpoch) / 86400000);
    let pIndex = (diffDays + 3) % 5;
    return pasaran[pIndex < 0 ? pIndex + 5 : pIndex];
}

function normalizeDegree(deg) {
    return ((deg % 360) + 360) % 360;
}

function roundDateToNearestMinute(dateObj) {
    if (!dateObj || isNaN(dateObj.getTime())) return null;
    return new Date(Math.round(dateObj.getTime() / 60000) * 60000);
}

function inferLocationUtcOffsetHours(
    latitude = currentLat,
    longitude = currentLng,
) {
    const lat = Number(latitude);
    const lng = Number(longitude);
    // Pembagian zona waktu Indonesia. Untuk lokasi di luar Indonesia,
    // gunakan pendekatan zona waktu terdekat berdasarkan bujur.
    if (lat >= -12 && lat <= 7 && lng >= 94 && lng <= 142) {
        if (lng < 114.75) return 7;
        if (lng < 129) return 8;
        return 9;
    }
    return Math.max(-12, Math.min(14, Math.round(lng / 15)));
}

function locationCivilDateTime(
    dateObj,
    hour,
    minute = 0,
    second = 0,
    latitude = currentLat,
    longitude = currentLng,
) {
    const offset = inferLocationUtcOffsetHours(latitude, longitude);
    return new Date(
        Date.UTC(
            dateObj.getFullYear(),
            dateObj.getMonth(),
            dateObj.getDate(),
            hour,
            minute,
            second,
        ) -
            offset * 3600000,
    );
}

function formatClock(dateObj, withSeconds = false) {
    if (!dateObj || isNaN(dateObj.getTime())) return "-";
    const instant = withSeconds
        ? dateObj.getTime()
        : roundDateToNearestMinute(dateObj).getTime();
    const offset = inferLocationUtcOffsetHours();
    const displayDate = new Date(instant + offset * 3600000);
    const hh = displayDate.getUTCHours().toString().padStart(2, "0");
    const mm = displayDate.getUTCMinutes().toString().padStart(2, "0");
    const ss = displayDate.getUTCSeconds().toString().padStart(2, "0");
    return withSeconds ? `${hh}:${mm}:${ss}` : `${hh}:${mm}`;
}

function formatTimeDifference(diffMs, withSeconds = false) {
    if (diffMs === null || diffMs === undefined || isNaN(diffMs)) return "-";
    const sign = diffMs >= 0 ? "+" : "-";
    const totalSeconds = withSeconds
        ? Math.round(Math.abs(diffMs) / 1000)
        : Math.round(Math.abs(diffMs) / 60000) * 60;
    const hours = Math.floor(totalSeconds / 3600)
        .toString()
        .padStart(2, "0");
    const minutes = Math.floor((totalSeconds % 3600) / 60)
        .toString()
        .padStart(2, "0");
    const seconds = (totalSeconds % 60).toString().padStart(2, "0");
    return withSeconds
        ? `${sign}${hours}:${minutes}:${seconds}`
        : `${sign}${hours}:${minutes}`;
}

function getQiblaBearing(latitude, longitude) {
    const kaabaLat = 21.422487;
    const kaabaLng = 39.826206;
    const lat1 = (latitude * Math.PI) / 180;
    const lat2 = (kaabaLat * Math.PI) / 180;
    const deltaLng = ((kaabaLng - longitude) * Math.PI) / 180;
    const y = Math.sin(deltaLng);
    const x =
        Math.cos(lat1) * Math.tan(lat2) - Math.sin(lat1) * Math.cos(deltaLng);
    return normalizeDegree((Math.atan2(y, x) * 180) / Math.PI);
}

function toRadians(deg) {
    return (deg * Math.PI) / 180;
}

function toDegrees(rad) {
    return (rad * 180) / Math.PI;
}

function signedAngleDifference(angle, target) {
    return ((angle - target + 540) % 360) - 180;
}

function dateToJulianDay(dateObj) {
    return dateObj.getTime() / 86400000 + 2440587.5;
}

function julianDayToDateTime(jd) {
    return new Date((jd - 2440587.5) * 86400000);
}

function julianCenturies(jd) {
    return (jd - 2451545.0) / 36525;
}

function meanObliquityDegrees(T) {
    const seconds = 21.448 - T * (46.815 + T * (0.00059 - T * 0.001813));
    return 23 + (26 + seconds / 60) / 60;
}

function nutationApprox(T) {
    const L = normalizeDegree(280.4665 + 36000.7698 * T);
    const Lp = normalizeDegree(218.3165 + 481267.8813 * T);
    const omega = normalizeDegree(
        125.04452 - 1934.136261 * T + 0.0020708 * T * T + (T * T * T) / 450000,
    );
    const dPsi =
        (-17.2 * Math.sin(toRadians(omega)) -
            1.32 * Math.sin(toRadians(2 * L)) -
            0.23 * Math.sin(toRadians(2 * Lp)) +
            0.21 * Math.sin(toRadians(2 * omega))) /
        3600;
    const dEps =
        (9.2 * Math.cos(toRadians(omega)) +
            0.57 * Math.cos(toRadians(2 * L)) +
            0.1 * Math.cos(toRadians(2 * Lp)) -
            0.09 * Math.cos(toRadians(2 * omega))) /
        3600;
    return { dPsi, dEps, omega };
}

function trueObliquityDegrees(T) {
    const n = nutationApprox(T);
    return meanObliquityDegrees(T) + n.dEps;
}

function eclipticToEquatorial(lambdaDeg, betaDeg, epsilonDeg) {
    const lambda = toRadians(lambdaDeg);
    const beta = toRadians(betaDeg);
    const epsilon = toRadians(epsilonDeg);
    const ra = normalizeDegree(
        toDegrees(
            Math.atan2(
                Math.sin(lambda) * Math.cos(epsilon) -
                    Math.tan(beta) * Math.sin(epsilon),
                Math.cos(lambda),
            ),
        ),
    );
    const dec = toDegrees(
        Math.asin(
            Math.sin(beta) * Math.cos(epsilon) +
                Math.cos(beta) * Math.sin(epsilon) * Math.sin(lambda),
        ),
    );
    return { ra, dec };
}

function sunApparentGeocentric(jd) {
    const T = julianCenturies(jd);
    const L0 = normalizeDegree(280.46646 + T * (36000.76983 + T * 0.0003032));
    const M = normalizeDegree(357.52911 + T * (35999.05029 - 0.0001537 * T));
    const e = 0.016708634 - T * (0.000042037 + 0.0000001267 * T);
    const Mrad = toRadians(M);
    const C =
        Math.sin(Mrad) * (1.914602 - T * (0.004817 + 0.000014 * T)) +
        Math.sin(2 * Mrad) * (0.019993 - 0.000101 * T) +
        Math.sin(3 * Mrad) * 0.000289;
    const trueLong = L0 + C;
    const trueAnom = M + C;
    const radiusVector =
        (1.000001018 * (1 - e * e)) / (1 + e * Math.cos(toRadians(trueAnom)));
    const omega = 125.04 - 1934.136 * T;
    const lambda = normalizeDegree(
        trueLong - 0.00569 - 0.00478 * Math.sin(toRadians(omega)),
    );
    const epsilon =
        meanObliquityDegrees(T) + 0.00256 * Math.cos(toRadians(omega));
    const eq = eclipticToEquatorial(lambda, 0, epsilon);
    return {
        lambda,
        beta: 0,
        ra: eq.ra,
        dec: eq.dec,
        distanceAu: radiusVector,
        epsilon,
    };
}

const MOON_LR_TERMS = [
    [0, 0, 1, 0, 6288774, -20905355],
    [2, 0, -1, 0, 1274027, -3699111],
    [2, 0, 0, 0, 658314, -2955968],
    [0, 0, 2, 0, 213618, -569925],
    [0, 1, 0, 0, -185116, 48888],
    [0, 0, 0, 2, -114332, -3149],
    [2, 0, -2, 0, 58793, 246158],
    [2, -1, -1, 0, 57066, -152138],
    [2, 0, 1, 0, 53322, -170733],
    [2, -1, 0, 0, 45758, -204586],
    [0, 1, -1, 0, -40923, -129620],
    [1, 0, 0, 0, -34720, 108743],
    [0, 1, 1, 0, -30383, 104755],
    [2, 0, 0, -2, 15327, 10321],
    [0, 0, 1, 2, -12528, 0],
    [0, 0, 1, -2, 10980, 79661],
    [4, 0, -1, 0, 10675, -34782],
    [0, 0, 3, 0, 10034, -23210],
    [4, 0, -2, 0, 8548, -21636],
    [2, 1, -1, 0, -7888, 24208],
    [2, 1, 0, 0, -6766, 30824],
    [1, 0, -1, 0, -5163, -8379],
    [1, 1, 0, 0, 4987, -16675],
    [2, -1, 1, 0, 4036, -12831],
    [2, 0, 2, 0, 3994, -10445],
    [4, 0, 0, 0, 3861, -11650],
    [2, 0, -3, 0, 3665, 14403],
    [0, 1, -2, 0, -2689, -7003],
    [2, 0, -1, 2, -2602, 0],
    [2, -1, -2, 0, 2390, 10056],
    [1, 0, 1, 0, -2348, 6322],
    [2, -2, 0, 0, 2236, -9884],
    [0, 1, 2, 0, -2120, 5751],
    [0, 2, 0, 0, -2069, 0],
    [2, -2, -1, 0, 2048, -4950],
    [2, 0, 1, -2, -1773, 4130],
    [2, 0, 0, 2, -1595, 0],
    [4, -1, -1, 0, 1215, -3958],
    [0, 0, 2, 2, -1110, 0],
    [3, 0, -1, 0, -892, 3258],
    [2, 1, 1, 0, -810, 2616],
    [4, -1, -2, 0, 759, -1897],
    [0, 2, -1, 0, -713, -2117],
    [2, 2, -1, 0, -700, 2354],
    [2, 1, -2, 0, 691, 0],
    [2, -1, 0, -2, 596, 0],
    [4, 0, 1, 0, 549, -1423],
    [0, 0, 4, 0, 537, -1117],
    [4, -1, 0, 0, 520, -1571],
    [1, 0, -2, 0, -487, -1739],
    [2, 1, 0, -2, -399, 0],
    [0, 0, 2, -2, -381, -4421],
    [1, 1, 1, 0, 351, 0],
    [3, 0, -2, 0, -340, 0],
    [4, 0, -3, 0, 330, 0],
    [2, -1, 2, 0, 327, 0],
    [0, 2, 1, 0, -323, 1165],
    [1, 1, -1, 0, 299, 0],
    [2, 0, 3, 0, 294, 0],
    [2, 0, -1, -2, 0, 8752],
];

const MOON_B_TERMS = [
    [0, 0, 0, 1, 5128122],
    [0, 0, 1, 1, 280602],
    [0, 0, 1, -1, 277693],
    [2, 0, 0, -1, 173237],
    [2, 0, -1, 1, 55413],
    [2, 0, -1, -1, 46271],
    [2, 0, 0, 1, 32573],
    [0, 0, 2, 1, 17198],
    [2, 0, 1, -1, 9266],
    [0, 0, 2, -1, 8822],
    [2, -1, 0, -1, 8216],
    [2, 0, -2, -1, 4324],
    [2, 0, 1, 1, 4200],
    [2, 1, 0, -1, -3359],
    [2, -1, -1, 1, 2463],
    [2, -1, 0, 1, 2211],
    [2, -1, -1, -1, 2065],
    [0, 1, -1, -1, -1870],
    [4, 0, -1, -1, 1828],
    [0, 1, 0, 1, -1794],
    [0, 0, 0, 3, -1749],
    [0, 1, -1, 1, -1565],
    [1, 0, 0, 1, -1491],
    [0, 1, 1, 1, -1475],
    [0, 1, 1, -1, -1410],
    [0, 1, 0, -1, -1344],
    [1, 0, 0, -1, -1335],
    [0, 0, 3, 1, 1107],
    [4, 0, 0, -1, 1021],
    [4, 0, -1, 1, 833],
    [0, 0, 1, -3, 777],
    [4, 0, -2, 1, 671],
    [2, 0, 0, -3, 607],
    [2, 0, 2, -1, 596],
    [2, -1, 1, -1, 491],
    [2, 0, -2, 1, -451],
    [0, 0, 3, -1, 439],
    [2, 0, 2, 1, 422],
    [2, 0, -3, -1, 421],
    [2, 1, -1, 1, -366],
    [2, 1, 0, 1, -351],
    [4, 0, 0, 1, 331],
    [2, -1, 1, 1, 315],
    [2, -2, 0, -1, 302],
    [0, 0, 1, 3, -283],
    [2, 1, 1, -1, -229],
    [1, 1, 0, -1, 223],
    [1, 1, 0, 1, 223],
    [0, 1, -2, -1, -220],
    [2, 1, -1, -1, -220],
    [1, 0, 1, 1, -185],
    [2, -1, -2, -1, 181],
    [0, 1, 2, 1, -177],
    [4, 0, -2, -1, 176],
    [4, -1, -1, -1, 166],
    [1, 0, 1, -1, -164],
    [4, 0, 1, -1, 132],
    [1, 0, -1, -1, -119],
    [4, -1, 0, -1, 115],
    [2, -2, 0, 1, 107],
];

function moonApparentGeocentric(jd) {
    const T = julianCenturies(jd);
    const Lp = normalizeDegree(
        218.3164477 +
            481267.88123421 * T -
            0.0015786 * T * T +
            (T * T * T) / 538841 -
            (T * T * T * T) / 65194000,
    );
    const D = normalizeDegree(
        297.8501921 +
            445267.1114034 * T -
            0.0018819 * T * T +
            (T * T * T) / 545868 -
            (T * T * T * T) / 113065000,
    );
    const M = normalizeDegree(
        357.5291092 +
            35999.0502909 * T -
            0.0001536 * T * T +
            (T * T * T) / 24490000,
    );
    const Mp = normalizeDegree(
        134.9633964 +
            477198.8675055 * T +
            0.0087414 * T * T +
            (T * T * T) / 69699 -
            (T * T * T * T) / 14712000,
    );
    const F = normalizeDegree(
        93.272095 +
            483202.0175233 * T -
            0.0036539 * T * T -
            (T * T * T) / 3526000 +
            (T * T * T * T) / 863310000,
    );
    const E = 1 - 0.002516 * T - 0.0000074 * T * T;
    const A1 = normalizeDegree(119.75 + 131.849 * T);
    const A2 = normalizeDegree(53.09 + 479264.29 * T);
    const A3 = normalizeDegree(313.45 + 481266.484 * T);

    let sigmaL = 0;
    let sigmaR = 0;
    for (const [d, m, mp, f, sl, sr] of MOON_LR_TERMS) {
        const eFactor = Math.pow(E, Math.abs(m));
        const arg = toRadians(d * D + m * M + mp * Mp + f * F);
        sigmaL += eFactor * sl * Math.sin(arg);
        sigmaR += eFactor * sr * Math.cos(arg);
    }

    let sigmaB = 0;
    for (const [d, m, mp, f, sb] of MOON_B_TERMS) {
        const eFactor = Math.pow(E, Math.abs(m));
        const arg = toRadians(d * D + m * M + mp * Mp + f * F);
        sigmaB += eFactor * sb * Math.sin(arg);
    }

    sigmaL +=
        3958 * Math.sin(toRadians(A1)) +
        1962 * Math.sin(toRadians(Lp - F)) +
        318 * Math.sin(toRadians(A2));
    sigmaB +=
        -2235 * Math.sin(toRadians(Lp)) +
        382 * Math.sin(toRadians(A3)) +
        175 * Math.sin(toRadians(A1 - F)) +
        175 * Math.sin(toRadians(A1 + F)) +
        127 * Math.sin(toRadians(Lp - Mp)) -
        115 * Math.sin(toRadians(Lp + Mp));

    const nut = nutationApprox(T);
    const lambda = normalizeDegree(Lp + sigmaL / 1000000 + nut.dPsi);
    const beta = sigmaB / 1000000;
    const distanceKm = 385000.56 + sigmaR / 1000;
    const epsilon = meanObliquityDegrees(T) + nut.dEps;
    const eq = eclipticToEquatorial(lambda, beta, epsilon);

    return {
        lambda,
        beta,
        ra: eq.ra,
        dec: eq.dec,
        distanceKm,
        horizontalParallax: toDegrees(Math.asin(6378.14 / distanceKm)),
        semidiameter: toDegrees(Math.asin(1737.4 / distanceKm)),
        epsilon,
    };
}

function gmstDegrees(jd) {
    const T = julianCenturies(jd);
    return normalizeDegree(
        280.46061837 +
            360.98564736629 * (jd - 2451545.0) +
            0.000387933 * T * T -
            (T * T * T) / 38710000,
    );
}

function localSiderealDegrees(jd, longitude) {
    return normalizeDegree(gmstDegrees(jd) + longitude);
}

function horizontalFromRaDec(dateObj, raDeg, decDeg, latitude, longitude) {
    const jd = dateToJulianDay(dateObj);
    const phi = toRadians(latitude);
    const dec = toRadians(decDeg);
    const Hdeg = signedAngleDifference(
        localSiderealDegrees(jd, longitude),
        raDeg,
    );
    const H = toRadians(Hdeg);

    const sinAlt =
        Math.sin(phi) * Math.sin(dec) +
        Math.cos(phi) * Math.cos(dec) * Math.cos(H);
    const altitude = Math.asin(clampNumber(sinAlt, -1, 1));

    const cosAlt = Math.max(1e-12, Math.cos(altitude));
    const cosAz =
        (Math.sin(dec) - Math.sin(altitude) * Math.sin(phi)) /
        (cosAlt * Math.cos(phi));
    const sinAz = (-Math.cos(dec) * Math.sin(H)) / cosAlt;
    const azimuth = normalizeDegree(
        toDegrees(Math.atan2(sinAz, clampNumber(cosAz, -1, 1))),
    );

    return {
        altitude: toDegrees(altitude),
        azimuth,
        hourAngle: Hdeg,
    };
}

function sunHorizontalPrecise(dateObj, latitude, longitude) {
    const sun = sunApparentGeocentric(dateToJulianDay(dateObj));
    const horiz = horizontalFromRaDec(
        dateObj,
        sun.ra,
        sun.dec,
        latitude,
        longitude,
    );
    return { ...sun, ...horiz };
}

function topocentricMoonEquatorial(
    dateObj,
    latitude,
    longitude,
    elevationMeters = OBSERVER_ELEVATION_METERS,
) {
    const jd = dateToJulianDay(dateObj);
    const moon = moonApparentGeocentric(jd);
    const phi = toRadians(latitude);
    const H = toRadians(
        signedAngleDifference(localSiderealDegrees(jd, longitude), moon.ra),
    );
    const dec = toRadians(moon.dec);
    const pi = toRadians(moon.horizontalParallax);
    const u = Math.atan(0.99664719 * Math.tan(phi));
    const rhoSin =
        0.99664719 * Math.sin(u) + (elevationMeters / 6378140) * Math.sin(phi);
    const rhoCos = Math.cos(u) + (elevationMeters / 6378140) * Math.cos(phi);

    const deltaAlpha = Math.atan2(
        -rhoCos * Math.sin(pi) * Math.sin(H),
        Math.cos(dec) - rhoCos * Math.sin(pi) * Math.cos(H),
    );

    const raTopo = normalizeDegree(moon.ra + toDegrees(deltaAlpha));
    const decTopo = toDegrees(
        Math.atan2(
            (Math.sin(dec) - rhoSin * Math.sin(pi)) * Math.cos(deltaAlpha),
            Math.cos(dec) - rhoCos * Math.sin(pi) * Math.cos(H),
        ),
    );

    return {
        ...moon,
        topocentricRa: raTopo,
        topocentricDec: decTopo,
    };
}

function moonHorizontalPrecise(dateObj, latitude, longitude) {
    const moon = topocentricMoonEquatorial(dateObj, latitude, longitude);
    const horiz = horizontalFromRaDec(
        dateObj,
        moon.topocentricRa,
        moon.topocentricDec,
        latitude,
        longitude,
    );
    return { ...moon, ...horiz };
}

function atmosphericRefractionDeg(altitudeDeg) {
    if (!Number.isFinite(altitudeDeg) || altitudeDeg < -1) return 0;
    const adjustedAltitude = altitudeDeg + 10.3 / (altitudeDeg + 5.11);
    return 1.02 / Math.tan(toRadians(adjustedAltitude)) / 60;
}

function geocentricElongationDegrees(dateObj) {
    const jd = dateToJulianDay(dateObj);
    const sun = sunApparentGeocentric(jd);
    const moon = moonApparentGeocentric(jd);
    return angularSeparationDegrees(
        toRadians(sun.dec),
        toRadians(sun.ra),
        toRadians(moon.dec),
        toRadians(moon.ra),
    );
}

function sunAltitudeDegAt(dateObj, latitude, longitude) {
    return sunHorizontalPrecise(dateObj, latitude, longitude).altitude;
}

function solarLocalHourTimestamp(dateObj, longitude, localHour) {
    // Semua rentang jam pada mesin falak dibaca sebagai jam sipil di lokasi markaz,
    // bukan zona waktu perangkat. Ini menjaga hasil tetap stabil saat aplikasi
    // dibuka dari server, WebView, atau browser dengan zona waktu berbeda.
    const base = new Date(
        dateObj.getFullYear(),
        dateObj.getMonth(),
        dateObj.getDate(),
        0,
        0,
        0,
        0,
    );
    const deviceOffsetHours = -base.getTimezoneOffset() / 60;
    const locationOffsetHours = Number(longitude) / 15;
    return (
        base.getTime() +
        (Number(localHour) - locationOffsetHours + deviceOffsetHours) * 3600000
    );
}

function findSunAltitudeEvent(
    dateObj,
    latitude,
    longitude,
    targetAltitudeDeg,
    startHour,
    endHour,
) {
    const startTime = solarLocalHourTimestamp(dateObj, longitude, startHour);
    const endTime = solarLocalHourTimestamp(dateObj, longitude, endHour);
    const stepMs = 5 * 60 * 1000;
    let previousTime = startTime;
    let previousDiff =
        sunAltitudeDegAt(new Date(previousTime), latitude, longitude) -
        targetAltitudeDeg;
    let closest = { time: previousTime, diff: Math.abs(previousDiff) };

    for (let t = startTime + stepMs; t <= endTime; t += stepMs) {
        const currentDiff =
            sunAltitudeDegAt(new Date(t), latitude, longitude) -
            targetAltitudeDeg;
        const currentAbsDiff = Math.abs(currentDiff);
        if (currentAbsDiff < closest.diff)
            closest = { time: t, diff: currentAbsDiff };

        if (
            previousDiff === 0 ||
            currentDiff === 0 ||
            (previousDiff < 0 && currentDiff > 0) ||
            (previousDiff > 0 && currentDiff < 0)
        ) {
            let low = previousTime;
            let high = t;
            let lowDiff = previousDiff;

            for (let i = 0; i < 40; i++) {
                const mid = Math.floor((low + high) / 2);
                const midDiff =
                    sunAltitudeDegAt(new Date(mid), latitude, longitude) -
                    targetAltitudeDeg;
                if (
                    (lowDiff <= 0 && midDiff <= 0) ||
                    (lowDiff >= 0 && midDiff >= 0)
                ) {
                    low = mid;
                    lowDiff = midDiff;
                } else {
                    high = mid;
                }
            }
            return new Date(Math.floor((low + high) / 2));
        }

        previousTime = t;
        previousDiff = currentDiff;
    }

    return closest.diff <= 0.05 ? new Date(closest.time) : null;
}

function findSolarNoonPrecise(dateObj, latitude, longitude) {
    let low = solarLocalHourTimestamp(dateObj, longitude, 9);
    let high = solarLocalHourTimestamp(dateObj, longitude, 15);

    for (let i = 0; i < 50; i++) {
        const m1 = low + (high - low) / 3;
        const m2 = high - (high - low) / 3;
        const alt1 = sunAltitudeDegAt(new Date(m1), latitude, longitude);
        const alt2 = sunAltitudeDegAt(new Date(m2), latitude, longitude);
        if (alt1 < alt2) {
            low = m1;
        } else {
            high = m2;
        }
    }

    return new Date(Math.round((low + high) / 2));
}

function findSunsetPrecise(dateObj, latitude, longitude) {
    return findSunAltitudeEvent(
        dateObj,
        latitude,
        longitude,
        STANDARD_SUNSET_ALTITUDE,
        15,
        21,
    );
}

function sunAzimuthCompass(dateObj) {
    return sunHorizontalPrecise(dateObj, currentLat, currentLng).azimuth;
}

function findSunAzimuthTime(dateObj, targetAzimuth) {
    const sunrise = findSunAltitudeEvent(
        dateObj,
        currentLat,
        currentLng,
        STANDARD_SUNSET_ALTITUDE,
        3,
        9,
    );
    const sunset = findSunsetPrecise(dateObj, currentLat, currentLng);
    if (
        !sunrise ||
        !sunset ||
        isNaN(sunrise.getTime()) ||
        isNaN(sunset.getTime())
    )
        return null;

    const stepMs = 60 * 1000;
    let previousTime = sunrise.getTime();
    let previousDiff = signedAngleDifference(
        sunAzimuthCompass(new Date(previousTime)),
        targetAzimuth,
    );
    let closest = { time: previousTime, diff: Math.abs(previousDiff) };

    for (let t = previousTime + stepMs; t <= sunset.getTime(); t += stepMs) {
        const currentDiff = signedAngleDifference(
            sunAzimuthCompass(new Date(t)),
            targetAzimuth,
        );
        const currentAbsDiff = Math.abs(currentDiff);

        if (currentAbsDiff < closest.diff) {
            closest = { time: t, diff: currentAbsDiff };
        }

        const crossedTarget =
            previousDiff === 0 ||
            currentDiff === 0 ||
            (previousDiff < 0 && currentDiff > 0) ||
            (previousDiff > 0 && currentDiff < 0);

        if (crossedTarget && Math.abs(previousDiff - currentDiff) < 90) {
            let low = previousTime;
            let high = t;
            let lowDiff = previousDiff;

            for (let i = 0; i < 40; i++) {
                const mid = Math.floor((low + high) / 2);
                const midDiff = signedAngleDifference(
                    sunAzimuthCompass(new Date(mid)),
                    targetAzimuth,
                );
                if (
                    (lowDiff <= 0 && midDiff <= 0) ||
                    (lowDiff >= 0 && midDiff >= 0)
                ) {
                    low = mid;
                    lowDiff = midDiff;
                } else {
                    high = mid;
                }
            }
            return {
                date: new Date(Math.floor((low + high) / 2)),
                angularError: 0,
            };
        }

        previousTime = t;
        previousDiff = currentDiff;
    }

    return closest.diff <= 0.05
        ? { date: new Date(closest.time), angularError: closest.diff }
        : null;
}

function calculateRoshdulKiblat(dateObj) {
    const istiwa = findSolarNoonPrecise(dateObj, currentLat, currentLng);
    const qiblaBearing = getQiblaBearing(currentLat, currentLng);
    const shadowTargetAzimuth = normalizeDegree(qiblaBearing + 180);

    let roshdulResult = findSunAzimuthTime(dateObj, shadowTargetAzimuth);
    let arahBayangan = "Bayangan mengarah kiblat";
    if (!roshdulResult) {
        roshdulResult = findSunAzimuthTime(dateObj, qiblaBearing);
        arahBayangan = "Bayangan segaris kiblat";
    }

    const roshdulDate = roshdulResult ? roshdulResult.date : null;
    const noonLocal = locationCivilDateTime(
        dateObj,
        12,
        0,
        0,
        currentLat,
        currentLng,
    );
    const diffWibToIstiwaMs =
        istiwa && !isNaN(istiwa.getTime())
            ? istiwa.getTime() - noonLocal.getTime()
            : null;

    return {
        roshdul: formatClock(roshdulDate),
        roshdulDetail: formatClock(roshdulDate, true),
        istiwa: formatClock(istiwa),
        istiwaDetail: formatClock(istiwa, true),
        selisih: formatTimeDifference(diffWibToIstiwaMs),
        selisihDetail: formatTimeDifference(diffWibToIstiwaMs, true),
        qiblaBearing: formatDegree(qiblaBearing),
        arahBayangan,
    };
}

// FUNGSI FALAKIYAH (ASTRONOMI)
function formatDegree(decimalDeg, isAltitude = false) {
    let sign = decimalDeg < 0 ? "-" : isAltitude ? "+" : "";
    let absDeg = Math.abs(decimalDeg);
    let d = Math.floor(absDeg);
    let mFloat = (absDeg - d) * 60;
    let m = Math.floor(mFloat);
    let s = Math.round((mFloat - m) * 60);
    if (s === 60) {
        s = 0;
        m += 1;
    }
    if (m === 60) {
        m = 0;
        d += 1;
    }
    return `${sign}${d.toString().padStart(2, "0")}° ${m.toString().padStart(2, "0")}' ${s.toString().padStart(2, "0")}"`;
}

function clampNumber(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function angularSeparationDegrees(alt1Rad, az1Rad, alt2Rad, az2Rad) {
    const cosSep =
        Math.sin(alt1Rad) * Math.sin(alt2Rad) +
        Math.cos(alt1Rad) * Math.cos(alt2Rad) * Math.cos(az1Rad - az2Rad);
    return (Math.acos(clampNumber(cosSep, -1, 1)) * 180) / Math.PI;
}

function addDays(dateObj, count) {
    const result = new Date(
        dateObj.getFullYear(),
        dateObj.getMonth(),
        dateObj.getDate(),
        0,
        0,
        0,
        0,
    );
    result.setDate(result.getDate() + count);
    return result;
}

function dateDiffDays(startDate, endDate) {
    const startUtc = Date.UTC(
        startDate.getFullYear(),
        startDate.getMonth(),
        startDate.getDate(),
    );
    const endUtc = Date.UTC(
        endDate.getFullYear(),
        endDate.getMonth(),
        endDate.getDate(),
    );
    return Math.round((endUtc - startUtc) / 86400000);
}

function lunarSolarLongitudeDifference(dateObj) {
    const jd = dateToJulianDay(dateObj);
    const sun = sunApparentGeocentric(jd);
    const moon = moonApparentGeocentric(jd);
    return signedAngleDifference(moon.lambda, sun.lambda);
}

function findIjtima(baseDate) {
    const searchStart = baseDate.getTime() - 16 * 86400000;
    const searchEnd = baseDate.getTime() + 16 * 86400000;
    const stepMs = 6 * 3600000;
    const roots = [];

    let prevTime = searchStart;
    let prevDiff = lunarSolarLongitudeDifference(new Date(prevTime));

    for (let t = searchStart + stepMs; t <= searchEnd; t += stepMs) {
        const currentDiff = lunarSolarLongitudeDifference(new Date(t));
        const crossed =
            prevDiff === 0 ||
            currentDiff === 0 ||
            (((prevDiff < 0 && currentDiff > 0) ||
                (prevDiff > 0 && currentDiff < 0)) &&
                Math.abs(prevDiff - currentDiff) < 60);

        if (crossed) {
            let low = prevTime;
            let high = t;
            let lowDiff = prevDiff;

            for (let i = 0; i < 48; i++) {
                const mid = Math.floor((low + high) / 2);
                const midDiff = lunarSolarLongitudeDifference(new Date(mid));
                if (
                    (lowDiff <= 0 && midDiff <= 0) ||
                    (lowDiff >= 0 && midDiff >= 0)
                ) {
                    low = mid;
                    lowDiff = midDiff;
                } else {
                    high = mid;
                }
            }
            roots.push(new Date(Math.floor((low + high) / 2)));
        }

        prevTime = t;
        prevDiff = currentDiff;
    }

    if (roots.length > 0) {
        roots.sort(
            (a, b) =>
                Math.abs(a.getTime() - baseDate.getTime()) -
                Math.abs(b.getTime() - baseDate.getTime()),
        );
        return roots[0];
    }

    // Fallback aman: ambil waktu dengan selisih bujur ekliptika terkecil.
    let bestTime = baseDate.getTime();
    let bestDiff = Math.abs(lunarSolarLongitudeDifference(baseDate));
    for (let t = searchStart; t <= searchEnd; t += 3600000) {
        const diff = Math.abs(lunarSolarLongitudeDifference(new Date(t)));
        if (diff < bestDiff) {
            bestDiff = diff;
            bestTime = t;
        }
    }
    return new Date(bestTime);
}

function calculateSunMoonDataAtSunset(obsDate, markaz = null) {
    const latitude = Number(markaz?.lat ?? currentLat);
    const longitude = Number(markaz?.lng ?? currentLng);
    const markazName = markaz?.name || "Markaz Aktif";
    const sunset = findSunsetPrecise(obsDate, latitude, longitude);

    if (!sunset || isNaN(sunset.getTime())) {
        throw new Error(
            `Waktu matahari terbenam tidak dapat dihitung untuk ${markazName}.`,
        );
    }

    const sun = sunHorizontalPrecise(sunset, latitude, longitude);
    const moon = moonHorizontalPrecise(sunset, latitude, longitude);

    const moonGeometricAltitude = moon.altitude;
    const moonRefraction = atmosphericRefractionDeg(moonGeometricAltitude);
    const moonApparentAltitude = moonGeometricAltitude + moonRefraction;
    const moonUpperLimbAltitude = moonApparentAltitude + moon.semidiameter;

    const topocentricElongation = angularSeparationDegrees(
        toRadians(sun.altitude),
        toRadians(sun.azimuth),
        toRadians(moon.altitude),
        toRadians(moon.azimuth),
    );

    const elongation = geocentricElongationDegrees(sunset);
    const ijtimaDate = findIjtima(sunset);
    const moonAgeHours = (sunset.getTime() - ijtimaDate.getTime()) / 3600000;
    const fraction = ((1 - Math.cos(toRadians(elongation))) / 2) * 100;

    const mabimsOk =
        moonAgeHours >= 0 &&
        moonApparentAltitude >= MABIMS_MIN_ALTITUDE &&
        elongation >= MABIMS_MIN_ELONGATION;

    return {
        obsDate: sunset,
        ijtimaDate,
        moonAltitude: moonApparentAltitude,
        moonGeometricAltitude,
        moonUpperLimbAltitude,
        moonSemidiameter: moon.semidiameter,
        moonDistanceKm: moon.distanceKm,
        elongation,
        topocentricElongation,
        moonAzimuth: moon.azimuth,
        sunAzimuth: sun.azimuth,
        fraction,
        moonAgeHours,
        elongationSource: "Elongasi geosentrik Matahari-Bulan pada saat magrib",
        mabimsOk,
        markazName,
        latitude,
        longitude,
    };
}

function sortHisabPriority(a, b) {
    if (Number(a.mabimsOk) !== Number(b.mabimsOk))
        return Number(b.mabimsOk) - Number(a.mabimsOk);
    if (Math.abs(b.moonAltitude - a.moonAltitude) > 0.000001)
        return b.moonAltitude - a.moonAltitude;
    return b.elongation - a.elongation;
}

function isSameCoordinate(a, b) {
    return (
        Math.abs(Number(a.lat) - Number(b.lat)) < 0.0001 &&
        Math.abs(Number(a.lng) - Number(b.lng)) < 0.0001
    );
}

function evaluateMabimsWithPriority(obsDate) {
    // Urutan keputusan:
    // 1. Hitung lebih dulu dari lokasi GPS.
    // 2. Jika GPS gagal, hitung dari default Pasuruan.
    // 3. Jika markaz utama tidak memenuhi MABIMS, baru cek kota rujukan Indonesia lain.
    // 4. Jika semua tidak memenuhi, tetap pakai data markaz utama dan tetapkan istikmal.
    const primaryMarkaz = {
        name: currentMarkazName || DEFAULT_MARKAZ.name,
        lat: Number(currentLat),
        lng: Number(currentLng),
    };

    const primaryResult = calculateSunMoonDataAtSunset(obsDate, primaryMarkaz);

    if (isValidMabimsHilal(primaryResult)) {
        return {
            ...primaryResult,
            mabimsOk: true,
            decisiveMarkazName: primaryResult.markazName,
            primaryMarkazName: primaryResult.markazName,
            primaryResult,
            passedCities: [primaryResult],
            passedCityNames: [primaryResult.markazName],
            passedCityCount: 1,
            checkedCityCount: 1,
            allMarkazResults: [primaryResult],
            nationalPolicy: NATIONAL_MABIMS_POLICY,
            decisionText: `Tidak istikmal. ${primaryResult.markazName} memenuhi ${MABIMS_CRITERIA_LABEL}.`,
        };
    }

    const otherResults = INDONESIA_MABIMS_MARKAZ.filter(
        (markaz) => !isSameCoordinate(markaz, primaryMarkaz),
    )
        .map((markaz) => calculateSunMoonDataAtSunset(obsDate, markaz))
        .sort(sortHisabPriority);

    const passedCities = otherResults.filter(isValidMabimsHilal);

    if (passedCities.length > 0) {
        const decisive = passedCities[0];

        return {
            ...decisive,
            mabimsOk: true,
            decisiveMarkazName: decisive.markazName,
            primaryMarkazName: primaryResult.markazName,
            primaryResult,
            passedCities,
            passedCityNames: passedCities.map((item) => item.markazName),
            passedCityCount: passedCities.length,
            checkedCityCount: otherResults.length + 1,
            allMarkazResults: [primaryResult, ...otherResults],
            nationalPolicy: NATIONAL_MABIMS_POLICY,
            decisionText: `Tidak istikmal. ${primaryResult.markazName} belum memenuhi, tetapi ${decisive.markazName} memenuhi ${MABIMS_CRITERIA_LABEL}.`,
        };
    }

    return {
        ...primaryResult,
        mabimsOk: false,
        decisiveMarkazName: primaryResult.markazName,
        primaryMarkazName: primaryResult.markazName,
        primaryResult,
        passedCities: [],
        passedCityNames: [],
        passedCityCount: 0,
        checkedCityCount: otherResults.length + 1,
        allMarkazResults: [primaryResult, ...otherResults],
        nationalPolicy: NATIONAL_MABIMS_POLICY,
        decisionText: `Istikmal 30 hari. ${primaryResult.markazName} belum memenuhi dan tidak ada kota rujukan lain yang memenuhi ${MABIMS_CRITERIA_LABEL}.`,
    };
}

function calculateHisabData(tanggalSatuGregorian) {
    // Fungsi lama dipertahankan hanya untuk kompatibilitas cadangan.
    // Alur utama tidak lagi memakai H-1 sebagai satu-satunya dasar awal bulan.
    // Setiap bulan sekarang memakai keputusan transisi dari gurub tanggal 29 bulan sebelumnya.
    const obsDate = addDays(tanggalSatuGregorian, -1);
    const result = evaluateMabimsWithPriority(obsDate);
    return {
        ...result,
        observedGregorianDate: obsDate,
        observedGregorianText: formatGregorianDateLong(obsDate),
        decisionMode: result.mabimsOk
            ? "Rukyat MABIMS (cadangan)"
            : "Istikmal 30 hari (cadangan)",
        startExplanation:
            "Data cadangan. Panel utama memakai data transisi tanggal 29 bulan sebelumnya.",
    };
}

// ==========================================================
// HISAB GERHANA MATAHARI DAN BULAN
// ==========================================================
const ECLIPSE_SOLAR_NODE_LIMIT_DEG = 1.65;
const ECLIPSE_LUNAR_NODE_LIMIT_DEG = 1.35;
const ECLIPSE_CONTACT_SEARCH_HOURS = 12;
const ECLIPSE_SCAN_STEP_MINUTES = 5;

function sunSemidiameterDegrees(dateObj) {
    const sun = sunApparentGeocentric(dateToJulianDay(dateObj));
    return 0.26656388 / Math.max(0.1, sun.distanceAu || 1);
}

function formatPercent(value, decimals = 1) {
    if (!Number.isFinite(value)) return "-";
    return `${value.toFixed(decimals)} %`;
}

function formatDecimal(value, decimals = 3) {
    if (!Number.isFinite(value)) return "-";
    return value.toFixed(decimals);
}

function formatEclipseDateTime(dateObj, withSeconds = false) {
    if (!dateObj || isNaN(dateObj.getTime())) return "-";
    const dateText = `${hariIndo[dateObj.getDay()]} ${getPasaran(formatDateDDMMYYYY(dateObj))}, ${dateObj.getDate()} ${bulanMasehi[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
    return `${dateText} pukul ${formatClock(dateObj, withSeconds)} ${getTimezoneLabelForDate(dateObj)}`;
}

function moonPhaseDifference(dateObj, targetDeg) {
    const jd = dateToJulianDay(dateObj);
    const sun = sunApparentGeocentric(jd);
    const moon = moonApparentGeocentric(jd);
    const phase = normalizeDegree(moon.lambda - sun.lambda);
    return signedAngleDifference(phase, targetDeg);
}

function findLunarPhaseNear(baseDate, targetDeg) {
    const searchStart = baseDate.getTime() - 16 * 86400000;
    const searchEnd = baseDate.getTime() + 16 * 86400000;
    const stepMs = 6 * 3600000;
    const roots = [];

    let previousTime = searchStart;
    let previousDiff = moonPhaseDifference(new Date(previousTime), targetDeg);

    for (let t = searchStart + stepMs; t <= searchEnd; t += stepMs) {
        const currentDiff = moonPhaseDifference(new Date(t), targetDeg);
        const crossed =
            previousDiff === 0 ||
            currentDiff === 0 ||
            (((previousDiff < 0 && currentDiff > 0) ||
                (previousDiff > 0 && currentDiff < 0)) &&
                Math.abs(previousDiff - currentDiff) < 80);

        if (crossed) {
            let low = previousTime;
            let high = t;
            let lowDiff = previousDiff;

            for (let i = 0; i < 50; i++) {
                const mid = Math.floor((low + high) / 2);
                const midDiff = moonPhaseDifference(new Date(mid), targetDeg);
                if (
                    (lowDiff <= 0 && midDiff <= 0) ||
                    (lowDiff >= 0 && midDiff >= 0)
                ) {
                    low = mid;
                    lowDiff = midDiff;
                } else {
                    high = mid;
                }
            }
            roots.push(new Date(Math.floor((low + high) / 2)));
        }

        previousTime = t;
        previousDiff = currentDiff;
    }

    if (roots.length > 0) {
        roots.sort(
            (a, b) =>
                Math.abs(a.getTime() - baseDate.getTime()) -
                Math.abs(b.getTime() - baseDate.getTime()),
        );
        return roots[0];
    }

    let bestTime = baseDate.getTime();
    let bestDiff = Math.abs(moonPhaseDifference(baseDate, targetDeg));
    for (let t = searchStart; t <= searchEnd; t += 3600000) {
        const diff = Math.abs(moonPhaseDifference(new Date(t), targetDeg));
        if (diff < bestDiff) {
            bestDiff = diff;
            bestTime = t;
        }
    }
    return new Date(bestTime);
}

function solarEclipseGeometry(
    dateObj,
    latitude = currentLat,
    longitude = currentLng,
) {
    const sun = sunHorizontalPrecise(dateObj, latitude, longitude);
    const moon = moonHorizontalPrecise(dateObj, latitude, longitude);
    const separation = angularSeparationDegrees(
        toRadians(sun.altitude),
        toRadians(sun.azimuth),
        toRadians(moon.altitude),
        toRadians(moon.azimuth),
    );
    const sunSd = sunSemidiameterDegrees(dateObj);
    const moonSd = moon.semidiameter;
    const partialLimit = sunSd + moonSd;
    const centralLimit = Math.abs(moonSd - sunSd);
    const magnitude = clampNumber(
        (partialLimit - separation) / (2 * sunSd),
        0,
        2,
    );
    const obscuration =
        magnitude > 0 ? clampNumber(magnitude * 100, 0, 100) : 0;

    return {
        date: dateObj,
        separation,
        sunAltitude: sun.altitude,
        sunAzimuth: sun.azimuth,
        moonAltitude: moon.altitude,
        moonAzimuth: moon.azimuth,
        sunSemidiameter: sunSd,
        moonSemidiameter: moonSd,
        partialLimit,
        centralLimit,
        magnitude,
        obscuration,
    };
}

function lunarEclipseGeometry(dateObj) {
    const jd = dateToJulianDay(dateObj);
    const sun = sunApparentGeocentric(jd);
    const moon = moonApparentGeocentric(jd);
    const antiSunRa = normalizeDegree(sun.ra + 180);
    const antiSunDec = -sun.dec;
    const separation = angularSeparationDegrees(
        toRadians(moon.dec),
        toRadians(moon.ra),
        toRadians(antiSunDec),
        toRadians(antiSunRa),
    );
    const sunSd = sunSemidiameterDegrees(dateObj);
    const moonSd = moon.semidiameter;
    const umbraRadius = Math.max(0, moon.horizontalParallax - sunSd);
    const penumbraRadius = moon.horizontalParallax + sunSd;
    const penumbralMagnitude =
        (penumbraRadius + moonSd - separation) / (2 * moonSd);
    const umbralMagnitude = (umbraRadius + moonSd - separation) / (2 * moonSd);
    const moonTopo = moonHorizontalPrecise(dateObj, currentLat, currentLng);

    return {
        date: dateObj,
        separation,
        sunSemidiameter: sunSd,
        moonSemidiameter: moonSd,
        moonLatitude: moon.beta,
        moonDistanceKm: moon.distanceKm,
        horizontalParallax: moon.horizontalParallax,
        umbraRadius,
        penumbraRadius,
        penumbralMagnitude,
        umbralMagnitude,
        moonAltitudeAtMarkaz: moonTopo.altitude,
        moonAzimuthAtMarkaz: moonTopo.azimuth,
    };
}

function refineMinimumTime(centerTime, radiusMs, geometryFn) {
    let low = centerTime - radiusMs;
    let high = centerTime + radiusMs;
    for (let i = 0; i < 45; i++) {
        const m1 = low + (high - low) / 3;
        const m2 = high - (high - low) / 3;
        const g1 = geometryFn(new Date(m1));
        const g2 = geometryFn(new Date(m2));
        if (g1.separation < g2.separation) {
            high = m2;
        } else {
            low = m1;
        }
    }
    return new Date(Math.round((low + high) / 2));
}

function findContactTimesAround(
    maxDate,
    geometryFn,
    thresholdFn,
    hours = ECLIPSE_CONTACT_SEARCH_HOURS,
) {
    const start = maxDate.getTime() - hours * 3600000;
    const end = maxDate.getTime() + hours * 3600000;
    const step = ECLIPSE_SCAN_STEP_MINUTES * 60000;
    const contacts = [];

    let previousTime = start;
    let previousValue =
        geometryFn(new Date(previousTime)).separation -
        thresholdFn(new Date(previousTime));

    for (let t = start + step; t <= end; t += step) {
        const currentValue =
            geometryFn(new Date(t)).separation - thresholdFn(new Date(t));
        if (
            previousValue === 0 ||
            currentValue === 0 ||
            (previousValue < 0 && currentValue > 0) ||
            (previousValue > 0 && currentValue < 0)
        ) {
            let low = previousTime;
            let high = t;
            let lowValue = previousValue;

            for (let i = 0; i < 45; i++) {
                const mid = Math.floor((low + high) / 2);
                const midValue =
                    geometryFn(new Date(mid)).separation -
                    thresholdFn(new Date(mid));
                if (
                    (lowValue <= 0 && midValue <= 0) ||
                    (lowValue >= 0 && midValue >= 0)
                ) {
                    low = mid;
                    lowValue = midValue;
                } else {
                    high = mid;
                }
            }
            contacts.push(new Date(Math.floor((low + high) / 2)));
        }
        previousTime = t;
        previousValue = currentValue;
    }

    contacts.sort((a, b) => a.getTime() - b.getTime());
    return contacts;
}

function calculateSolarEclipseForMonth(hisab) {
    const conjunction = hisab?.ijtimaDate ? new Date(hisab.ijtimaDate) : null;
    if (!conjunction || isNaN(conjunction.getTime())) return null;

    const moonAtConjunction = moonApparentGeocentric(
        dateToJulianDay(conjunction),
    );
    const nodeDistance = Math.abs(moonAtConjunction.beta);
    const globalPotential = nodeDistance <= ECLIPSE_SOLAR_NODE_LIMIT_DEG;

    const searchStart =
        conjunction.getTime() - ECLIPSE_CONTACT_SEARCH_HOURS * 3600000;
    const searchEnd =
        conjunction.getTime() + ECLIPSE_CONTACT_SEARCH_HOURS * 3600000;
    const step = ECLIPSE_SCAN_STEP_MINUTES * 60000;
    let bestTime = conjunction.getTime();
    let bestGeometry = solarEclipseGeometry(
        conjunction,
        currentLat,
        currentLng,
    );
    let bestVisibleTime = null;
    let bestVisibleGeometry = null;

    for (let t = searchStart; t <= searchEnd; t += step) {
        const geometry = solarEclipseGeometry(
            new Date(t),
            currentLat,
            currentLng,
        );
        if (geometry.separation < bestGeometry.separation) {
            bestGeometry = geometry;
            bestTime = t;
        }
        if (geometry.sunAltitude > -0.8333) {
            if (
                !bestVisibleGeometry ||
                geometry.separation < bestVisibleGeometry.separation
            ) {
                bestVisibleGeometry = geometry;
                bestVisibleTime = t;
            }
        }
    }

    const maxTime = refineMinimumTime(bestTime, step * 2, (dateObj) =>
        solarEclipseGeometry(dateObj, currentLat, currentLng),
    );
    const maxGeometry = solarEclipseGeometry(maxTime, currentLat, currentLng);
    const visibleMaxTime = bestVisibleTime
        ? refineMinimumTime(bestVisibleTime, step * 2, (dateObj) =>
              solarEclipseGeometry(dateObj, currentLat, currentLng),
          )
        : null;
    const visibleMaxGeometry = visibleMaxTime
        ? solarEclipseGeometry(visibleMaxTime, currentLat, currentLng)
        : null;

    const localGeometry =
        visibleMaxGeometry && visibleMaxGeometry.sunAltitude > -0.8333
            ? visibleMaxGeometry
            : maxGeometry;
    const localPotential =
        !!visibleMaxGeometry &&
        visibleMaxGeometry.separation <= visibleMaxGeometry.partialLimit &&
        visibleMaxGeometry.sunAltitude > -0.8333;
    const isCentral =
        localPotential &&
        localGeometry.separation <= localGeometry.centralLimit;
    const type =
        !globalPotential && !localPotential
            ? "Tidak ada indikasi gerhana Matahari"
            : localPotential
              ? isCentral
                  ? localGeometry.moonSemidiameter >=
                    localGeometry.sunSemidiameter
                      ? "Gerhana Matahari Total"
                      : "Gerhana Matahari Cincin"
                  : "Gerhana Matahari Sebagian"
              : "Potensi Gerhana Matahari Global";

    const contacts = localPotential
        ? findContactTimesAround(
              localGeometry.date,
              (dateObj) =>
                  solarEclipseGeometry(dateObj, currentLat, currentLng),
              (dateObj) =>
                  solarEclipseGeometry(dateObj, currentLat, currentLng)
                      .partialLimit,
              8,
          )
        : [];
    const centralContacts =
        localPotential && isCentral
            ? findContactTimesAround(
                  localGeometry.date,
                  (dateObj) =>
                      solarEclipseGeometry(dateObj, currentLat, currentLng),
                  (dateObj) =>
                      solarEclipseGeometry(dateObj, currentLat, currentLng)
                          .centralLimit,
                  8,
              )
            : [];

    return {
        kind: "solar",
        title: "Gerhana Matahari",
        type,
        potential: globalPotential || localPotential,
        globalPotential,
        localPotential,
        conjunction,
        nodeDistance,
        maxTime: localGeometry.date,
        maximumGeometry: localGeometry,
        contacts,
        centralContacts,
        rows: [
            { label: "Status", value: type },
            {
                label: "Ijtima'",
                value: formatEclipseDateTime(conjunction, true),
            },
            {
                label: "Jarak Bulan dari ekliptika",
                value: formatDegree(moonAtConjunction.beta, true),
            },
            {
                label: "Maksimum markaz",
                value: formatEclipseDateTime(localGeometry.date, true),
            },
            {
                label: "Magnitudo lokal",
                value: formatDecimal(localGeometry.magnitude, 3),
            },
            {
                label: "Tutupan piringan taksiran",
                value: formatPercent(localGeometry.obscuration, 1),
            },
            {
                label: "Separasi pusat",
                value: formatDegree(localGeometry.separation),
            },
            {
                label: "Tinggi Matahari",
                value: formatDegree(localGeometry.sunAltitude, true),
            },
            {
                label: "Kontak lokal",
                value:
                    contacts.length >= 2
                        ? `${formatClock(contacts[0], true)} - ${formatClock(contacts[contacts.length - 1], true)} ${getTimezoneLabelForDate(contacts[0])}`
                        : "Tidak tampak dari markaz",
            },
        ],
    };
}

function calculateLunarEclipseForMonth(monthData) {
    if (!monthData || !monthData.length) return null;
    const firstDay = parseDate(monthData[0].date.gregorian.date);
    const fullMoonGuess = addDays(firstDay, 14);
    const opposition = findLunarPhaseNear(fullMoonGuess, 180);
    const moonAtOpposition = moonApparentGeocentric(
        dateToJulianDay(opposition),
    );
    const nodeDistance = Math.abs(moonAtOpposition.beta);
    let maxTime = opposition;
    let bestGeometry = lunarEclipseGeometry(maxTime);

    const searchStart =
        opposition.getTime() - ECLIPSE_CONTACT_SEARCH_HOURS * 3600000;
    const searchEnd =
        opposition.getTime() + ECLIPSE_CONTACT_SEARCH_HOURS * 3600000;
    const step = ECLIPSE_SCAN_STEP_MINUTES * 60000;
    let bestTime = opposition.getTime();

    for (let t = searchStart; t <= searchEnd; t += step) {
        const geometry = lunarEclipseGeometry(new Date(t));
        if (geometry.separation < bestGeometry.separation) {
            bestGeometry = geometry;
            bestTime = t;
        }
    }

    maxTime = refineMinimumTime(bestTime, step * 2, lunarEclipseGeometry);
    bestGeometry = lunarEclipseGeometry(maxTime);

    const penumbral = bestGeometry.penumbralMagnitude > 0;
    const umbral = bestGeometry.umbralMagnitude > 0;
    const total = bestGeometry.umbralMagnitude >= 1;
    const potential = penumbral || nodeDistance <= ECLIPSE_LUNAR_NODE_LIMIT_DEG;
    const type = total
        ? "Gerhana Bulan Total"
        : umbral
          ? "Gerhana Bulan Sebagian"
          : penumbral
            ? "Gerhana Bulan Penumbra"
            : nodeDistance <= ECLIPSE_LUNAR_NODE_LIMIT_DEG
              ? "Potensi Gerhana Bulan Global"
              : "Tidak ada indikasi gerhana Bulan";

    const pContacts = penumbral
        ? findContactTimesAround(maxTime, lunarEclipseGeometry, (dateObj) => {
              const g = lunarEclipseGeometry(dateObj);
              return g.penumbraRadius + g.moonSemidiameter;
          })
        : [];
    const uContacts = umbral
        ? findContactTimesAround(maxTime, lunarEclipseGeometry, (dateObj) => {
              const g = lunarEclipseGeometry(dateObj);
              return g.umbraRadius + g.moonSemidiameter;
          })
        : [];
    const tContacts = total
        ? findContactTimesAround(maxTime, lunarEclipseGeometry, (dateObj) => {
              const g = lunarEclipseGeometry(dateObj);
              return Math.max(0, g.umbraRadius - g.moonSemidiameter);
          })
        : [];

    return {
        kind: "lunar",
        title: "Gerhana Bulan",
        type,
        potential,
        opposition,
        nodeDistance,
        maxTime,
        maximumGeometry: bestGeometry,
        contacts: pContacts,
        umbraContacts: uContacts,
        totalContacts: tContacts,
        rows: [
            { label: "Status", value: type },
            {
                label: "Istiqbal / Purnama",
                value: formatEclipseDateTime(opposition, true),
            },
            {
                label: "Maksimum gerhana",
                value: formatEclipseDateTime(maxTime, true),
            },
            {
                label: "Lintang Bulan ekliptika",
                value: formatDegree(bestGeometry.moonLatitude, true),
            },
            {
                label: "Magnitudo penumbra",
                value: formatDecimal(bestGeometry.penumbralMagnitude, 3),
            },
            {
                label: "Magnitudo umbra",
                value: formatDecimal(bestGeometry.umbralMagnitude, 3),
            },
            {
                label: "Separasi pusat bayangan",
                value: formatDegree(bestGeometry.separation),
            },
            {
                label: "Tinggi Bulan di markaz",
                value: formatDegree(bestGeometry.moonAltitudeAtMarkaz, true),
            },
            {
                label: "Kontak penumbra",
                value:
                    pContacts.length >= 2
                        ? `${formatClock(pContacts[0], true)} - ${formatClock(pContacts[pContacts.length - 1], true)} ${getTimezoneLabelForDate(pContacts[0])}`
                        : "-",
            },
            {
                label: "Kontak umbra",
                value:
                    uContacts.length >= 2
                        ? `${formatClock(uContacts[0], true)} - ${formatClock(uContacts[uContacts.length - 1], true)} ${getTimezoneLabelForDate(uContacts[0])}`
                        : "-",
            },
        ],
    };
}

function calculateMonthEclipses(monthData, hisab) {
    const solar = calculateSolarEclipseForMonth(hisab);
    const lunar = calculateLunarEclipseForMonth(monthData);
    const events = [solar, lunar].filter((item) => item && item.potential);
    const rows = [];

    events.forEach((event) => {
        rows.push({ label: event.title, value: event.type });
        event.rows.forEach((row) =>
            rows.push({ label: `  ${row.label}`, value: row.value }),
        );
    });

    return { solar, lunar, events, rows };
}

function calculateGregorianMonthEclipses(monthData, relatedHijriMonths = []) {
    const lunar = calculateLunarEclipseForMonth(monthData);
    const solarEvents = [];
    const seenSolarKeys = new Set();
    const monthStartDate = monthData?.[0]
        ? parseDate(monthData[0].date.gregorian.date)
        : null;
    const monthEndDate = monthData?.length
        ? parseDate(monthData[monthData.length - 1].date.gregorian.date)
        : null;

    relatedHijriMonths.forEach((meta) => {
        const hisab =
            meta?.startDecisionHisab || meta?.monthEndDecisionHisab || null;
        const solar = calculateSolarEclipseForMonth(hisab);
        if (!solar || !solar.potential) return;

        const eventDate = solar.maxTime || solar.conjunction || null;
        if (monthStartDate && monthEndDate && eventDate) {
            const outsideGregorianMonth =
                dateDiffDays(monthStartDate, eventDate) < 0 ||
                dateDiffDays(eventDate, monthEndDate) < 0;
            if (outsideGregorianMonth) return;
        }

        const keyDate = solar.conjunction || solar.maxTime || null;
        const key =
            keyDate && !isNaN(keyDate.getTime())
                ? `${solar.kind}-${formatDateDDMMYYYY(keyDate)}`
                : `${solar.kind}-${solar.type}`;
        if (seenSolarKeys.has(key)) return;

        seenSolarKeys.add(key);
        solarEvents.push(solar);
    });

    const events = [...solarEvents, lunar].filter(
        (item) => item && item.potential,
    );
    const rows = [];
    events.forEach((event) => {
        rows.push({ label: event.title, value: event.type });
        event.rows.forEach((row) =>
            rows.push({ label: `  ${row.label}`, value: row.value }),
        );
    });

    return { solar: solarEvents[0] || null, solarEvents, lunar, events, rows };
}

function renderEclipseEventHTML(event) {
    if (!event || !event.potential) return "";
    const statusClass = /Tidak ada/i.test(event.type)
        ? "bg-gray-100 text-gray-600"
        : /Total|Cincin/i.test(event.type)
          ? "bg-red-100 text-red-700"
          : "bg-amber-100 text-amber-800";
    const rowsHTML = event.rows
        .map(
            (row) => `
                <div class="flex justify-between gap-3 border-b border-gray-100 pb-1">
                    <span class="font-semibold text-gray-600">${row.label}</span>
                    <span class="font-bold text-right text-gray-800">${row.value}</span>
                </div>
            `,
        )
        .join("");

    return `
                <div class="border border-gray-200 bg-white p-3 rounded-sm break-inside-avoid">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <h6 class="text-[11px] font-bold text-[#064e3b] uppercase tracking-widest">${event.title}</h6>
                        <span class="px-2 py-1 rounded text-[9px] font-bold ${statusClass}">${event.type}</span>
                    </div>
                    <div class="text-[10px] md:text-[11px] flex flex-col gap-1 text-gray-700">
                        ${rowsHTML}
                    </div>
                </div>
            `;
}

function renderEclipsePanelHTML(eclipseData) {
    if (!eclipseData || !eclipseData.events || eclipseData.events.length === 0)
        return "";
    return `
                <div class="mt-5 border-t border-gray-300 pt-4">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-200 pb-2 mb-3">
                        <h5 class="text-xs font-bold text-gray-800 uppercase tracking-widest">Hisab Gerhana Matahari & Bulan</h5>
                        <span class="text-[9px] font-bold text-[#064e3b] uppercase">Model numerik lokal</span>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        ${eclipseData.events.map(renderEclipseEventHTML).join("")}
                    </div>
                    <div class="mt-3 text-[9px] leading-snug text-gray-500 bg-white border border-gray-100 p-2">
                        Catatan akurasi: hisab ini memakai ephemeris Matahari-Bulan internal, koreksi topocentrik markaz, dan pencarian numerik kontak. Untuk publikasi resmi jalur gerhana, tetap cocokkan dengan data Besselian atau katalog gerhana resmi.
                    </div>
                </div>
            `;
}

// PROSES UTAMA
function startProcessing() {
    const overlay = document.getElementById("loading-overlay");
    const loadText = document.getElementById("loading-text");
    overlay.classList.remove("hidden");
    loadText.innerText = "Meminta Akses Lokasi (GPS)...";

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                currentLat = position.coords.latitude;
                currentLng = position.coords.longitude;
                currentMarkazName = "Lokasi GPS";
                isGpsLocation = true;
                try {
                    window.FalakNative?.syncLocation(
                        currentLat,
                        currentLng,
                        currentMarkazName,
                    );
                } catch (nativeError) {
                    console.warn(nativeError);
                }

                document.getElementById("display-location").innerText =
                    `Markaz jadwal sholat dan hisab awal: GPS Lat ${currentLat.toFixed(4)}, Lng ${currentLng.toFixed(4)} | Kalender: ${NATIONAL_MABIMS_POLICY}`;

                generateFullYear();
            },
            (error) => {
                console.warn(
                    "GPS Ditolak/Gagal. Menggunakan default Pasuruan.",
                    error,
                );
                currentLat = DEFAULT_MARKAZ.lat;
                currentLng = DEFAULT_MARKAZ.lng;
                currentMarkazName = DEFAULT_MARKAZ.name;
                isGpsLocation = false;

                document.getElementById("display-location").innerText =
                    `Markaz jadwal sholat dan hisab awal: Pasuruan default Lat ${currentLat.toFixed(4)}, Lng ${currentLng.toFixed(4)} | Kalender: ${NATIONAL_MABIMS_POLICY}`;

                generateFullYear();
            },
            { timeout: 5000 },
        );
    } else {
        currentLat = DEFAULT_MARKAZ.lat;
        currentLng = DEFAULT_MARKAZ.lng;
        currentMarkazName = DEFAULT_MARKAZ.name;
        isGpsLocation = false;

        document.getElementById("display-location").innerText =
            `Markaz jadwal sholat dan hisab awal: Pasuruan default | Kalender: ${NATIONAL_MABIMS_POLICY}`;

        generateFullYear();
    }
}

function buildCoordinatePrayerQuery() {
    const params = new URLSearchParams({
        latitude: Number(currentLat).toFixed(6),
        longitude: Number(currentLng).toFixed(6),
        method: String(PRAYER_METHOD_ID),
    });
    return params.toString();
}

function buildDefaultPrayerQuery() {
    const params = new URLSearchParams({
        city: "Pasuruan",
        country: "Indonesia",
        method: String(PRAYER_METHOD_ID),
    });
    return params.toString();
}

async function fetchApiData(url, label) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 8000);
    let response;
    try {
        response = await fetch(url, { signal: controller.signal });
    } catch (error) {
        if (error?.name === "AbortError")
            throw new Error(`${label} melewati batas waktu 8 detik.`);
        throw error;
    } finally {
        clearTimeout(timeoutId);
    }
    if (!response.ok) {
        throw new Error(`${label} gagal. HTTP ${response.status}`);
    }
    const data = await response.json();
    if (data.code && Number(data.code) !== 200) {
        throw new Error(
            `${label} gagal. ${data.status || "Respons API tidak valid"}`,
        );
    }
    if (!data.data) {
        throw new Error(`${label} gagal. Data kosong dari API.`);
    }
    return data.data;
}

function getTimezoneLabelForDate(dateObj) {
    const offsetHours = inferLocationUtcOffsetHours();
    if (offsetHours === 7) return "WIB";
    if (offsetHours === 8) return "WITA";
    if (offsetHours === 9) return "WIT";
    const sign = offsetHours >= 0 ? "+" : "-";
    return `UTC${sign}${String(Math.abs(offsetHours)).padStart(2, "0")}:00`;
}

function formatTimingForSchedule(dateObj) {
    if (!dateObj || isNaN(dateObj.getTime())) return "-";
    return `${formatClock(dateObj)} (${getTimezoneLabelForDate(dateObj)})`;
}

function addMinutes(dateObj, minutes) {
    return new Date(dateObj.getTime() + minutes * 60000);
}

function findAsrTime(dateObj, latitude, longitude, noon, sunset) {
    const shadowFactor = PRAYER_ASR_SHADOW_FACTOR;
    function asrAltitudeAt(timeObj) {
        const sun = sunApparentGeocentric(dateToJulianDay(timeObj));
        const phi = toRadians(latitude);
        const dec = toRadians(sun.dec);
        return toDegrees(
            Math.atan(1 / (shadowFactor + Math.tan(Math.abs(phi - dec)))),
        );
    }

    const startTime = noon.getTime();
    const endTime = sunset.getTime();
    const stepMs = 5 * 60 * 1000;
    let previousTime = startTime;
    let previousDiff =
        sunAltitudeDegAt(new Date(previousTime), latitude, longitude) -
        asrAltitudeAt(new Date(previousTime));

    for (let t = startTime + stepMs; t <= endTime; t += stepMs) {
        const currentDate = new Date(t);
        const currentDiff =
            sunAltitudeDegAt(currentDate, latitude, longitude) -
            asrAltitudeAt(currentDate);

        if (
            previousDiff === 0 ||
            currentDiff === 0 ||
            (previousDiff > 0 && currentDiff < 0) ||
            (previousDiff < 0 && currentDiff > 0)
        ) {
            let low = previousTime;
            let high = t;
            let lowDiff = previousDiff;

            for (let i = 0; i < 40; i++) {
                const mid = Math.floor((low + high) / 2);
                const midDate = new Date(mid);
                const midDiff =
                    sunAltitudeDegAt(midDate, latitude, longitude) -
                    asrAltitudeAt(midDate);
                if (
                    (lowDiff <= 0 && midDiff <= 0) ||
                    (lowDiff >= 0 && midDiff >= 0)
                ) {
                    low = mid;
                    lowDiff = midDiff;
                } else {
                    high = mid;
                }
            }
            return new Date(Math.floor((low + high) / 2));
        }

        previousTime = t;
        previousDiff = currentDiff;
    }

    return null;
}

function buildLocalPrayerTimings(dateObj, latitude, longitude) {
    const fajr = findSunAltitudeEvent(
        dateObj,
        latitude,
        longitude,
        -Math.abs(PRAYER_FAJR_ANGLE),
        0,
        8,
    );
    const sunrise = findSunAltitudeEvent(
        dateObj,
        latitude,
        longitude,
        STANDARD_SUNSET_ALTITUDE,
        3,
        9,
    );
    const noon = addMinutes(
        findSolarNoonPrecise(dateObj, latitude, longitude),
        PRAYER_DHUHR_OFFSET_MINUTES,
    );
    const sunset = findSunsetPrecise(dateObj, latitude, longitude);
    const asr = sunset
        ? findAsrTime(dateObj, latitude, longitude, noon, sunset)
        : null;
    const isha = findSunAltitudeEvent(
        dateObj,
        latitude,
        longitude,
        -Math.abs(PRAYER_ISHA_ANGLE),
        17,
        24,
    );
    const imsak = fajr ? addMinutes(fajr, -PRAYER_IMSAK_OFFSET_MINUTES) : null;

    const prayerTime = (key, instant) =>
        formatTimingForSchedule(
            instant && PRAYER_IHTIYAT_KEYS.has(key)
                ? addMinutes(instant, PRAYER_IHTIYAT_MINUTES)
                : instant,
        );
    return {
        Imsak: prayerTime("Imsak", imsak),
        Fajr: prayerTime("Fajr", fajr),
        Sunrise: prayerTime("Sunrise", sunrise),
        Dhuhr: prayerTime("Dhuhr", noon),
        Asr: prayerTime("Asr", asr),
        Maghrib: prayerTime("Maghrib", sunset),
        Isha: prayerTime("Isha", isha),
    };
}

function buildLocalGregorianCalendar(gYear, gMonth) {
    const lastDate = new Date(Number(gYear), Number(gMonth), 0).getDate();
    const rows = [];
    for (let day = 1; day <= lastDate; day++) {
        const dateObj = new Date(
            Number(gYear),
            Number(gMonth) - 1,
            day,
            0,
            0,
            0,
            0,
        );
        rows.push({
            timings: buildLocalPrayerTimings(
                dateObj,
                Number(currentLat),
                Number(currentLng),
            ),
            date: {
                gregorian: {
                    date: formatDateDDMMYYYY(dateObj),
                    format: "DD-MM-YYYY",
                    day: String(day).padStart(2, "0"),
                    weekday: {
                        en: [
                            "Sunday",
                            "Monday",
                            "Tuesday",
                            "Wednesday",
                            "Thursday",
                            "Friday",
                            "Saturday",
                        ][dateObj.getDay()],
                    },
                    month: {
                        number: Number(gMonth),
                        en: bulanMasehi[Number(gMonth) - 1],
                    },
                    year: String(gYear),
                    designation: { abbreviated: "AD", expanded: "Anno Domini" },
                },
            },
            meta: {
                source: "Local Accurate-Times-like engine",
                fajrAngle: PRAYER_FAJR_ANGLE,
                ishaAngle: PRAYER_ISHA_ANGLE,
                asrShadowFactor: PRAYER_ASR_SHADOW_FACTOR,
            },
        });
    }
    return rows;
}

async function fetchGregorianCalendarByLocation(gYear, gMonth) {
    if (USE_LOCAL_PRAYER_ENGINE) {
        return buildLocalGregorianCalendar(gYear, gMonth);
    }

    const coordUrl = `${API_BASE}/calendar/${gYear}/${gMonth}?${buildCoordinatePrayerQuery()}`;
    try {
        return await fetchApiData(coordUrl, `Masehi ${gYear}/${gMonth}`);
    } catch (coordinateError) {
        console.warn(
            "Gagal memakai endpoint kalender koordinat. Fallback ke Pasuruan.",
            coordinateError,
        );
        const cityUrl = `${API_BASE}/calendarByCity/${gYear}/${gMonth}?${buildDefaultPrayerQuery()}`;
        return await fetchApiData(
            cityUrl,
            `Masehi ${gYear}/${gMonth} Pasuruan`,
        );
    }
}

function getGregorianMonthCacheKey(gYear, gMonth) {
    return `${Number(gYear)}-${String(Number(gMonth)).padStart(2, "0")}-${currentLat.toFixed(4)}-${currentLng.toFixed(4)}`;
}

async function getPrayerDataForGregorianDate(dateObj) {
    const gYear = dateObj.getFullYear();
    const gMonth = dateObj.getMonth() + 1;
    const cacheKey = getGregorianMonthCacheKey(gYear, gMonth);

    if (!prayerCalendarCache.has(cacheKey)) {
        prayerCalendarCache.set(
            cacheKey,
            await fetchGregorianCalendarByLocation(gYear, gMonth),
        );
    }

    const targetDate = formatDateDDMMYYYY(dateObj);
    const rows = prayerCalendarCache.get(cacheKey);
    const found = rows.find(
        (item) => item?.date?.gregorian?.date === targetDate,
    );
    if (!found) {
        throw new Error(
            `Data jadwal sholat tanggal ${targetDate} tidak ditemukan.`,
        );
    }
    return JSON.parse(JSON.stringify(found));
}

function julianDayToGregorian(jd) {
    let z = Math.floor(jd + 0.5);
    let f = jd + 0.5 - z;
    let a = z;

    if (z >= 2299161) {
        const alpha = Math.floor((z - 1867216.25) / 36524.25);
        a = z + 1 + alpha - Math.floor(alpha / 4);
    }

    const b = a + 1524;
    const c = Math.floor((b - 122.1) / 365.25);
    const d = Math.floor(365.25 * c);
    const e = Math.floor((b - d) / 30.6001);
    const day = b - d - Math.floor(30.6001 * e) + f;
    const month = e < 14 ? e - 1 : e - 13;
    const year = month > 2 ? c - 4716 : c - 4715;

    return new Date(year, month - 1, Math.floor(day), 0, 0, 0, 0);
}

function getTabularHijriApproxFirstMuharram(year) {
    // Perkiraan kalender aritmetika. Hanya untuk titik awal pencarian.
    // Keputusan final tetap diambil dari hisab MABIMS pada fungsi findMabimsStartNear().
    const jd =
        1 +
        Math.ceil(29.5 * 0) +
        (year - 1) * 354 +
        Math.floor((3 + 11 * year) / 30) +
        1948439.5 -
        1;
    return julianDayToGregorian(jd);
}

async function getApproxFirstMuharram(year) {
    // API ini hanya dipakai sebagai tanggal pendekatan agar pencarian awal Muharram cepat.
    // Jika API gagal, skrip memakai perkiraan aritmetika lalu tetap divalidasi dengan hisab MABIMS.
    const hDate = `01-01-${year}`;
    try {
        const conversion = await fetchApiData(
            `${API_BASE}/hToG/${hDate}`,
            `Konversi pendekatan awal ${year} H`,
        );
        return parseDate(conversion.gregorian.date);
    } catch (error) {
        console.warn(
            "Konversi API Hijriyah gagal. Memakai perkiraan tabular sebagai titik awal.",
            error,
        );
        return getTabularHijriApproxFirstMuharram(year);
    }
}

function getHijriWeekdayObject(dateObj) {
    const weekdayEn = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
    ];
    const weekdayAr = [
        "الأحد",
        "الإثنين",
        "الثلاثاء",
        "الأربعاء",
        "الخميس",
        "الجمعة",
        "السبت",
    ];
    return {
        en: weekdayEn[dateObj.getDay()],
        ar: weekdayAr[dateObj.getDay()],
        id: hariIndo[dateObj.getDay()],
    };
}

function isValidMabimsHilal(hisab) {
    return (
        !!hisab &&
        hisab.moonAgeHours >= 0 &&
        hisab.moonAltitude >= MABIMS_MIN_ALTITUDE &&
        hisab.elongation >= MABIMS_MIN_ELONGATION
    );
}

function findMabimsStartNear(approxFirstDate) {
    const candidates = [];

    // Cari awal Muharram di sekitar tanggal pendekatan.
    // Titik awal ini hanya dipakai sebagai jangkar tahun.
    // Bulan 2 sampai 12 dibangun dari keputusan transisi tanggal 29 bulan sebelumnya.
    for (let offset = -8; offset <= 8; offset++) {
        const candidateStart = addDays(approxFirstDate, offset);
        const hisab = evaluateMabimsWithPriority(addDays(candidateStart, -1));
        const previousHisab = evaluateMabimsWithPriority(
            addDays(candidateStart, -2),
        );

        const isFirstMabimsDay =
            isValidMabimsHilal(hisab) && !isValidMabimsHilal(previousHisab);
        if (isFirstMabimsDay) {
            candidates.push({
                start: candidateStart,
                hisab,
                distance: Math.abs(
                    dateDiffDays(approxFirstDate, candidateStart),
                ),
            });
        }
    }

    if (candidates.length === 0) {
        const validCandidates = [];
        for (let offset = -8; offset <= 8; offset++) {
            const candidateStart = addDays(approxFirstDate, offset);
            const hisab = evaluateMabimsWithPriority(
                addDays(candidateStart, -1),
            );
            if (isValidMabimsHilal(hisab)) {
                validCandidates.push({
                    start: candidateStart,
                    hisab,
                    distance: Math.abs(
                        dateDiffDays(approxFirstDate, candidateStart),
                    ),
                });
            }
        }

        if (validCandidates.length === 0) {
            throw new Error(
                `Tidak menemukan awal bulan MABIMS di sekitar ${formatDateDDMMYYYY(approxFirstDate)}.`,
            );
        }

        validCandidates.sort(
            (a, b) => a.distance - b.distance || a.start - b.start,
        );
        return validCandidates[0].start;
    }

    candidates.sort((a, b) => a.distance - b.distance || a.start - b.start);
    return candidates[0].start;
}

function formatGregorianDateLong(dateObj) {
    if (!dateObj || isNaN(dateObj.getTime())) return "-";
    return `${hariIndo[dateObj.getDay()]} ${getPasaran(formatDateDDMMYYYY(dateObj))}, ${dateObj.getDate()} ${bulanMasehi[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
}

function buildMonthTransitionDecision(
    monthStartDate,
    monthNumber = null,
    year = null,
) {
    // Keputusan panjang bulan harus memakai data gurub tanggal 29 bulan berjalan.
    // Panel awal bulan berikutnya juga harus memakai data ini.
    // Perbaikan ini mencegah kasus Safar tampil seolah dimulai dari hisab H-1,
    // padahal Muharram bisa saja sudah ditetapkan istikmal 30 hari.
    const obsDate29 = addDays(monthStartDate, 28);
    const nationalHisab29 = evaluateMabimsWithPriority(obsDate29);
    const monthLength = nationalHisab29.mabimsOk ? 29 : 30;
    const nextMonthStart = addDays(monthStartDate, monthLength);
    const currentMonthName = monthNumber
        ? getHijriMonthLabel(monthNumber, "id")
        : "bulan berjalan";
    const nextMonthNumber = monthNumber ? (monthNumber % 12) + 1 : null;
    const nextMonthName = nextMonthNumber
        ? getHijriMonthLabel(nextMonthNumber, "id")
        : "bulan berikutnya";
    const decisionMode = nationalHisab29.mabimsOk
        ? "Rukyat MABIMS"
        : "Istikmal 30 hari";
    const startExplanation = nationalHisab29.mabimsOk
        ? `Awal ${nextMonthName} ditetapkan karena hilal ${currentMonthName} memenuhi ${MABIMS_CRITERIA_LABEL} pada gurub tanggal 29.`
        : `Awal ${nextMonthName} ditetapkan setelah ${currentMonthName} disempurnakan 30 hari karena hilal belum memenuhi ${MABIMS_CRITERIA_LABEL} pada gurub tanggal 29.`;

    return {
        ...nationalHisab29,
        monthNumber,
        year,
        monthLength,
        nextMonthStart,
        observedHijriDay: 29,
        observedGregorianDate: obsDate29,
        observedGregorianText: formatGregorianDateLong(obsDate29),
        decisionMode,
        startExplanation,
        currentMonthName,
        nextMonthName,
        decisionForNextMonthNumber: nextMonthNumber,
        decisionForNextMonthName: nextMonthName,
        decisionText: `${nationalHisab29.decisionText} ${startExplanation}`,
    };
}

function getMabimsMonthLength(monthStartDate) {
    return buildMonthTransitionDecision(monthStartDate).monthLength;
}

function inferStartDecisionForFirstMonth(monthStartDate) {
    // Muharram adalah bulan pertama dalam paket tahun yang dibuat.
    // Data transisi dari Zulhijah tahun sebelumnya tidak tersedia di loop,
    // sehingga sistem menebak secara aman dari dua kemungkinan:
    // 1) bulan sebelumnya 29 hari bila H-1 memenuhi MABIMS;
    // 2) bulan sebelumnya istikmal bila H-2 belum memenuhi MABIMS.
    const decisionHMinus1 = evaluateMabimsWithPriority(
        addDays(monthStartDate, -1),
    );
    if (isValidMabimsHilal(decisionHMinus1)) {
        return {
            ...decisionHMinus1,
            monthLength: 29,
            observedHijriDay: 29,
            observedGregorianDate: addDays(monthStartDate, -1),
            observedGregorianText: formatGregorianDateLong(
                addDays(monthStartDate, -1),
            ),
            decisionMode: "Rukyat MABIMS",
            startExplanation: `Awal Muharram ditetapkan karena hilal Zulhijah memenuhi ${MABIMS_CRITERIA_LABEL} pada gurub tanggal 29.`,
            decisionForNextMonthNumber: 1,
            decisionForNextMonthName: "Muharram",
        };
    }

    const decisionHMinus2 = evaluateMabimsWithPriority(
        addDays(monthStartDate, -2),
    );
    return {
        ...decisionHMinus2,
        monthLength: 30,
        observedHijriDay: 29,
        observedGregorianDate: addDays(monthStartDate, -2),
        observedGregorianText: formatGregorianDateLong(
            addDays(monthStartDate, -2),
        ),
        decisionMode: "Istikmal 30 hari",
        startExplanation: `Awal Muharram ditetapkan setelah Zulhijah disempurnakan 30 hari karena hilal belum memenuhi ${MABIMS_CRITERIA_LABEL} pada gurub tanggal 29.`,
        decisionForNextMonthNumber: 1,
        decisionForNextMonthName: "Muharram",
        decisionText: `${decisionHMinus2.decisionText} Awal Muharram ditetapkan melalui istikmal.`,
    };
}

function validateMabimsYearBuild(yearBuild, year) {
    const starts = yearBuild.starts || [];
    const startDecisions = yearBuild.startDecisions || [];
    const monthEndDecisions = yearBuild.monthEndDecisions || [];
    const monthLengths = yearBuild.monthLengths || [];

    for (let month = 1; month <= 12; month++) {
        const currentStart = starts[month];
        const nextStart = starts[month + 1];
        const monthName = getHijriMonthLabel(month, "id");

        if (
            !currentStart ||
            !nextStart ||
            isNaN(currentStart.getTime()) ||
            isNaN(nextStart.getTime())
        ) {
            throw new Error(
                `Validasi gagal. Tanggal awal ${monthName} ${year} H tidak lengkap.`,
            );
        }

        const actualLength = dateDiffDays(currentStart, nextStart);
        if (
            actualLength !== monthLengths[month] ||
            actualLength < 29 ||
            actualLength > 30
        ) {
            throw new Error(
                `Validasi gagal. Panjang ${monthName} ${year} H terbaca ${actualLength} hari.`,
            );
        }

        const endDecision = monthEndDecisions[month];
        const expectedObservation = addDays(currentStart, 28);
        if (
            !endDecision ||
            !endDecision.observedGregorianDate ||
            dateDiffDays(
                expectedObservation,
                endDecision.observedGregorianDate,
            ) !== 0
        ) {
            throw new Error(
                `Validasi gagal. Observasi akhir ${monthName} harus jatuh pada tanggal 29 Hijriyah.`,
            );
        }

        if (
            !endDecision.nextMonthStart ||
            dateDiffDays(nextStart, endDecision.nextMonthStart) !== 0
        ) {
            throw new Error(
                `Validasi gagal. Awal bulan setelah ${monthName} tidak sesuai keputusan tanggal 29.`,
            );
        }

        if (month > 1) {
            const previousDecision = monthEndDecisions[month - 1];
            const startDecision = startDecisions[month];
            if (!previousDecision || !startDecision) {
                throw new Error(
                    `Validasi gagal. Keputusan awal ${monthName} tidak tersedia.`,
                );
            }

            if (
                dateDiffDays(
                    previousDecision.observedGregorianDate,
                    startDecision.observedGregorianDate,
                ) !== 0
            ) {
                throw new Error(
                    `Validasi gagal. Panel awal ${monthName} tidak memakai observasi bulan sebelumnya.`,
                );
            }

            if (
                dateDiffDays(currentStart, previousDecision.nextMonthStart) !==
                0
            ) {
                throw new Error(
                    `Validasi gagal. Tanggal 1 ${monthName} tidak sama dengan hasil transisi bulan sebelumnya.`,
                );
            }
        }
    }

    return true;
}

async function buildMabimsYearStarts(year) {
    const approxFirstMuharram = await getApproxFirstMuharram(year);
    const starts = [null];
    const startDecisions = [null];
    const monthEndDecisions = [null];
    const monthLengths = [null];

    starts[1] = findMabimsStartNear(approxFirstMuharram);

    if (!starts[1] || isNaN(starts[1].getTime())) {
        throw new Error("Awal Muharram tidak valid dari hasil hisab MABIMS.");
    }

    startDecisions[1] = inferStartDecisionForFirstMonth(starts[1]);

    for (let month = 1; month <= 12; month++) {
        const decision = buildMonthTransitionDecision(
            starts[month],
            month,
            year,
        );
        const length = decision.monthLength;
        if (length !== 29 && length !== 30) {
            throw new Error(`Panjang bulan ${month}-${year} tidak valid.`);
        }

        monthEndDecisions[month] = decision;
        monthLengths[month] = length;
        starts[month + 1] = decision.nextMonthStart;
        startDecisions[month + 1] = {
            ...decision,
            decisionForNextMonthNumber: (month % 12) + 1,
            decisionForNextMonthName: getHijriMonthLabel(
                (month % 12) + 1,
                "id",
            ),
        };
    }

    const yearBuild = {
        starts,
        startDecisions,
        monthEndDecisions,
        monthLengths,
    };

    validateMabimsYearBuild(yearBuild, year);
    return yearBuild;
}

async function buildMabimsHijriMonth(
    year,
    month,
    monthStartDate,
    nextMonthStartDate,
) {
    const length = dateDiffDays(monthStartDate, nextMonthStartDate);
    if (length < 29 || length > 30) {
        throw new Error(
            `Panjang bulan ${month}-${year} tidak valid: ${length} hari.`,
        );
    }

    const hijriMonth = HIJRI_MONTHS[month];
    const rows = [];

    for (let dayIndex = 0; dayIndex < length; dayIndex++) {
        const gregorianDate = addDays(monthStartDate, dayIndex);
        const base = await getPrayerDataForGregorianDate(gregorianDate);
        const hijriDay = dayIndex + 1;
        const gregorianDateText = formatDateDDMMYYYY(gregorianDate);

        // Data Masehi dan jadwal sholat tetap dari API berdasarkan tanggal Masehi hasil hisab.
        base.date = base.date || {};
        base.date.gregorian = base.date.gregorian || {};
        base.date.gregorian.date = gregorianDateText;
        base.date.gregorian.day = String(gregorianDate.getDate()).padStart(
            2,
            "0",
        );
        base.date.gregorian.month = base.date.gregorian.month || {};
        base.date.gregorian.month.number = gregorianDate.getMonth() + 1;
        base.date.gregorian.month.en = bulanMasehi[gregorianDate.getMonth()];
        base.date.gregorian.year = String(gregorianDate.getFullYear());

        // Data Hijriyah tidak memakai konversi API.
        // Nomor hari, bulan, dan tahun murni mengikuti monthStartDate hasil hisab MABIMS.
        base.date.hijri = {
            date: `${String(hijriDay).padStart(2, "0")}-${String(month).padStart(2, "0")}-${year}`,
            format: "DD-MM-YYYY",
            day: String(hijriDay),
            weekday: getHijriWeekdayObject(gregorianDate),
            month: {
                number: month,
                en: hijriMonth.en,
                id: hijriMonth.id,
                sheet: hijriMonth.sheet,
                ar: hijriMonth.ar,
            },
            year: String(year),
            designation: {
                abbreviated: "H",
                expanded: "Hijriyah",
            },
            source: "Hisab MABIMS 3°-6,4°",
        };

        rows.push(base);
    }

    return rows;
}

async function generateFullYear() {
    updateCalendarModeUI();
    const mode = getCalendarMode();
    const yearInput = parseInt(document.getElementById("hijriYear").value, 10);
    if (mode === CALENDAR_MODE_GREGORIAN) {
        await generateGregorianFullYear(yearInput);
    } else {
        await generateHijriFullYear(yearInput);
    }
}

async function generateHijriFullYear(yearInput) {
    if (!Number.isInteger(yearInput) || yearInput < 1) {
        alert("Masukkan tahun Hijriyah yang valid.");
        document.getElementById("loading-overlay").classList.add("hidden");
        return;
    }
    document.getElementById("display-calendar-title").textContent =
        "Kalender Hijriyah";
    document.getElementById("display-hijri-year").textContent =
        `${yearInput} H`;

    const wrapper = document.getElementById("months-wrapper");
    const loadText = document.getElementById("loading-text");

    wrapper.innerHTML = "";
    generatedMonthsData = [];
    prayerCalendarCache.clear();
    loadText.innerText =
        "Menghitung awal bulan, ephemeris, dan potensi gerhana dengan prioritas GPS atau Pasuruan...";

    try {
        const yearBuild = await buildMabimsYearStarts(yearInput);
        const monthStarts = yearBuild.starts;
        for (let m = 1; m <= 12; m++) {
            loadText.innerText = `Menyusun ${getHijriMonthLabel(m, "id")} ${yearInput} H sesuai keputusan MABIMS prioritas markaz...`;
            const monthData = await buildMabimsHijriMonth(
                yearInput,
                m,
                monthStarts[m],
                monthStarts[m + 1],
            );
            monthData.startDecisionHisab = yearBuild.startDecisions[m];
            monthData.monthEndDecisionHisab = yearBuild.monthEndDecisions[m];
            monthData.monthLength = yearBuild.monthLengths[m];
            if (monthData && monthData.length > 0) {
                if (m === 1) {
                    const startMasehi = parseDate(
                        monthData[0].date.gregorian.date,
                    ).getFullYear();
                    document.getElementById(
                        "display-gregorian-year",
                    ).textContent = `${startMasehi} M`;
                } else if (m === 12) {
                    const startMasehi = document
                        .getElementById("display-gregorian-year")
                        .textContent.split(" ")[0];
                    const endMasehi = parseDate(
                        monthData[monthData.length - 1].date.gregorian.date,
                    ).getFullYear();
                    if (startMasehi != endMasehi)
                        document.getElementById(
                            "display-gregorian-year",
                        ).textContent = `${startMasehi} - ${endMasehi} M`;
                }

                const monthHTML = createMonthLayout(monthData, m);
                wrapper.appendChild(monthHTML);

                if (m < 12) {
                    const divider = document.createElement("div");
                    divider.className = "divider page-break";
                    wrapper.appendChild(divider);
                }
            }
        }
    } catch (error) {
        console.error("Hijri generation error:", error);
        alert(
            `Gagal mengambil atau memvalidasi data. ${error.message || "Periksa koneksi internet."}`,
        );
    } finally {
        document.getElementById("loading-overlay").classList.add("hidden");
    }
}

function estimateHijriYearFromGregorianYear(gYear) {
    return Math.floor(((Number(gYear) - 622) * 33) / 32) + 1;
}

function getMabimsYearCacheKey(hYear) {
    return `${Number(hYear)}-${Number(currentLat).toFixed(4)}-${Number(currentLng).toFixed(4)}`;
}

async function getMabimsYearBuildCached(hYear) {
    const key = getMabimsYearCacheKey(hYear);
    if (!mabimsYearBuildCache.has(key)) {
        mabimsYearBuildCache.set(key, await buildMabimsYearStarts(hYear));
    }
    return mabimsYearBuildCache.get(key);
}

function buildHijriObjectFromMabims(dateObj, hYear, hMonth, hDay) {
    const hijriMonth = HIJRI_MONTHS[hMonth];
    return {
        date: `${String(hDay).padStart(2, "0")}-${String(hMonth).padStart(2, "0")}-${hYear}`,
        format: "DD-MM-YYYY",
        day: String(hDay),
        weekday: getHijriWeekdayObject(dateObj),
        month: {
            number: hMonth,
            en: hijriMonth.en,
            id: hijriMonth.id,
            sheet: hijriMonth.sheet,
            ar: hijriMonth.ar,
        },
        year: String(hYear),
        designation: {
            abbreviated: "H",
            expanded: "Hijriyah",
        },
        source: "Hisab MABIMS 3°-6,4°",
    };
}

async function buildMabimsHijriLookupForGregorianYear(gYear) {
    const startDate = new Date(Number(gYear), 0, 1, 0, 0, 0, 0);
    const endDate = new Date(Number(gYear), 11, 31, 0, 0, 0, 0);
    const lookup = new Map();
    const estimate = estimateHijriYearFromGregorianYear(gYear);
    const years = [estimate - 1, estimate, estimate + 1, estimate + 2].filter(
        (year, index, arr) => year > 0 && arr.indexOf(year) === index,
    );

    for (const hYear of years) {
        const yearBuild = await getMabimsYearBuildCached(hYear);
        for (let hMonth = 1; hMonth <= 12; hMonth++) {
            const monthStart = yearBuild.starts[hMonth];
            const nextMonthStart = yearBuild.starts[hMonth + 1];
            const monthLength = dateDiffDays(monthStart, nextMonthStart);

            for (let dayIndex = 0; dayIndex < monthLength; dayIndex++) {
                const gDate = addDays(monthStart, dayIndex);
                if (
                    dateDiffDays(startDate, gDate) < 0 ||
                    dateDiffDays(gDate, endDate) < 0
                )
                    continue;

                const hDay = dayIndex + 1;
                const key = formatDateDDMMYYYY(gDate);
                lookup.set(key, {
                    hYear,
                    hMonth,
                    hDay,
                    hijri: buildHijriObjectFromMabims(
                        gDate,
                        hYear,
                        hMonth,
                        hDay,
                    ),
                    monthStartDate: monthStart,
                    nextMonthStartDate: nextMonthStart,
                    monthLength,
                    startDecisionHisab: yearBuild.startDecisions[hMonth],
                    monthEndDecisionHisab: yearBuild.monthEndDecisions[hMonth],
                });
            }
        }
    }

    return lookup;
}

async function buildMabimsGregorianMonth(gYear, gMonth, hijriLookup) {
    const lastDate = new Date(Number(gYear), Number(gMonth), 0).getDate();
    const rows = [];

    for (let day = 1; day <= lastDate; day++) {
        const gregorianDate = new Date(
            Number(gYear),
            Number(gMonth) - 1,
            day,
            0,
            0,
            0,
            0,
        );
        const gregorianDateText = formatDateDDMMYYYY(gregorianDate);
        const hijriMeta = hijriLookup.get(gregorianDateText);
        if (!hijriMeta) {
            throw new Error(
                `Data Hijriyah MABIMS untuk ${gregorianDateText} belum ditemukan.`,
            );
        }

        const base = await getPrayerDataForGregorianDate(gregorianDate);
        base.date = base.date || {};
        base.date.gregorian = base.date.gregorian || {};
        base.date.gregorian.date = gregorianDateText;
        base.date.gregorian.day = String(day).padStart(2, "0");
        base.date.gregorian.weekday = base.date.gregorian.weekday || {
            en: [
                "Sunday",
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday",
            ][gregorianDate.getDay()],
        };
        base.date.gregorian.month = base.date.gregorian.month || {};
        base.date.gregorian.month.number = Number(gMonth);
        base.date.gregorian.month.en = bulanMasehi[Number(gMonth) - 1];
        base.date.gregorian.year = String(gYear);
        base.date.gregorian.designation = {
            abbreviated: "M",
            expanded: "Masehi",
        };
        base.date.hijri = hijriMeta.hijri;
        base.hijriMeta = hijriMeta;
        rows.push(base);
    }

    return rows;
}

async function generateGregorianFullYear(yearInput) {
    if (!Number.isInteger(yearInput) || yearInput < 1) {
        alert("Masukkan tahun Masehi yang valid.");
        document.getElementById("loading-overlay").classList.add("hidden");
        return;
    }

    document.getElementById("display-calendar-title").textContent =
        "Kalender Masehi";
    document.getElementById("display-hijri-year").textContent =
        `${yearInput} M`;
    document.getElementById("display-gregorian-year").textContent =
        "Konversi Hijriyah MABIMS Otomatis";

    const wrapper = document.getElementById("months-wrapper");
    const loadText = document.getElementById("loading-text");

    wrapper.innerHTML = "";
    generatedMonthsData = [];
    prayerCalendarCache.clear();
    loadText.innerText =
        "Membangun peta Hijriyah MABIMS untuk kalender Masehi...";

    try {
        const hijriLookup =
            await buildMabimsHijriLookupForGregorianYear(yearInput);
        for (let m = 1; m <= 12; m++) {
            loadText.innerText = `Menyusun ${bulanMasehi[m - 1]} ${yearInput} M dengan prioritas tanggal Masehi...`;
            const monthData = await buildMabimsGregorianMonth(
                yearInput,
                m,
                hijriLookup,
            );
            const monthHTML = createGregorianMonthLayout(monthData, m);
            wrapper.appendChild(monthHTML);

            if (m < 12) {
                const divider = document.createElement("div");
                divider.className = "divider page-break";
                wrapper.appendChild(divider);
            }
        }
    } catch (error) {
        console.error("Gregorian generation error:", error);
        alert(
            `Gagal membuat kalender Masehi. ${error.message || "Periksa koneksi internet."}`,
        );
    } finally {
        document.getElementById("loading-overlay").classList.add("hidden");
    }
}

// =============================================================
// FALAKAPP PRO UI CONTROLLER 2.0
// =============================================================
const APP_MONTHS = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
];
const APP_MONTHS_SHORT = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "Mei",
    "Jun",
    "Jul",
    "Agu",
    "Sep",
    "Okt",
    "Nov",
    "Des",
];
const APP_DAYS = ["Ahad", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
const APP_DAYS_AR = [
    "الأحد",
    "الإثنين",
    "الثلاثاء",
    "الأربعاء",
    "الخميس",
    "الجمعة",
    "السبت",
];
const PRAYER_LABELS = [
    ["Imsak", "Imsak", "◒"],
    ["Fajr", "Subuh", "✦"],
    ["Sunrise", "Terbit", "☀"],
    ["Dhuhr", "Zuhur", "◉"],
    ["Asr", "Asar", "◔"],
    ["Maghrib", "Magrib", "◑"],
    ["Isha", "Isya", "☾"],
];

// Hari libur nasional dan cuti bersama resmi 2026.
// SKB Menteri Agama 1497/2025, Menteri Ketenagakerjaan 2/2025,
// dan Menteri PANRB 5/2025.
const OFFICIAL_PUBLIC_HOLIDAYS = {
    2026: {
        "2026-01-01": [
            {
                id: "new-year",
                name: "Tahun Baru 2026 Masehi",
                type: "national",
            },
        ],
        "2026-01-16": [
            {
                id: "isra-mikraj",
                name: "Isra Mikraj Nabi Muhammad saw.",
                type: "national",
            },
        ],
        "2026-02-16": [
            {
                id: "imlek-leave",
                name: "Cuti Bersama Tahun Baru Imlek 2577 Kongzili",
                type: "collective",
            },
        ],
        "2026-02-17": [
            {
                id: "imlek",
                name: "Tahun Baru Imlek 2577 Kongzili",
                type: "national",
            },
        ],
        "2026-03-18": [
            {
                id: "nyepi-leave",
                name: "Cuti Bersama Hari Suci Nyepi",
                type: "collective",
            },
        ],
        "2026-03-19": [
            {
                id: "nyepi",
                name: "Hari Suci Nyepi Tahun Baru Saka 1948",
                type: "national",
            },
        ],
        "2026-03-20": [
            {
                id: "eid-fitr-leave-1",
                name: "Cuti Bersama Idulfitri 1447 H",
                type: "collective",
            },
        ],
        "2026-03-21": [
            { id: "eid-fitr", name: "Idulfitri 1447 H", type: "national" },
        ],
        "2026-03-22": [
            {
                id: "eid-fitr-day-2",
                name: "Idulfitri 1447 H Hari Kedua",
                type: "national",
            },
        ],
        "2026-03-23": [
            {
                id: "eid-fitr-leave-2",
                name: "Cuti Bersama Idulfitri 1447 H",
                type: "collective",
            },
        ],
        "2026-03-24": [
            {
                id: "eid-fitr-leave-3",
                name: "Cuti Bersama Idulfitri 1447 H",
                type: "collective",
            },
        ],
        "2026-04-03": [
            {
                id: "good-friday",
                name: "Wafat Yesus Kristus",
                type: "national",
            },
        ],
        "2026-04-05": [
            {
                id: "easter",
                name: "Kebangkitan Yesus Kristus (Paskah)",
                type: "national",
            },
        ],
        "2026-05-01": [
            {
                id: "labour-day",
                name: "Hari Buruh Internasional",
                type: "national",
            },
        ],
        "2026-05-14": [
            {
                id: "ascension",
                name: "Kenaikan Yesus Kristus",
                type: "national",
            },
        ],
        "2026-05-15": [
            {
                id: "ascension-leave",
                name: "Cuti Bersama Kenaikan Yesus Kristus",
                type: "collective",
            },
        ],
        "2026-05-27": [
            { id: "eid-adha", name: "Iduladha 1447 H", type: "national" },
        ],
        "2026-05-28": [
            {
                id: "eid-adha-leave",
                name: "Cuti Bersama Iduladha 1447 H",
                type: "collective",
            },
        ],
        "2026-05-31": [
            {
                id: "waisak",
                name: "Hari Raya Waisak 2570 BE",
                type: "national",
            },
        ],
        "2026-06-01": [
            { id: "pancasila", name: "Hari Lahir Pancasila", type: "national" },
        ],
        "2026-06-16": [
            {
                id: "islamic-new-year",
                name: "1 Muharam Tahun Baru Islam 1448 H",
                type: "national",
            },
        ],
        "2026-08-17": [
            {
                id: "independence",
                name: "Proklamasi Kemerdekaan Republik Indonesia",
                type: "national",
            },
        ],
        "2026-08-25": [
            {
                id: "maulid",
                name: "Maulid Nabi Muhammad saw.",
                type: "national",
            },
        ],
        "2026-12-24": [
            {
                id: "christmas-leave",
                name: "Cuti Bersama Kelahiran Yesus Kristus",
                type: "collective",
            },
        ],
        "2026-12-25": [
            {
                id: "christmas",
                name: "Kelahiran Yesus Kristus",
                type: "national",
            },
        ],
    },
};
const FIXED_PUBLIC_HOLIDAYS = {
    "01-01": { id: "new-year", name: "Tahun Baru Masehi" },
    "05-01": { id: "labour-day", name: "Hari Buruh Internasional" },
    "06-01": { id: "pancasila", name: "Hari Lahir Pancasila" },
    "08-17": {
        id: "independence",
        name: "Proklamasi Kemerdekaan Republik Indonesia",
    },
    "12-25": { id: "christmas", name: "Kelahiran Yesus Kristus" },
};
const ISLAMIC_EVENT_RULES = {
    "1-1": [
        {
            id: "islamic-new-year",
            name: "Tahun Baru Hijriyah",
            importance: "major",
        },
    ],
    "1-10": [{ id: "ashura", name: "Hari Asyura", importance: "major" }],
    "3-12": [
        {
            id: "maulid",
            name: "Maulid Nabi Muhammad saw.",
            importance: "major",
        },
    ],
    "7-27": [
        {
            id: "isra-mikraj",
            name: "Isra Mikraj Nabi Muhammad saw.",
            importance: "major",
        },
    ],
    "8-15": [{ id: "nisfu-shaban", name: "Nisfu Syaban", importance: "major" }],
    "9-1": [
        {
            id: "ramadan-start",
            name: "Awal Puasa Ramadan",
            importance: "major",
        },
    ],
    "9-17": [
        { id: "nuzulul-quran", name: "Nuzulul Quran", importance: "major" },
    ],
    "9-21": [
        {
            id: "ramadan-odd-21",
            name: "Malam Ganjil Ramadan ke-21",
            importance: "routine",
        },
    ],
    "9-23": [
        {
            id: "ramadan-odd-23",
            name: "Malam Ganjil Ramadan ke-23",
            importance: "routine",
        },
    ],
    "9-25": [
        {
            id: "ramadan-odd-25",
            name: "Malam Ganjil Ramadan ke-25",
            importance: "routine",
        },
    ],
    "9-27": [
        {
            id: "ramadan-odd-27",
            name: "Malam Ganjil Ramadan ke-27",
            importance: "major",
        },
    ],
    "9-29": [
        {
            id: "ramadan-odd-29",
            name: "Malam Ganjil Ramadan ke-29",
            importance: "routine",
        },
    ],
    "10-1": [
        { id: "eid-fitr", name: "Idulfitri 1 Syawal", importance: "major" },
    ],
    "10-2": [
        {
            id: "eid-fitr-day-2",
            name: "Hari Kedua Idulfitri",
            importance: "major",
        },
    ],
    "12-8": [{ id: "tarwiyah", name: "Hari Tarwiyah", importance: "major" }],
    "12-9": [{ id: "arafah", name: "Puasa Arafah", importance: "major" }],
    "12-10": [{ id: "eid-adha", name: "Iduladha", importance: "major" }],
    "12-11": [{ id: "tashriq-11", name: "Hari Tasyrik", importance: "major" }],
    "12-12": [{ id: "tashriq-12", name: "Hari Tasyrik", importance: "major" }],
    "12-13": [{ id: "tashriq-13", name: "Hari Tasyrik", importance: "major" }],
};
const appState = {
    today: new Date(),
    selectedDate: new Date(),
    locationName: "Pasuruan default",
    calendarMode: "hijri",
    viewHYear: null,
    viewHMonth: null,
    viewGDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
    currentHijri: null,
    currentCalendarRows: [],
    prayerTimes: null,
    nextPrayer: null,
    heading: null,
    orientationActive: false,
    lastNotificationAt: 0,
    initialized: false,
    currentMonthData: null,
    currentMonthHisab: null,
    almanacFilter: "all",
    annualLoaded: false,
};

// Adapter FalakApp Pro untuk mesin kalender dari script lampiran.
// Cache dipisahkan per tahun dan koordinat supaya perpindahan bulan tetap cepat.
const falakAppGregorianLookupCache = new Map();

const __ijtimaOriginal = findIjtima;
const __ijtimaCache = new Map();
findIjtima = function (baseDate) {
    const key = `${baseDate.getFullYear()}-${baseDate.getMonth() + 1}-${baseDate.getDate()}`;
    if (!__ijtimaCache.has(key))
        __ijtimaCache.set(key, __ijtimaOriginal(baseDate));
    return new Date(__ijtimaCache.get(key).getTime());
};
const __sunMoonOriginal = calculateSunMoonDataAtSunset;
const __sunMoonCache = new Map();
calculateSunMoonDataAtSunset = function (obsDate, markaz = null) {
    const lat = Number(markaz?.lat ?? currentLat),
        lng = Number(markaz?.lng ?? currentLng),
        name = markaz?.name || "Markaz Aktif";
    const key = `${formatDateDDMMYYYY(obsDate)}|${lat.toFixed(4)}|${lng.toFixed(4)}`;
    if (!__sunMoonCache.has(key))
        __sunMoonCache.set(key, __sunMoonOriginal(obsDate, { name, lat, lng }));
    return { ...__sunMoonCache.get(key) };
};
const __mabimsEvalCache = new Map();
evaluateMabimsWithPriority = function (obsDate) {
    const cacheKey = `${formatDateDDMMYYYY(obsDate)}|${Number(currentLat).toFixed(4)}|${Number(currentLng).toFixed(4)}`;
    if (__mabimsEvalCache.has(cacheKey)) return __mabimsEvalCache.get(cacheKey);
    const primaryMarkaz = {
        name: currentMarkazName || DEFAULT_MARKAZ.name,
        lat: Number(currentLat),
        lng: Number(currentLng),
    };
    const primaryResult = calculateSunMoonDataAtSunset(obsDate, primaryMarkaz);
    if (isValidMabimsHilal(primaryResult)) {
        const out = {
            ...primaryResult,
            mabimsOk: true,
            decisiveMarkazName: primaryResult.markazName,
            primaryMarkazName: primaryResult.markazName,
            primaryResult,
            passedCities: [primaryResult],
            passedCityNames: [primaryResult.markazName],
            passedCityCount: 1,
            checkedCityCount: 1,
            allMarkazResults: [primaryResult],
            nationalPolicy: NATIONAL_MABIMS_POLICY,
            decisionText: `Tidak istikmal. ${primaryResult.markazName} memenuhi ${MABIMS_CRITERIA_LABEL}.`,
        };
        __mabimsEvalCache.set(cacheKey, out);
        return out;
    }
    const checked = [primaryResult];
    for (const markaz of INDONESIA_MABIMS_MARKAZ) {
        if (isSameCoordinate(markaz, primaryMarkaz)) continue;
        const result = calculateSunMoonDataAtSunset(obsDate, markaz);
        checked.push(result);
        if (isValidMabimsHilal(result)) {
            const out = {
                ...result,
                mabimsOk: true,
                decisiveMarkazName: result.markazName,
                primaryMarkazName: primaryResult.markazName,
                primaryResult,
                passedCities: [result],
                passedCityNames: [result.markazName],
                passedCityCount: 1,
                checkedCityCount: checked.length,
                allMarkazResults: checked,
                nationalPolicy: NATIONAL_MABIMS_POLICY,
                decisionText: `Tidak istikmal. ${primaryResult.markazName} belum memenuhi, tetapi ${result.markazName} memenuhi ${MABIMS_CRITERIA_LABEL}.`,
            };
            __mabimsEvalCache.set(cacheKey, out);
            return out;
        }
    }
    const out = {
        ...primaryResult,
        mabimsOk: false,
        decisiveMarkazName: primaryResult.markazName,
        primaryMarkazName: primaryResult.markazName,
        primaryResult,
        passedCities: [],
        passedCityNames: [],
        passedCityCount: 0,
        checkedCityCount: checked.length,
        allMarkazResults: checked,
        nationalPolicy: NATIONAL_MABIMS_POLICY,
        decisionText: `Istikmal 30 hari. ${primaryResult.markazName} belum memenuhi dan tidak ada kota rujukan lain yang memenuhi ${MABIMS_CRITERIA_LABEL}.`,
    };
    __mabimsEvalCache.set(cacheKey, out);
    return out;
};
getApproxFirstMuharram = async function (year) {
    return getTabularHijriApproxFirstMuharram(year);
};

function setLoading(show, text = "Menghitung data falak...") {
    document.getElementById("loader").classList.toggle("show", show);
    document.getElementById("loaderText").textContent = text;
}
function toast(message) {
    const el = document.getElementById("toast");
    el.textContent = message;
    el.classList.add("show");
    clearTimeout(toast.t);
    toast.t = setTimeout(() => el.classList.remove("show"), 2600);
}

function openDonationModal() {
    const modal = document.getElementById("donationModal");
    if (!modal) return;
    modal.classList.add("open");
    document.body.style.overflow = "hidden";
    setTimeout(() => modal.querySelector(".donation-dialog")?.focus(), 20);
}
function closeDonationModal() {
    const modal = document.getElementById("donationModal");
    if (!modal) return;
    modal.classList.remove("open");
    document.body.style.overflow = "";
}
function handleDonationBackdrop(event) {
    if (event.target === event.currentTarget) closeDonationModal();
}
async function copyDonationValue(value, label, button) {
    try {
        if (navigator.clipboard?.writeText && window.isSecureContext)
            await navigator.clipboard.writeText(value);
        else {
            const area = document.createElement("textarea");
            area.value = value;
            area.setAttribute("readonly", "");
            area.style.position = "fixed";
            area.style.opacity = "0";
            document.body.appendChild(area);
            area.select();
            if (!document.execCommand("copy")) throw new Error("Salin gagal");
            area.remove();
        }
        if (button) {
            const old = button.innerHTML;
            button.classList.add("copied");
            button.textContent = "Tersalin";
            setTimeout(() => {
                button.classList.remove("copied");
                button.innerHTML = old;
            }, 1500);
        }
        toast(`${label} berhasil disalin.`);
    } catch (error) {
        toast(`Tidak dapat menyalin ${label.toLowerCase()}.`);
    }
}
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeDonationModal();
});

const PERSISTENT_PRAYER_STORAGE_KEY = "falakapp-persistent-prayer-v1";
function isPersistentPrayerEnabled() {
    if (FALAK_NATIVE_ANDROID) return false;
    try {
        return localStorage.getItem(PERSISTENT_PRAYER_STORAGE_KEY) === "1";
    } catch (error) {
        return false;
    }
}
function savePersistentPrayerState(enabled) {
    try {
        if (enabled) localStorage.setItem(PERSISTENT_PRAYER_STORAGE_KEY, "1");
        else localStorage.removeItem(PERSISTENT_PRAYER_STORAGE_KEY);
    } catch (error) {
        console.warn("Penyimpanan preferensi tidak tersedia:", error);
    }
}
function updatePersistentPrayerUI() {
    const enabled = isPersistentPrayerEnabled(),
        button = document.getElementById("persistentPrayerBtn"),
        bar = document.getElementById("persistentPrayerBar");
    button?.classList.toggle("active", enabled);
    if (document.getElementById("persistentPrayerBtnText"))
        document.getElementById("persistentPrayerBtnText").textContent = enabled
            ? "Persisten aktif"
            : "Aktifkan persisten";
    bar?.classList.toggle("show", enabled);
    const next = appState.nextPrayer;
    if (next) {
        const diff = Math.max(0, next.date - new Date()),
            minutes = Math.ceil(diff / 60000);
        const title = document.getElementById("persistentPrayerTitle"),
            detail = document.getElementById("persistentPrayerDetail");
        if (title)
            title.textContent = `${next.label} ${next.time} · ${minutes} menit lagi`;
        if (detail)
            detail.textContent = `${appState.locationName} · Ihtiyat 2 menit · notifikasi layar kunci aktif bila didukung perangkat.`;
    }
}
async function ensureServiceWorker() {
    if (
        FALAK_NATIVE_ANDROID ||
        !("serviceWorker" in navigator) ||
        !/^https?:$/.test(location.protocol)
    )
        return null;
    try {
        return await navigator.serviceWorker.register("./falakapp-sw.js");
    } catch (error) {
        console.warn("Service worker gagal didaftarkan:", error);
        return null;
    }
}
function prayerNotificationBody() {
    const t = appState.prayerTimes || {};
    const primary = [
        ["Subuh", "Fajr"],
        ["Zuhur", "Dhuhr"],
        ["Asar", "Asr"],
        ["Magrib", "Maghrib"],
        ["Isya", "Isha"],
    ]
        .map(([label, key]) => `${label} ${cleanTime(t[key])}`)
        .join(" · ");
    const next = appState.nextPrayer
        ? `Berikutnya ${appState.nextPrayer.label} ${appState.nextPrayer.time}. `
        : "";
    return `${next}${primary}\n${appState.locationName} · ihtiyat 2 menit`;
}
async function refreshPersistentPrayerNotification(force = false) {
    if (
        FALAK_NATIVE_ANDROID ||
        !isPersistentPrayerEnabled() ||
        !("Notification" in window) ||
        Notification.permission !== "granted"
    )
        return;
    const now = Date.now();
    if (!force && now - appState.lastNotificationAt < 55000) return;
    appState.lastNotificationAt = now;
    try {
        const registration =
            (await ensureServiceWorker()) ||
            (await navigator.serviceWorker?.ready);
        if (!registration) return;
        await registration.showNotification("FalakApp Pro · Jadwal Salat", {
            body: prayerNotificationBody(),
            icon: "./falakapp-icon.svg",
            badge: "./falakapp-icon.svg",
            tag: "falakapp-prayer-schedule",
            renotify: false,
            requireInteraction: true,
            silent: true,
            data: { url: location.href },
        });
    } catch (error) {
        console.warn("Notifikasi jadwal gagal:", error);
    }
}
async function closePersistentPrayerNotification() {
    try {
        const registration = await navigator.serviceWorker?.ready;
        if (!registration?.getNotifications) return;
        const notifications = await registration.getNotifications({
            tag: "falakapp-prayer-schedule",
        });
        notifications.forEach((item) => item.close());
    } catch (error) {
        console.warn(error);
    }
}
async function togglePersistentPrayer(forceState) {
    if (FALAK_NATIVE_ANDROID) return;
    const nextState =
        typeof forceState === "boolean"
            ? forceState
            : !isPersistentPrayerEnabled();
    if (nextState) {
        savePersistentPrayerState(true);
        updatePersistentPrayerUI();
        if (!("Notification" in window)) {
            toast(
                "Jadwal mengambang aktif. Browser ini belum mendukung notifikasi layar kunci.",
            );
            return;
        }
        let permission = Notification.permission;
        if (permission === "default")
            permission = await Notification.requestPermission();
        if (permission !== "granted") {
            toast(
                "Jadwal mengambang aktif. Izin notifikasi diperlukan untuk bilah status dan layar kunci.",
            );
            return;
        }
        const registration = await ensureServiceWorker();
        if (!registration) {
            toast(
                "Jadwal mengambang aktif. Notifikasi sistem memerlukan HTTPS atau localhost.",
            );
            return;
        }
        await refreshPersistentPrayerNotification(true);
        toast("Jadwal salat persisten diaktifkan.");
    } else {
        savePersistentPrayerState(false);
        updatePersistentPrayerUI();
        await closePersistentPrayerNotification();
        toast("Jadwal salat persisten dinonaktifkan.");
    }
}

function sameDate(a, b) {
    return (
        a &&
        b &&
        a.getFullYear() === b.getFullYear() &&
        a.getMonth() === b.getMonth() &&
        a.getDate() === b.getDate()
    );
}
function cleanTime(value) {
    const m = String(value || "").match(/\d{2}:\d{2}/);
    return m ? m[0] : "-";
}
function dateLong(d) {
    return `${APP_DAYS[d.getDay()]}, ${d.getDate()} ${APP_MONTHS[d.getMonth()]} ${d.getFullYear()}`;
}
function dateKey(d) {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
}
function htmlEscape(value) {
    return String(value ?? "").replace(
        /[&<>"]/g,
        (ch) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" })[ch],
    );
}
function haversineKm(lat1, lon1, lat2, lon2) {
    const R = 6371.0088,
        p1 = toRadians(lat1),
        p2 = toRadians(lat2),
        dp = toRadians(lat2 - lat1),
        dl = toRadians(lon2 - lon1);
    const a =
        Math.sin(dp / 2) ** 2 +
        Math.cos(p1) * Math.cos(p2) * Math.sin(dl / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}
function julianDateLabel(d) {
    return dateToJulianDay(
        new Date(d.getFullYear(), d.getMonth(), d.getDate(), 12, 0, 0),
    ).toFixed(1);
}
function hijriLabel(info) {
    return info
        ? `${info.day} ${getHijriMonthLabel(info.month, "id")} ${info.year} H`
        : "-";
}
function hijriArabic(info) {
    if (!info) return "";
    return `${toArabicNum(info.day)} ${HIJRI_MONTHS[info.month]?.ar || ""} ${toArabicNum(info.year)} هـ`;
}
function hijriArabicCompact(info) {
    if (!info) return "-";
    return `${toArabicNum(info.day)} ${HIJRI_MONTHS[info.month]?.ar || ""} ${toArabicNum(info.year)} هـ`;
}

// function getPublicHolidayEvents(date) {
//     const official = OFFICIAL_PUBLIC_HOLIDAYS[date.getFullYear()];
//     if (official)
//         return (official[dateKey(date)] || []).map((item) => ({
//             ...item,
//             isHoliday: true,
//             types: [item.type],
//             importance: "major",
//         }));
//     const fixed =
//         FIXED_PUBLIC_HOLIDAYS[
//             `${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`
//         ];
//     return fixed
//         ? [
//               {
//                   ...fixed,
//                   type: "national",
//                   types: ["national"],
//                   isHoliday: true,
//                   importance: "major",
//               },
//           ]
//         : [];
// }
// function getIslamicEvents(hijri) {
//   if (!hijri) return [];
//   const events = [];
//   if (hijri.day === 1)
//     events.push({
//       id: `month-start-${hijri.month}`,
//       name: `Awal Bulan ${getHijriMonthLabel(hijri.month, "id")}`,
//       type: "islamic",
//       types: ["islamic"],
//       isHoliday: false,
//       importance: "routine",
//     });
//   if ([13, 14, 15].includes(hijri.day))
//     events.push({
//       id: `ayyamul-bidh-${hijri.month}-${hijri.day}`,
//       name: "Puasa Ayyamul Bidh",
//       type: "islamic",
//       types: ["islamic"],
//       isHoliday: false,
//       importance: "routine",
//     });
//   for (const item of ISLAMIC_EVENT_RULES[`${hijri.month}-${hijri.day}`] || [])
//     events.push({
//       ...item,
//       type: "islamic",
//       types: ["islamic"],
//       isHoliday: false,
//     });
//   return events;
// }

function getPublicHolidayEvents(date) {
    // Matikan semua libur masehi karena madrasah hanya mengikuti libur Islam
    return [];
}
function getIslamicEvents(hijri) {
    if (!hijri) return [];
    const events = [];

    // ===========================================================
    // 1. EVENT RUTIN (Awal Bulan & Ayyamul Bidh - Tetap dipertahankan)
    // ===========================================================
    // if (hijri.day === 1) {
    //     events.push({
    //         id: `month-start-${hijri.month}`,
    //         name: `Awal Bulan ${getHijriMonthLabel(hijri.month, "id")}`,
    //         type: "islamic",
    //         types: ["islamic"],
    //         isHoliday: false,
    //         importance: "routine",
    //     });
    // }

    // if ([13, 14, 15].includes(hijri.day)) {
    //     events.push({
    //         id: `ayyamul-bidh-${hijri.month}-${hijri.day}`,
    //         name: "Puasa Ayyamul Bidh",
    //         type: "islamic",
    //         types: ["islamic"],
    //         isHoliday: false,
    //         importance: "routine",
    //     });
    // }

    // ===========================================================
    // 2. HARI BESAR ISLAM (Menjadi Libur Nasional Madrasah)
    // ===========================================================

    // Tahun Baru Islam (1 Muharram)
    if (hijri.month === 1 && hijri.day === 1) {
        events.push({
            id: "is-1",
            name: "Tahun Baru Islam",
            type: "islamic",
            types: ["islamic"],
            isHoliday: true,
            importance: "major",
        });
    }

    // Maulid Nabi (12 Rabiul Awal)
    if (hijri.month === 3 && hijri.day === 12) {
        events.push({
            id: "is-2",
            name: "Maulid Nabi Muhammad SAW",
            type: "islamic",
            types: ["islamic"],
            isHoliday: true,
            importance: "major",
        });
    }

    // Isra' Mi'raj (27 Rajab)
    if (hijri.month === 7 && hijri.day === 27) {
        events.push({
            id: "is-3",
            name: "Isra' Mi'raj Nabi Muhammad SAW",
            type: "islamic",
            types: ["islamic"],
            isHoliday: true,
            importance: "major",
        });
    }

    // Idul Fitri (1 & 2 Syawal)
    if (hijri.month === 10 && (hijri.day === 1 || hijri.day === 2)) {
        events.push({
            id: `is-4-${hijri.day}`,
            name: "Hari Raya Idul Fitri",
            type: "islamic",
            types: ["islamic"],
            isHoliday: true,
            importance: "major",
        });
    }

    // Idul Adha (10 Dzulhijjah)
    if (hijri.month === 12 && hijri.day === 10) {
        events.push({
            id: "is-5",
            name: "Hari Raya Idul Adha",
            type: "islamic",
            types: ["islamic"],
            isHoliday: true,
            importance: "major",
        });
    }

    // ===========================================================
    // 3. HAUL PENGASUH (Custom Dinamis)
    // ===========================================================

    // 27 Shafar (Bulan ke-2, Hari ke-27)
    if (hijri.month === 1 && hijri.day === 2) {
        let tahunWafat = 1440; // SILAKAN GANTI dengan Tahun Wafat Pengasuh (Hijriyah)
        let haulKe = hijri.year - tahunWafat;

        if (haulKe >= 1) {
            events.push({
                id: `haul-${hijri.year}`,
                name: `Peringatan Haul Ustadz H. Hadiri Ghazali Ke-${haulKe}`,
                type: "islamic",
                types: ["islamic"],
                isHoliday: false, // Ganti menjadi "true" jika hari haul yayasan libur
                importance: "routine",
            });
        }
    }
    // 27 Shafar (Bulan ke-2, Hari ke-27)
    if (hijri.month === 2 && hijri.day === 27) {
        let tahunWafat = 1438; // SILAKAN GANTI dengan Tahun Wafat Pengasuh (Hijriyah)
        let haulKe = hijri.year - tahunWafat;

        if (haulKe >= 1) {
            events.push({
                id: `haul-${hijri.year}`,
                name: `Peringatan Haul Ustadz Abd. Ghoffar Hasin Ke-${haulKe}`,
                type: "islamic",
                types: ["islamic"],
                isHoliday: false, // Ganti menjadi "true" jika hari haul yayasan libur
                importance: "routine",
            });
        }
    }

    // ===========================================================
    // 4. EVENT PENTING LAINNYA (Muncul di kalender tapi tidak libur)
    // ===========================================================
    if (hijri.month === 8 && hijri.day === 15) {
        events.push({
            id: "is-6",
            name: "Malam Nisfu Sya'ban",
            type: "islamic",
            types: ["islamic"],
            isHoliday: false,
            importance: "major",
        });
    }
    if (hijri.month === 9 && hijri.day === 1) {
        events.push({
            id: "is-7",
            name: "Awal Puasa Ramadhan",
            type: "islamic",
            types: ["islamic"],
            isHoliday: false,
            importance: "major",
        });
    }

    return events;
}

function mergeDateEvents(...groups) {
    const map = new Map();
    for (const item of groups.flat()) {
        const key = item.id || item.name.toLowerCase();
        if (!map.has(key))
            map.set(key, {
                ...item,
                types: [...(item.types || [item.type])],
                isHoliday: !!item.isHoliday,
            });
        else {
            const old = map.get(key);
            old.types = [
                ...new Set([
                    ...(old.types || []),
                    ...(item.types || [item.type]),
                ]),
            ];
            old.isHoliday = old.isHoliday || !!item.isHoliday;
            old.importance =
                old.importance === "major" || item.importance === "major"
                    ? "major"
                    : "routine";
            if (item.type === "national" || item.type === "collective")
                old.type = item.type;
        }
    }
    return [...map.values()];
}
function getDateEvents(date, hijri) {
    return mergeDateEvents(
        getPublicHolidayEvents(date),
        getIslamicEvents(hijri),
    );
}
function eventForHijri(info) {
    return getIslamicEvents(info)
        .map((x) => x.name)
        .join(" · ");
}
function enrichCalendarRow(row) {
    const events = getDateEvents(row.date, row.hijri),
        holidayEvents = events.filter((e) => e.isHoliday),
        islamicEvents = events.filter((e) => e.types?.includes("islamic"));
    return {
        ...row,
        events,
        holidayEvents,
        islamicEvents,
        isHoliday: holidayEvents.length > 0,
        isIslamicEvent: islamicEvents.length > 0,
        event: events.map((e) => e.name).join(" · "),
    };
}
function eventTypeLabel(type) {
    return type === "collective"
        ? "Cuti Bersama"
        : type === "national"
          ? "Libur Nasional"
          : "Hari Besar Islam";
}
function eventTagsMarkup(events) {
    const tags = [];
    const types = new Set(events.flatMap((e) => e.types || [e.type]));
    if (types.has("national"))
        tags.push('<span class="event-tag national">Libur Nasional</span>');
    if (types.has("collective"))
        tags.push('<span class="event-tag collective">Cuti Bersama</span>');
    if (types.has("islamic"))
        tags.push('<span class="event-tag islamic">Hari Besar Islam</span>');
    return tags.join("");
}

async function locateHijri(dateObj) {
    const date = new Date(
        dateObj.getFullYear(),
        dateObj.getMonth(),
        dateObj.getDate(),
    );
    const est = estimateHijriYearFromGregorianYear(date.getFullYear());
    for (const y of [est - 1, est, est + 1, est + 2]) {
        const build = await getMabimsYearBuildCached(y);
        for (let m = 1; m <= 12; m++) {
            if (
                dateDiffDays(build.starts[m], date) >= 0 &&
                dateDiffDays(date, build.starts[m + 1]) > 0
            )
                return {
                    year: y,
                    month: m,
                    day: dateDiffDays(build.starts[m], date) + 1,
                    start: build.starts[m],
                    next: build.starts[m + 1],
                    build,
                };
        }
    }
    throw new Error("Tanggal Hijriyah MABIMS tidak ditemukan.");
}
function adaptAttachedCalendarRow(sourceRow) {
    const gregorianDate = parseDate(sourceRow?.date?.gregorian?.date);
    const hijriSource = sourceRow?.date?.hijri || {};
    const row = {
        date: gregorianDate,
        hijri: {
            year: Number(hijriSource.year),
            month: Number(hijriSource.month?.number),
            day: Number(hijriSource.day),
        },
        timings: sourceRow?.timings || {},
        attachedCalendarSource: sourceRow,
    };
    return enrichCalendarRow(row);
}
async function buildHijriMonthRows(hYear, hMonth) {
    const build = await getMabimsYearBuildCached(hYear);
    const start = build.starts[hMonth],
        next = build.starts[hMonth + 1];
    const attachedRows = await buildMabimsHijriMonth(
        hYear,
        hMonth,
        start,
        next,
    );
    attachedRows.startDecisionHisab = build.startDecisions[hMonth];
    attachedRows.monthEndDecisionHisab = build.monthEndDecisions[hMonth];
    attachedRows.monthLength = build.monthLengths[hMonth];
    return {
        rows: attachedRows.map(adaptAttachedCalendarRow),
        attachedRows,
        build,
        start,
        next,
    };
}
async function getAttachedGregorianLookup(gYear) {
    const key = `${Number(gYear)}-${Number(currentLat).toFixed(4)}-${Number(currentLng).toFixed(4)}`;
    if (!falakAppGregorianLookupCache.has(key)) {
        falakAppGregorianLookupCache.set(
            key,
            await buildMabimsHijriLookupForGregorianYear(gYear),
        );
    }
    return falakAppGregorianLookupCache.get(key);
}
async function buildGregorianMonthRows(gDate) {
    const y = gDate.getFullYear(),
        monthNumber = gDate.getMonth() + 1;
    const lookup = await getAttachedGregorianLookup(y);
    const attachedRows = await buildMabimsGregorianMonth(
        y,
        monthNumber,
        lookup,
    );
    return {
        rows: attachedRows.map(adaptAttachedCalendarRow),
        attachedRows,
        start: new Date(y, monthNumber - 1, 1),
        next: new Date(y, monthNumber, 1),
    };
}

function calendarTitleMarkup(rows, mode) {
    const first = rows[0],
        last = rows[rows.length - 1];
    if (mode === "hijri") {
        const ar =
            HIJRI_MONTHS[first.hijri.month]?.ar ||
            getHijriMonthLabel(first.hijri.month, "id");
        return `<strong class="arabic-title">${ar} ${toArabicNum(first.hijri.year)} هـ</strong><small>${getHijriMonthLabel(first.hijri.month, "id")} ${first.hijri.year} H · ${first.date.getDate()} ${APP_MONTHS_SHORT[first.date.getMonth()]} ${first.date.getFullYear()} sampai ${last.date.getDate()} ${APP_MONTHS_SHORT[last.date.getMonth()]} ${last.date.getFullYear()}</small>`;
    }
    const hs = [
        ...new Set(
            rows.map(
                (r) =>
                    `${getHijriMonthLabel(r.hijri.month, "id")} ${r.hijri.year} H`,
            ),
        ),
    ];
    return `<strong>${APP_MONTHS[first.date.getMonth()]} ${first.date.getFullYear()}</strong><small>${hs.join(" sampai ")}</small>`;
}
function calendarCellMarkup(row, mode, mini = false) {
    const main =
        mode === "hijri" ? toArabicNum(row.hijri.day) : row.date.getDate();
    const sub =
        mode === "hijri" ? row.date.getDate() : toArabicNum(row.hijri.day);
    const mainClass = mode === "hijri" ? "arabic-number" : "";
    const subClass = mode === "gregorian" ? "arabic-sub" : "";
    const key = dateKey(row.date),
        eventTitle = row.events.map((e) => e.name).join(", "),
        pasaran = getPasaran(formatDateDDMMYYYY(row.date));
    const classes = [
        mini ? "mini-day" : "cal-day",
        row.date.getDay() === 0 ? "sunday" : "",
        row.isHoliday ? "holiday" : "",
        row.isIslamicEvent ? "islamic-event" : "",
        sameDate(row.date, appState.today) ? "today" : "",
        sameDate(row.date, appState.selectedDate) ? "selected" : "",
    ]
        .filter(Boolean)
        .join(" ");
    if (mini) {
        return `<button class="${classes}" onclick="selectAnnualDate('${key}')" title="${htmlEscape(`${dateLong(row.date)}; ${hijriLabel(row.hijri)}${eventTitle ? `; ${eventTitle}` : ""}`)}"><span class="mini-main ${mainClass}">${main}</span><span class="mini-sub ${subClass}">${sub}</span><span class="mini-pass">${pasaran}</span></button>`;
    }
    const eventTypes = new Set(
        row.events.flatMap((event) => event.types || [event.type]),
    );
    const markers = [
        eventTypes.has("national")
            ? '<i class="event-marker national" title="Libur nasional">L</i>'
            : "",
        eventTypes.has("collective")
            ? '<i class="event-marker collective" title="Cuti bersama">C</i>'
            : "",
        eventTypes.has("islamic")
            ? '<i class="event-marker islamic" title="Hari besar Islam"></i>'
            : "",
    ].join("");
    const eventLabel = row.events[0]?.name || "";
    return `<button class="${classes}" onclick="selectCalendarDate('${key}')" aria-label="${htmlEscape(`${dateLong(row.date)}, ${hijriLabel(row.hijri)}${eventTitle ? `, ${eventTitle}` : ""}`)}"><span class="cal-day-top"><span class="event-markers">${markers}</span><span class="cal-day-sub ${subClass}">${sub}</span></span><span class="cal-day-main ${mainClass}">${main}</span><span class="cal-day-pasaran">${pasaran}</span>${eventLabel ? `<span class="cal-day-event">${htmlEscape(eventLabel)}</span>` : ""}</button>`;
}
function calendarMarkup(rows, mode, target) {
    const firstDow = rows[0].date.getDay();
    const navFn = target === "home" ? "shiftHomeMonth" : "shiftCalendar";
    const weekdays = APP_DAYS.map(
        (day, i) =>
            `<div class="calendar-weekday"><span>${day.slice(0, 3)}</span><span class="weekday-ar">${APP_DAYS_AR[i]}</span></div>`,
    ).join("");
    let cells = "";
    for (let i = 0; i < firstDow; i++)
        cells += '<div class="cal-day blank"></div>';
    cells += rows.map((row) => calendarCellMarkup(row, mode)).join("");
    return `<div class="calendar-pro"><div class="calendar-topline"><button class="calendar-nav" onclick="${navFn}(-1)" aria-label="Bulan sebelumnya">‹</button><div class="calendar-title-stack">${calendarTitleMarkup(rows, mode)}</div><button class="calendar-nav" onclick="${navFn}(1)" aria-label="Bulan berikutnya">›</button><div class="calendar-mode"><button class="${mode === "hijri" ? "active" : ""}" onclick="setCalendarMode('hijri')">Hijriyah</button><button class="${mode === "gregorian" ? "active" : ""}" onclick="setCalendarMode('gregorian')">Masehi</button></div></div><div class="calendar-weekdays">${weekdays}</div><div class="calendar-days">${cells}</div></div>`;
}
async function renderHomeCalendar() {
    let data;
    if (appState.calendarMode === "hijri") {
        data = await buildHijriMonthRows(
            appState.viewHYear,
            appState.viewHMonth,
        );
        appState.currentMonthData = data;
        appState.currentMonthHisab =
            data.build.startDecisions[appState.viewHMonth];
    } else data = await buildGregorianMonthRows(appState.viewGDate);
    appState.currentCalendarRows = data.rows;
    document.getElementById("homeCalendar").innerHTML = calendarMarkup(
        data.rows,
        appState.calendarMode,
        "home",
    );
    renderHomeAlmanac(data.rows);
}
async function renderMainCalendar() {
    let data;
    if (appState.calendarMode === "hijri")
        data = await buildHijriMonthRows(
            appState.viewHYear,
            appState.viewHMonth,
        );
    else data = await buildGregorianMonthRows(appState.viewGDate);
    appState.currentCalendarRows = data.rows;
    document.getElementById("mainCalendar").innerHTML = calendarMarkup(
        data.rows,
        appState.calendarMode,
        "main",
    );
    await renderSelectedDate();
}
function renderHomeAlmanac(rows) {
    const featured = rows.filter(
        (r) =>
            r.isHoliday ||
            r.events.some(
                (e) => e.types?.includes("islamic") && e.importance === "major",
            ),
    );
    const nationalCount = rows.filter((r) =>
        r.events.some((e) => e.types?.includes("national")),
    ).length;
    const collectiveCount = rows.filter((r) =>
        r.events.some((e) => e.types?.includes("collective")),
    ).length;
    const islamicCount = rows.filter((r) =>
        r.events.some(
            (e) => e.types?.includes("islamic") && e.importance === "major",
        ),
    ).length;
    document.getElementById("homeHolidaySummary").innerHTML =
        `<div class="summary-chip holiday"><span>Libur nasional</span><strong>${nationalCount}</strong></div><div class="summary-chip collective"><span>Cuti bersama</span><strong>${collectiveCount}</strong></div><div class="summary-chip islamic"><span>Hari besar Islam</span><strong>${islamicCount}</strong></div>`;
    document.getElementById("homeAlmanac").innerHTML = featured.length
        ? featured
              .map((row) => {
                  const names = row.events
                      .filter(
                          (e) =>
                              e.isHoliday ||
                              (e.types?.includes("islamic") &&
                                  e.importance === "major"),
                      )
                      .map((e) => e.name);
                  return `<article class="observance-row ${row.isHoliday ? "is-holiday" : ""} ${row.isIslamicEvent ? "is-islamic" : ""}" onclick="selectCalendarDate('${dateKey(row.date)}')"><div class="observance-date"><strong>${row.date.getDate()}</strong><span>${APP_MONTHS_SHORT[row.date.getMonth()]}</span></div><div class="observance-main"><h4>${htmlEscape(names.join(" · "))}</h4><p>${APP_DAYS[row.date.getDay()]} ${getPasaran(formatDateDDMMYYYY(row.date))} · <span class="arabic">${hijriArabicCompact(row.hijri)}</span></p></div><div class="event-tags">${eventTagsMarkup(row.events)}</div></article>`;
              })
              .join("")
        : '<div class="empty">Tidak ada libur nasional atau hari besar Islam utama pada bulan ini.</div>';
    const year = rows[0]?.date.getFullYear();
    document.getElementById("holidaySourceNote").textContent =
        OFFICIAL_PUBLIC_HOLIDAYS[year]
            ? "Libur nasional dan cuti bersama tahun 2026 mengacu pada SKB 3 Menteri Nomor 1497/2025, Nomor 2/2025, dan Nomor 5/2025. Hari besar Islam mengikuti kalender Hijriyah MABIMS aplikasi."
            : "Untuk tahun di luar data resmi 2026, aplikasi menampilkan libur nasional bertanggal tetap dan hari besar Islam hasil kalender MABIMS. Perbarui objek OFFICIAL_PUBLIC_HOLIDAYS saat SKB tahunan terbit.";
}

function miniCalendarMarkup(rows, mode) {
    const firstDow = rows[0].date.getDay();
    let cells = "";
    for (let i = 0; i < firstDow; i++)
        cells += '<span class="mini-day blank"></span>';
    cells += rows.map((row) => calendarCellMarkup(row, mode, true)).join("");
    const first = rows[0],
        last = rows[rows.length - 1];
    const head =
        mode === "hijri"
            ? `<strong class="arabic-mini">${HIJRI_MONTHS[first.hijri.month]?.ar || ""} ${toArabicNum(first.hijri.year)} هـ</strong><span>${getHijriMonthLabel(first.hijri.month, "id")} · ${first.date.getDate()} ${APP_MONTHS_SHORT[first.date.getMonth()]} sampai ${last.date.getDate()} ${APP_MONTHS_SHORT[last.date.getMonth()]}</span>`
            : `<strong>${APP_MONTHS[first.date.getMonth()]} ${first.date.getFullYear()}</strong><span>${[...new Set(rows.map((r) => `${getHijriMonthLabel(r.hijri.month, "id")} ${r.hijri.year} H`))].join(" · ")}</span>`;
    return `<section class="mini-calendar"><div class="mini-calendar-head">${head}</div><div class="mini-weekdays">${APP_DAYS.map((d) => `<span>${d.slice(0, 1)}</span>`).join("")}</div><div class="mini-days">${cells}</div></section>`;
}
function syncAnnualControls(force = false) {
    const modeEl = document.getElementById("annualModeSelect"),
        yearEl = document.getElementById("annualYearInput");
    if (!modeEl || !yearEl) return;
    if (force || !appState.annualLoaded) {
        modeEl.value = appState.calendarMode;
        yearEl.value =
            appState.calendarMode === "hijri"
                ? appState.viewHYear || appState.currentHijri?.year || 1448
                : appState.viewGDate.getFullYear();
    }
    document.getElementById("annualYearLabel").textContent =
        modeEl.value === "hijri" ? "Tahun Hijriyah" : "Tahun Masehi";
}
function handleAnnualModeChange() {
    const mode = document.getElementById("annualModeSelect").value;
    document.getElementById("annualYearLabel").textContent =
        mode === "hijri" ? "Tahun Hijriyah" : "Tahun Masehi";
    document.getElementById("annualYearInput").value =
        mode === "hijri"
            ? appState.currentHijri?.year || 1448
            : appState.today.getFullYear();
}
async function renderAnnualCalendar() {
    const mode = document.getElementById("annualModeSelect").value,
        year = parseInt(document.getElementById("annualYearInput").value, 10),
        grid = document.getElementById("annualCalendarGrid"),
        status = document.getElementById("annualCalendarStatus");
    if (
        !Number.isInteger(year) ||
        year < 1 ||
        (mode === "gregorian" && (year < 1700 || year > 2500))
    ) {
        toast("Tahun kalender tidak valid.");
        return;
    }
    setLoading(
        true,
        `Menyusun kalender ${mode === "hijri" ? "Hijriyah" : "Masehi"} satu tahun...`,
    );
    grid.innerHTML = "";
    try {
        const parts = [];
        for (let month = 1; month <= 12; month++) {
            document.getElementById("loaderText").textContent =
                `Menyusun bulan ${month} dari 12...`;
            const data =
                mode === "hijri"
                    ? await buildHijriMonthRows(year, month)
                    : await buildGregorianMonthRows(
                          new Date(year, month - 1, 1),
                      );
            parts.push(miniCalendarMarkup(data.rows, mode));
        }
        grid.innerHTML = parts.join("");
        appState.annualLoaded = true;
        status.textContent = `Kalender ${mode === "hijri" ? `Hijriyah ${year} H` : `Masehi ${year}`} selesai dibuat. Angka tanggal Hijriyah ditampilkan dengan angka Arab. Klik tanggal untuk membuka detail.`;
    } catch (error) {
        console.error(error);
        status.textContent = `Kalender tahunan gagal dibuat: ${error.message}`;
        toast("Gagal membuat kalender tahunan.");
    } finally {
        setLoading(false);
    }
}
function printAnnualCalendar() {
    if (!document.getElementById("annualCalendarGrid").children.length) {
        toast("Tampilkan kalender 12 bulan terlebih dahulu.");
        return;
    }
    document.body.classList.add("print-annual");
    window.print();
    setTimeout(() => document.body.classList.remove("print-annual"), 600);
}
async function selectAnnualDate(key) {
    appState.selectedDate = new Date(`${key}T12:00:00`);
    try {
        localStorage.setItem("falakapp-selected-date", key);
    } catch (error) {}
    const h = await locateHijri(appState.selectedDate);
    appState.viewHYear = h.year;
    appState.viewHMonth = h.month;
    appState.viewGDate = new Date(
        appState.selectedDate.getFullYear(),
        appState.selectedDate.getMonth(),
        1,
    );
    if (document.getElementById("mainCalendar")) await renderMainCalendar();
    else showView("calendar");
}

async function renderPrayer(date = appState.today) {
    const timings = buildLocalPrayerTimings(date, currentLat, currentLng);
    appState.prayerTimes = timings;
    const now = new Date(),
        today = sameDate(date, now),
        items = [];
    let next = null;
    for (const [key, label, icon] of PRAYER_LABELS) {
        const t = cleanTime(timings[key]);
        let active = false;
        if (today && t !== "-") {
            const [h, m] = t.split(":").map(Number),
                dt = locationCivilDateTime(
                    now,
                    h,
                    m,
                    0,
                    currentLat,
                    currentLng,
                );
            if (dt > now && !next) {
                next = { key, label, time: t, date: dt };
                active = true;
            }
        }
        const ihtiyat = PRAYER_IHTIYAT_KEYS.has(key)
            ? '<span class="ihtiyat-badge" title="Ihtiyat 2 menit">+2</span>'
            : "";
        items.push(
            `<div class="prayer-item ${active ? "active" : ""}"><div class="prayer-icon">${icon}</div><div class="prayer-name">${label}</div><div class="prayer-time-wrap"><div class="prayer-time">${t}</div>${ihtiyat}</div></div>`,
        );
    }
    if (today && !next) {
        const tomorrow = addDays(now, 1),
            tt = buildLocalPrayerTimings(tomorrow, currentLat, currentLng),
            t = cleanTime(tt.Fajr),
            [h, m] = t.split(":").map(Number);
        next = {
            key: "Fajr",
            label: "Subuh",
            time: t,
            date: locationCivilDateTime(
                tomorrow,
                h,
                m,
                0,
                currentLat,
                currentLng,
            ),
        };
    }
    appState.nextPrayer = next;
    document.getElementById("prayerGrid").innerHTML = items.join("");
    updateCountdown();
    updatePersistentPrayerUI();
    await refreshPersistentPrayerNotification(true);
}
function updateCountdown() {
    const n = appState.nextPrayer;
    if (!n) {
        updatePersistentPrayerUI();
        return;
    }
    const diff = n.date - new Date();
    if (diff <= 0) {
        renderPrayer();
        return;
    }
    const s = Math.floor(diff / 1000),
        hh = String(Math.floor(s / 3600)).padStart(2, "0"),
        mm = String(Math.floor((s % 3600) / 60)).padStart(2, "0"),
        ss = String(s % 60).padStart(2, "0");
    document.getElementById("nextPrayerName").textContent =
        `${n.label} ${n.time}`;
    document.getElementById("nextPrayerCountdown").textContent =
        `${hh}:${mm}:${ss} lagi`;
    updatePersistentPrayerUI();
}

function renderMetrics(info, target) {
    const date = appState.selectedDate,
        events = getDateEvents(date, info),
        eventText = events.map((e) => e.name).join(" · ") || "Hari biasa";
    const status = events.some((e) => e.types?.includes("collective"))
        ? "Cuti bersama"
        : events.some((e) => e.types?.includes("national"))
          ? "Libur nasional"
          : events.some((e) => e.types?.includes("islamic"))
            ? "Hari penting Islam"
            : "Hari biasa";
    const items = [
        ["Tanggal Masehi", dateLong(date)],
        ["Tanggal Hijriyah", hijriArabicCompact(info)],
        [
            "Hari dan Pasaran",
            `${APP_DAYS[date.getDay()]} ${getPasaran(formatDateDDMMYYYY(date))}`,
        ],
        ["Status", status],
        ["Julian Day", julianDateLabel(date)],
        [
            "Hari ke-Masehi",
            String(
                Math.floor(
                    (date - new Date(date.getFullYear(), 0, 0)) / 86400000,
                ),
            ),
        ],
        [
            "Azimut Kiblat",
            `${getQiblaBearing(currentLat, currentLng).toFixed(3)}°`,
        ],
        ["Keterangan", eventText],
        ["Sumber kalender", "Hisab MABIMS 3° dan elongasi 6,4°"],
    ];
    const el = document.getElementById(target);
    if (el)
        el.innerHTML = items
            .map(
                ([a, b]) =>
                    `<div class="metric"><span>${a}</span><strong class="${a === "Tanggal Hijriyah" ? "arabic" : ""}">${htmlEscape(b)}</strong></div>`,
            )
            .join("");
}
function renderSelectedEventPanel(info) {
    const panel = document.getElementById("selectedEventPanel");
    if (!panel) return;
    const events = getDateEvents(appState.selectedDate, info);
    panel.innerHTML = events.length
        ? `<h4>Hari penting pada tanggal ini</h4><div class="observance-list">${events.map((e) => `<div class="observance-main"><h4>${htmlEscape(e.name)}</h4><p>${eventTypeLabel(e.type)}${e.types?.includes("islamic") && e.type !== "islamic" ? " · Hari Besar Islam" : ""}</p></div>`).join("")}</div>`
        : '<h4>Hari penting pada tanggal ini</h4><div class="empty-inline">Tidak ada libur nasional atau catatan hari besar Islam.</div>';
}
async function renderSelectedDate() {
    const h = await locateHijri(appState.selectedDate);
    const label = document.getElementById("selectedDateLabel");
    if (label)
        label.innerHTML = `${dateLong(appState.selectedDate)} · <span class="arabic">${hijriArabicCompact(h)}</span>`;
    renderMetrics(h, "selectedDateMetrics");
    renderMetrics(h, "almanacMetrics");
    renderSelectedEventPanel(h);
}
function setAlmanacFilter(filter) {
    appState.almanacFilter = filter;
    document
        .querySelectorAll("#almanacFilter button")
        .forEach((btn) =>
            btn.classList.toggle("active", btn.dataset.filter === filter),
        );
    renderAlmanacTable();
}
async function renderAlmanacTable(rows = appState.currentCalendarRows) {
    const sourceRows = rows || [],
        title = sourceRows.length
            ? `${dateLong(sourceRows[0].date)} sampai ${dateLong(sourceRows[sourceRows.length - 1].date)}`
            : "-";
    const titleEl = document.getElementById("almanacTableTitle");
    if (titleEl) titleEl.textContent = title;
    let filtered = sourceRows;
    if (appState.almanacFilter === "events")
        filtered = sourceRows.filter((r) => r.events.length);
    if (appState.almanacFilter === "holidays")
        filtered = sourceRows.filter((r) => r.isHoliday);
    if (appState.almanacFilter === "islamic")
        filtered = sourceRows.filter((r) => r.islamicEvents.length);
    const out = [];
    for (const r of filtered) {
        const p = buildLocalPrayerTimings(r.date, currentLat, currentLng),
            rowClass = [
                sameDate(r.date, appState.today) ? "today-row" : "",
                r.isHoliday ? "holiday-row" : "",
                r.isIslamicEvent ? "islamic-row" : "",
            ]
                .filter(Boolean)
                .join(" ");
        const eventMarkup = r.events.length
            ? `<div class="table-event-stack">${r.events.map((e) => `<span class="table-event-name">${htmlEscape(e.name)}</span>`).join("")}</div>`
            : "-";
        out.push(
            `<tr class="${rowClass}"><td>${r.date.getDate()} ${APP_MONTHS[r.date.getMonth()]} ${r.date.getFullYear()}</td><td class="arabic">${hijriArabicCompact(r.hijri)}</td><td>${APP_DAYS[r.date.getDay()]} · ${getPasaran(formatDateDDMMYYYY(r.date))}</td><td>${cleanTime(p.Fajr)}</td><td>${cleanTime(p.Dhuhr)}</td><td>${cleanTime(p.Maghrib)}</td><td>${eventMarkup}</td></tr>`,
        );
    }
    const body = document.getElementById("almanacTable");
    if (body)
        body.innerHTML =
            out.join("") ||
            '<tr><td colspan="7" class="empty">Tidak ada data yang sesuai dengan filter.</td></tr>';
}
async function selectCalendarDate(key) {
    appState.selectedDate = new Date(`${key}T12:00:00`);
    try {
        localStorage.setItem("falakapp-selected-date", key);
    } catch (error) {}
    await renderSelectedDate();
    if (document.getElementById("mainCalendar")) await renderMainCalendar();
    else if (document.getElementById("homeCalendar"))
        await renderHomeCalendar();
}
async function shiftHomeMonth(delta) {
    setLoading(true, "Menyusun kalender bulan...");
    try {
        if (appState.calendarMode === "hijri") {
            let m = appState.viewHMonth + delta,
                y = appState.viewHYear;
            if (m < 1) {
                m = 12;
                y--;
            }
            if (m > 12) {
                m = 1;
                y++;
            }
            appState.viewHMonth = m;
            appState.viewHYear = y;
        } else
            appState.viewGDate = new Date(
                appState.viewGDate.getFullYear(),
                appState.viewGDate.getMonth() + delta,
                1,
            );
        await renderHomeCalendar();
    } finally {
        setLoading(false);
    }
}
async function shiftCalendar(delta) {
    setLoading(true, "Menyusun kalender...");
    try {
        if (appState.calendarMode === "hijri") {
            let m = appState.viewHMonth + delta,
                y = appState.viewHYear;
            if (m < 1) {
                m = 12;
                y--;
            }
            if (m > 12) {
                m = 1;
                y++;
            }
            appState.viewHMonth = m;
            appState.viewHYear = y;
        } else
            appState.viewGDate = new Date(
                appState.viewGDate.getFullYear(),
                appState.viewGDate.getMonth() + delta,
                1,
            );
        await renderMainCalendar();
    } finally {
        setLoading(false);
    }
}
async function setCalendarMode(mode) {
    if (
        !["hijri", "gregorian"].includes(mode) ||
        mode === appState.calendarMode
    )
        return;
    appState.calendarMode = mode;
    setLoading(true, "Mengubah mode kalender...");
    try {
        if (mode === "gregorian")
            appState.viewGDate = new Date(
                appState.selectedDate.getFullYear(),
                appState.selectedDate.getMonth(),
                1,
            );
        else {
            const h = await locateHijri(appState.selectedDate);
            appState.viewHYear = h.year;
            appState.viewHMonth = h.month;
        }
        syncAnnualControls(true);
        if (document.getElementById("homeCalendar")) await renderHomeCalendar();
        if (document.getElementById("mainCalendar")) await renderMainCalendar();
    } finally {
        setLoading(false);
    }
}
async function goHomeToday() {
    appState.selectedDate = new Date();
    const h = await locateHijri(appState.selectedDate);
    appState.viewHYear = h.year;
    appState.viewHMonth = h.month;
    appState.viewGDate = new Date(
        appState.today.getFullYear(),
        appState.today.getMonth(),
        1,
    );
    await renderHomeCalendar();
    await renderSelectedDate();
}
async function goCalendarToday() {
    appState.selectedDate = new Date();
    const h = await locateHijri(appState.selectedDate);
    appState.viewHYear = h.year;
    appState.viewHMonth = h.month;
    appState.viewGDate = new Date(
        appState.today.getFullYear(),
        appState.today.getMonth(),
        1,
    );
    await renderMainCalendar();
}
async function selectToday() {
    appState.selectedDate = new Date();
    await renderSelectedDate();
    showView("almanac");
}

function renderQibla() {
    const bearing = getQiblaBearing(currentLat, currentLng),
        distance = haversineKm(currentLat, currentLng, 21.422487, 39.826206),
        rashdul = calculateRoshdulKiblat(new Date()),
        sun = sunHorizontalPrecise(new Date(), currentLat, currentLng);
    document.getElementById("qLat").value = Number(currentLat).toFixed(6);
    document.getElementById("qLng").value = Number(currentLng).toFixed(6);
    document.getElementById("qiblaArrow").style.transform =
        `translate(-50%,-100%) rotate(${bearing}deg)`;
    const qitems = [
        ["Azimut kiblat", `${bearing.toFixed(5)}° dari utara sejati`],
        [
            "Jarak ke Ka’bah",
            `${distance.toLocaleString("id-ID", { maximumFractionDigits: 1 })} km`,
        ],
        [
            "Koordinat markaz",
            `${Number(currentLat).toFixed(5)}, ${Number(currentLng).toFixed(5)}`,
        ],
        [
            "Rashdul kiblat",
            rashdul.roshdul !== "-"
                ? `${rashdul.roshdul} · ${rashdul.arahBayangan}`
                : "Tidak terjadi hari ini",
        ],
        ["Waktu istiwa", rashdul.istiwaDetail],
        ["Selisih istiwa", rashdul.selisihDetail],
    ];
    document.getElementById("qiblaMetrics").innerHTML = qitems
        .map(
            ([a, b]) =>
                `<div class="pro-item"><span>${a}</span><strong>${b}</strong></div>`,
        )
        .join("");
    if (!appState.orientationActive) {
        document.getElementById("qiblaAlignmentTitle").textContent =
            `Azimut kiblat ${bearing.toFixed(2)}°`;
        document.getElementById("qiblaAlignmentDetail").textContent =
            "Aktifkan sensor untuk memperoleh panduan putar kanan atau kiri secara langsung.";
    }
    const sitems = [
        ["Azimut Matahari", `${sun.azimuth.toFixed(3)}°`],
        ["Tinggi Matahari", `${sun.altitude.toFixed(3)}°`],
        ["Deklinasi Matahari", `${sun.dec.toFixed(3)}°`],
        ["Arah bayangan", `${normalizeDegree(sun.azimuth + 180).toFixed(3)}°`],
    ];
    document.getElementById("sunQiblaMetrics").innerHTML = sitems
        .map(
            ([a, b]) =>
                `<div class="metric"><span>${a}</span><strong>${b}</strong></div>`,
        )
        .join("");
}
function applyManualLocation() {
    const lat = parseFloat(document.getElementById("qLat").value),
        lng = parseFloat(document.getElementById("qLng").value);
    if (
        !Number.isFinite(lat) ||
        lat < -90 ||
        lat > 90 ||
        !Number.isFinite(lng) ||
        lng < -180 ||
        lng > 180
    ) {
        toast("Koordinat tidak valid.");
        return;
    }
    currentLat = lat;
    currentLng = lng;
    currentMarkazName = "Koordinat manual";
    appState.locationName = "Koordinat manual";
    afterLocationChange();
}
function requestLocation() {
    if (!navigator.geolocation) {
        toast("GPS tidak tersedia pada perangkat ini.");
        return;
    }
    setLoading(true, "Mengambil lokasi GPS...");
    navigator.geolocation.getCurrentPosition(
        (p) => {
            currentLat = p.coords.latitude;
            currentLng = p.coords.longitude;
            currentMarkazName = "Lokasi GPS";
            appState.locationName = `GPS ±${Math.round(p.coords.accuracy || 0)} m`;
            afterLocationChange();
        },
        (e) => {
            setLoading(false);
            toast("GPS gagal. Aplikasi tetap memakai Pasuruan default.");
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 },
    );
}
async function afterLocationChange() {
    __mabimsEvalCache.clear();
    __sunMoonCache.clear();
    __ijtimaCache.clear();
    mabimsYearBuildCache.clear();
    falakAppGregorianLookupCache.clear();
    prayerCalendarCache.clear();
    const homeLocation = document.getElementById("homeLocation");
    if (homeLocation) homeLocation.textContent = appState.locationName;
    window.FalakHisabAwalBulan?.setLocation?.({
        lat: currentLat,
        lon: currentLng,
        location: appState.locationName,
    });
    try {
        const h = await locateHijri(new Date());
        appState.currentHijri = h;
        appState.viewHYear = h.year;
        appState.viewHMonth = h.month;
        const homeHijri = document.getElementById("homeHijri");
        if (homeHijri) homeHijri.textContent = hijriArabic(h);
        if (document.getElementById("prayerGrid")) await renderPrayer();
        if (document.getElementById("qiblaMetrics")) renderQibla();
        if (document.getElementById("homeCalendar")) await renderHomeCalendar();
        if (document.getElementById("mainCalendar")) await renderMainCalendar();
        if (document.getElementById("currentEclipseResults")) {
            const data = await buildHijriMonthRows(h.year, h.month);
            appState.currentMonthData = data;
            appState.currentMonthHisab = data.build.startDecisions[h.month];
            renderCurrentEclipse();
        }
        toast("Lokasi dan hisab pada halaman ini telah diperbarui.");
    } catch (e) {
        console.error(e);
        toast("Sebagian data gagal dihitung ulang.");
    } finally {
        setLoading(false);
    }
}
function getScreenOrientationAngle() {
    return Number(screen.orientation?.angle ?? window.orientation ?? 0) || 0;
}
function orientationHeading(event) {
    if (Number.isFinite(event.webkitCompassHeading))
        return normalizeDegree(event.webkitCompassHeading);
    if (!Number.isFinite(event.alpha)) return null;
    return normalizeDegree(360 - event.alpha + getScreenOrientationAngle());
}
function handleOrientation(event) {
    const raw = orientationHeading(event);
    if (raw === null) return;
    const previous = Number.isFinite(appState.heading) ? appState.heading : raw;
    const step = signedAngleDifference(raw, previous);
    const heading = normalizeDegree(previous + step * 0.22);
    appState.heading = heading;
    appState.orientationActive = true;
    const bearing = getQiblaBearing(currentLat, currentLng),
        turn = signedAngleDifference(bearing, heading),
        delta = Math.abs(turn),
        aligned = delta <= 3;
    document.getElementById("compassDial").style.transform =
        `rotate(${-heading}deg)`;
    document.getElementById("headingValue").textContent =
        `${heading.toFixed(1)}°`;
    const alignment = document.getElementById("qiblaAlignment");
    alignment.classList.toggle("is-aligned", aligned);
    document.getElementById("qiblaAlignmentTitle").textContent = aligned
        ? "Tepat menghadap kiblat"
        : `Putar ${turn > 0 ? "ke kanan" : "ke kiri"} ${delta.toFixed(1)}°`;
    document.getElementById("qiblaAlignmentDetail").textContent = aligned
        ? `Selisih sensor ${delta.toFixed(1)}°. Pertahankan posisi perangkat.`
        : `Target azimut ${bearing.toFixed(2)}°. Pegang perangkat datar dan bergerak perlahan.`;
    document.getElementById("compassStatus").textContent = aligned
        ? "Arah perangkat berada dalam toleransi ±3°."
        : `Heading ${heading.toFixed(1)}° · target kiblat ${bearing.toFixed(1)}°`;
    const meta = document.getElementById("qiblaSensorMeta");
    meta.classList.add("active");
    meta.querySelector("b").textContent = Number.isFinite(
        event.webkitCompassAccuracy,
    )
        ? `Sensor aktif · akurasi ±${Math.round(event.webkitCompassAccuracy)}°`
        : event.absolute
          ? "Sensor absolut aktif"
          : "Sensor orientasi aktif";
}
async function requestOrientation() {
    try {
        if (typeof DeviceOrientationEvent === "undefined")
            throw new Error("Sensor tidak tersedia");
        if (typeof DeviceOrientationEvent.requestPermission === "function") {
            const permission = await DeviceOrientationEvent.requestPermission();
            if (permission !== "granted") throw new Error("Izin ditolak");
        }
        window.removeEventListener(
            "deviceorientationabsolute",
            handleOrientation,
            true,
        );
        window.removeEventListener(
            "deviceorientation",
            handleOrientation,
            true,
        );
        window.addEventListener(
            "deviceorientationabsolute",
            handleOrientation,
            true,
        );
        window.addEventListener("deviceorientation", handleOrientation, true);
        appState.orientationActive = true;
        document.getElementById("compassStatus").textContent =
            "Sensor aktif. Lakukan gerakan angka delapan untuk kalibrasi.";
        document.getElementById("qiblaSensorMeta").classList.add("active");
        document
            .getElementById("qiblaSensorMeta")
            .querySelector("b").textContent = "Menunggu data sensor...";
        toast("Kompas kiblat profesional diaktifkan.");
    } catch (error) {
        appState.orientationActive = false;
        document.getElementById("qiblaSensorMeta").classList.remove("active");
        document
            .getElementById("qiblaSensorMeta")
            .querySelector("b").textContent =
            "Sensor tidak tersedia atau izin ditolak";
        toast("Izin kompas tidak tersedia atau ditolak.");
    }
}

function monthDataForEclipse(rows) {
    return rows.map((r) => ({
        date: { gregorian: { date: formatDateDDMMYYYY(r.date) } },
    }));
}
function eclipseCard(event, monthLabel = "") {
    if (!event) return '<div class="empty">Data gerhana tidak tersedia.</div>';
    const rows = (event.rows || [])
        .map(
            (r) =>
                `<div class="event-row"><span>${r.label}</span><strong>${r.value}</strong></div>`,
        )
        .join("");
    return `<article class="event-card"><div class="event-top"><div><h3>${event.title}</h3><div style="font-size:9px;color:#64748b;margin-top:4px">${monthLabel}</div></div><span class="event-type">${event.type}</span></div><div class="event-rows">${rows}</div></article>`;
}
function renderCurrentEclipse() {
    if (!appState.currentMonthData) return;
    const rows = appState.currentMonthData.rows,
        monthData = monthDataForEclipse(rows),
        solar = calculateSolarEclipseForMonth(appState.currentMonthHisab),
        lunar = calculateLunarEclipseForMonth(monthData);
    document.getElementById("eclipseMonthLabel").textContent =
        `${getHijriMonthLabel(rows[0].hijri.month, "id")} ${rows[0].hijri.year} H · Markaz ${appState.locationName}`;
    document.getElementById("currentEclipseResults").innerHTML =
        eclipseCard(solar, "Fase ijtima bulan berjalan") +
        eclipseCard(lunar, "Fase purnama bulan berjalan");
}
async function scanEclipseYear() {
    const year = parseInt(document.getElementById("eclipseYear").value, 10),
        filter = document.getElementById("eclipseFilter").value;
    if (!Number.isInteger(year) || year < 1) {
        toast("Tahun Hijriyah tidak valid.");
        return;
    }
    setLoading(true, "Memindai 12 lunasi dan kontak gerhana...");
    try {
        const build = await getMabimsYearBuildCached(year),
            events = [],
            seen = new Set();
        for (let m = 1; m <= 12; m++) {
            document.getElementById("loaderText").textContent =
                `Menghitung ${getHijriMonthLabel(m, "id")} ${year} H...`;
            const data = await buildHijriMonthRows(year, m),
                monthData = monthDataForEclipse(data.rows),
                solar = calculateSolarEclipseForMonth(build.startDecisions[m]),
                lunar = calculateLunarEclipseForMonth(monthData);
            for (const ev of [solar, lunar]) {
                if (!ev || !ev.potential) continue;
                if (filter !== "all" && ev.kind !== filter) continue;
                const key = `${ev.kind}-${formatDateDDMMYYYY(ev.maxTime || ev.conjunction || ev.opposition)}`;
                if (!seen.has(key)) {
                    seen.add(key);
                    events.push({
                        event: ev,
                        label: `${getHijriMonthLabel(m, "id")} ${year} H`,
                    });
                }
            }
        }
        events.sort(
            (a, b) =>
                (a.event.maxTime || a.event.conjunction || a.event.opposition) -
                (b.event.maxTime || b.event.conjunction || b.event.opposition),
        );
        document.getElementById("yearEclipseResults").innerHTML = events.length
            ? events.map((x) => eclipseCard(x.event, x.label)).join("")
            : '<div class="empty">Tidak ditemukan potensi gerhana pada tahun dan filter ini.</div>';
    } catch (e) {
        console.error(e);
        document.getElementById("yearEclipseResults").innerHTML =
            `<div class="empty">Hisab gagal: ${e.message}</div>`;
    } finally {
        setLoading(false);
    }
}
function resetEclipseYear() {
    document.getElementById("eclipseYear").value =
        appState.currentHijri?.year || 1448;
    document.getElementById("yearEclipseResults").innerHTML = "";
}

function showView(name) {
    const routes = {
        home: "index.html",
        almanac: "almanak.html",
        calendar: "kalender.html",
        qibla: "kiblat.html",
        eclipse: "gerhana.html",
    };
    const target = routes[name] || routes.home;
    if (
        location.pathname.endsWith("/" + target) ||
        location.pathname.endsWith(target)
    )
        return;
    location.href = target;
}

async function initApp() {
    setLoading(true, "Menyelaraskan kalender MABIMS...");
    try {
        const page = document.body.dataset.page || "home";
        appState.today = new Date();
        let storedDate = null;
        try {
            storedDate = localStorage.getItem("falakapp-selected-date");
        } catch (error) {}
        appState.selectedDate = storedDate
            ? new Date(`${storedDate}T12:00:00`)
            : new Date();
        const storedSettings = window.FalakSettings?.read?.();
        if (storedSettings) {
            if (Number.isFinite(storedSettings.lat))
                currentLat = storedSettings.lat;
            if (Number.isFinite(storedSettings.lng))
                currentLng = storedSettings.lng;
            if (storedSettings.location) {
                currentMarkazName = storedSettings.location;
                appState.locationName = storedSettings.location;
            }
        }
        const h = await locateHijri(appState.selectedDate);
        appState.currentHijri = h;
        appState.viewHYear = h.year;
        appState.viewHMonth = h.month;
        appState.viewGDate = new Date(
            appState.selectedDate.getFullYear(),
            appState.selectedDate.getMonth(),
            1,
        );
        const homeDate = document.getElementById("homeDate");
        if (homeDate) homeDate.textContent = dateLong(appState.today);
        const homeLocation = document.getElementById("homeLocation");
        if (homeLocation) homeLocation.textContent = appState.locationName;
        const homeHijri = document.getElementById("homeHijri");
        if (homeHijri)
            homeHijri.textContent = hijriArabic(
                await locateHijri(appState.today),
            );
        if (page === "home") {
            await ensureServiceWorker();
            await renderPrayer();
            await renderHomeCalendar();
            updatePersistentPrayerUI();
        } else if (page === "calendar") {
            syncAnnualControls(true);
            await renderMainCalendar();
        } else if (page === "qibla") {
            renderQibla();
        } else if (page === "eclipse") {
            const yearInput = document.getElementById("eclipseYear");
            if (yearInput) yearInput.value = h.year;
            const data = await buildHijriMonthRows(h.year, h.month);
            appState.currentMonthData = data;
            appState.currentMonthHisab = data.build.startDecisions[h.month];
            renderCurrentEclipse();
        } else if (page === "almanac") {
            window.FalakHisabAwalBulan?.syncFromApp?.({
                year: h.year,
                month: h.month,
                lat: currentLat,
                lon: currentLng,
                location: appState.locationName,
            });
        }
        appState.initialized = true;
    } catch (e) {
        console.error(e);
        toast(`Gagal memulai aplikasi: ${e.message}`);
    } finally {
        setLoading(false);
    }
}
setInterval(updateCountdown, 1000);
setInterval(() => refreshPersistentPrayerNotification(false), 60000);
document.addEventListener("DOMContentLoaded", initApp);

/* Multipage shell and application settings */
(function () {
    const KEY = "falakapp-pro-settings-v2";
    function read() {
        try {
            const raw = JSON.parse(localStorage.getItem(KEY) || "{}");
            return {
                lat: Number.isFinite(Number(raw.lat)) ? Number(raw.lat) : null,
                lng: Number.isFinite(Number(raw.lng)) ? Number(raw.lng) : null,
                location: typeof raw.location === "string" ? raw.location : "",
                theme: raw.theme || "light",
                compact: !!raw.compact,
                largeText: !!raw.largeText,
            };
        } catch (error) {
            return {
                lat: null,
                lng: null,
                location: "",
                theme: "light",
                compact: false,
                largeText: false,
            };
        }
    }
    function applyVisual(settings) {
        document.body.dataset.theme =
            settings.theme === "dark" ? "dark" : "light";
        document.body.classList.toggle("compact-mode", !!settings.compact);
        document.body.classList.toggle("large-text", !!settings.largeText);
    }
    function fill() {
        const s = read();
        applyVisual(s);
        const map = {
            settingsLat: s.lat,
            settingsLng: s.lng,
            settingsLocation: s.location,
            settingsTheme: s.theme,
        };
        Object.entries(map).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el && value !== null && value !== "") el.value = value;
        });
        const compact = document.getElementById("settingsCompact");
        if (compact) compact.checked = s.compact;
        const large = document.getElementById("settingsLargeText");
        if (large) large.checked = s.largeText;
    }
    function open() {
        document.getElementById("appDrawer")?.classList.add("open");
        document.getElementById("drawerBackdrop")?.classList.add("open");
        document.body.style.overflow = "hidden";
        fill();
    }
    function close() {
        document.getElementById("appDrawer")?.classList.remove("open");
        document.getElementById("drawerBackdrop")?.classList.remove("open");
        document.body.style.overflow = "";
    }
    async function save() {
        const lat = parseFloat(document.getElementById("settingsLat")?.value),
            lng = parseFloat(document.getElementById("settingsLng")?.value),
            locationName = (
                document.getElementById("settingsLocation")?.value ||
                "Koordinat tersimpan"
            ).trim();
        if (
            !Number.isFinite(lat) ||
            lat < -90 ||
            lat > 90 ||
            !Number.isFinite(lng) ||
            lng < -180 ||
            lng > 180
        ) {
            const status = document.getElementById("settingsStatus");
            if (status) status.textContent = "Koordinat belum valid.";
            return;
        }
        const settings = {
            lat,
            lng,
            location: locationName,
            theme: document.getElementById("settingsTheme")?.value || "light",
            compact: !!document.getElementById("settingsCompact")?.checked,
            largeText: !!document.getElementById("settingsLargeText")?.checked,
        };
        localStorage.setItem(KEY, JSON.stringify(settings));
        applyVisual(settings);
        try {
            window.FalakNative?.syncLocation(lat, lng, locationName);
        } catch (nativeError) {
            console.warn(nativeError);
        }
        if (typeof currentLat !== "undefined") {
            currentLat = lat;
            currentLng = lng;
            currentMarkazName = locationName;
            appState.locationName = locationName;
            await afterLocationChange();
        }
        const status = document.getElementById("settingsStatus");
        if (status) status.textContent = "Pengaturan disimpan.";
    }
    function reset() {
        localStorage.removeItem(KEY);
        location.reload();
    }
    window.FalakSettings = { read, open, close, save, reset, fill };
    window.openAppDrawer = open;
    window.closeAppDrawer = close;
    window.saveAppSettings = save;
    window.resetAppSettings = reset;
    document.addEventListener("DOMContentLoaded", fill);
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") close();
    });
})();

/* Android native integration: keep the complete annual calendar module in control. */
if (window.FalakCalendarAnnual)
    Object.assign(window, window.FalakCalendarAnnual);
