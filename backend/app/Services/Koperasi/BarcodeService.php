<?php

namespace App\Services\Koperasi;

class BarcodeService
{
    /**
     * Code 128 Encoding Patterns (Table B)
     * Format: 6 numbers representing alternating widths of bars and spaces.
     */
    protected static array $patterns = [
        0 => '212222', 1 => '222122', 2 => '222221', 3 => '121223', 4 => '121322',
        5 => '131222', 6 => '122213', 7 => '122312', 8 => '132212', 9 => '221213',
        10 => '221312', 11 => '231212', 12 => '112232', 13 => '122132', 14 => '122231',
        15 => '113222', 16 => '123122', 17 => '123221', 18 => '223211', 19 => '221132',
        20 => '221231', 21 => '213212', 22 => '223112', 23 => '312131', 24 => '311222',
        25 => '321122', 26 => '321221', 27 => '312212', 28 => '322112', 29 => '322211',
        30 => '212123', 31 => '212321', 32 => '232121', 33 => '111323', 34 => '131123',
        35 => '131321', 36 => '112313', 37 => '132113', 38 => '132311', 39 => '211313',
        40 => '231113', 41 => '231311', 42 => '112133', 43 => '112331', 44 => '132131',
        45 => '113123', 46 => '113321', 47 => '133121', 48 => '313121', 49 => '211331',
        50 => '231131', 51 => '213113', 52 => '213311', 53 => '213131', 54 => '311123',
        55 => '311321', 56 => '331121', 57 => '312113', 58 => '312311', 59 => '332111',
        60 => '314111', 61 => '221411', 62 => '431111', 63 => '111224', 64 => '111422',
        65 => '121124', 66 => '121421', 67 => '141122', 68 => '141221', 69 => '112214',
        70 => '112412', 71 => '122114', 72 => '122411', 73 => '142112', 74 => '142211',
        75 => '241211', 76 => '221114', 77 => '413111', 78 => '241112', 79 => '134111',
        80 => '111242', 81 => '121142', 82 => '121241', 83 => '114212', 84 => '124112',
        85 => '124211', 86 => '411212', 87 => '421112', 88 => '421211', 89 => '212141',
        90 => '214121', 91 => '412121', 92 => '111143', 93 => '111341', 94 => '131141',
        95 => '114113', 96 => '114311', 97 => '411113', 98 => '411311', 99 => '113141',
        100 => '114131', 101 => '311141', 102 => '411131', 
        103 => '211412', // Start Code A
        104 => '211214', // Start Code B (Standard ASCII alphanumeric)
        105 => '211232', // Start Code C (Numeric pairs)
        106 => '2331112' // Stop Code (7 elements: bar, space, bar, space, bar, space, bar)
    ];

    /**
     * Generate pure SVG string for Code 128 B barcode
     *
     * @param string $text The barcode text/SKU
     * @param int $moduleWidth Width of 1 module in pixels
     * @param int $height Height of bars in pixels
     * @param bool $showText Whether to render text inside SVG
     * @return string Valid SVG XML string
     */
    public static function getBarcodeSvg(string $text, int $moduleWidth = 2, int $height = 40, bool $showText = false): string
    {
        $text = trim($text);
        if (empty($text)) {
            $text = 'PROD-001';
        }

        // 1. Calculate values for Code 128 (Subset B)
        $startCode = 104; // START B
        $codes = [$startCode];
        $checksum = $startCode;

        $chars = str_split($text);
        foreach ($chars as $index => $char) {
            $val = ord($char) - 32;
            if ($val < 0 || $val > 95) {
                $val = 0; // Fallback for unsupported chars
            }
            $codes[] = $val;
            $checksum += $val * ($index + 1);
        }

        $checkDigit = $checksum % 103;
        $codes[] = $checkDigit;
        $codes[] = 106; // STOP

        // 2. Build bar pattern
        $patternString = '';
        foreach ($codes as $code) {
            $patternString .= self::$patterns[$code] ?? '';
        }

        // 3. Generate SVG Rectangles
        $totalModules = 0;
        $rectangles = '';
        $x = 10 * $moduleWidth; // Quiet zone left

        $isBar = true;
        for ($i = 0; $i < strlen($patternString); $i++) {
            $width = (int) $patternString[$i] * $moduleWidth;
            if ($isBar) {
                $rectangles .= sprintf('<rect x="%d" y="0" width="%d" height="%d" fill="#000000" />', $x, $width, $height);
            }
            $x += $width;
            $isBar = !$isBar;
        }

        $totalWidth = $x + (10 * $moduleWidth); // Quiet zone right
        $totalHeight = $height;

        if ($showText) {
            $totalHeight += 14;
            $rectangles .= sprintf(
                '<text x="%d" y="%d" font-family="monospace" font-size="10" font-weight="bold" text-anchor="middle" fill="#000000">%s</text>',
                $totalWidth / 2,
                $height + 11,
                htmlspecialchars($text, ENT_QUOTES, 'UTF-8')
            );
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="100%%" height="100%%" preserveAspectRatio="xMidYMid meet">%s</svg>',
            $totalWidth,
            $totalHeight,
            $rectangles
        );
    }

    /**
     * Get Base64 Encoded SVG Data URI for <img> tags
     */
    public static function getBarcodeBase64(string $text, int $moduleWidth = 2, int $height = 40): string
    {
        $svg = self::getBarcodeSvg($text, $moduleWidth, $height);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}