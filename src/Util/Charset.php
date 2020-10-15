<?php declare(strict_types=1);

/**
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program. If not, see <https://www.gnu.org/licenses/agpl-3.0.txt>.
 */

namespace Bitnix\Form\Util;

use InvalidArgumentException;

/**
 * @version 0.1.0
 */
final class Charset {

    const DEFAULT_CHARSET = 'UTF-8';

    private const CHARSETS = [
        'utf-8'        => 'UTF-8',
        'iso-8859-1'   => 'ISO-8859-1',
        'iso8859-1'    => 'ISO-8859-1',
        'iso-8859-5'   => 'ISO-8859-5',
        'iso8859-5'    => 'ISO-8859-5',
        'iso-8859-15'  => 'ISO-8859-15',
        'iso8859-15'   => 'ISO-8859-15',
        'cp866'        => 'cp866',
        'ibm866'       => 'cp866',
        '866'          => 'cp866',
        'cp1251'       => 'cp1251',
        'windows-1251' => 'cp1251',
        'win-1251'     => 'cp1251',
        '1251'         => 'cp1251',
        'cp1252'       => 'cp1252',
        'windows-1252' => 'cp1252',
        '1252'         => 'cp1252',
        'koi8-R'       => 'KOI8-R',
        'koi8-ru'      => 'KOI8-R',
        'koi8r'        => 'KOI8-R',
        'big5'         => 'BIG5',
        '950'          => 'BIG5',
        'gb2312'       => 'GB2312',
        '936'          => 'GB2312',
        'big5-hkscs'   => 'BIG5-HKSCS',
        'shift_jis'    => 'Shift_JIS',
        'sjis'         => 'Shift_JIS',
        'sjis-win'     => 'Shift_JIS',
        'cp932'        => 'Shift_JIS',
        '932'          => 'Shift_JIS',
        'euc-jp'       => 'EUC-JP',
        'eucjp'        => 'EUC-JP',
        'eucjp-win'    => 'EUC-JP',
        'macroman'     => 'MacRoman'
    ];

    private function __construct() {}

    /**
     * @param null|string $charset
     * @return string
     * @throws InvalidArgumentException
     */
    public static function filter(string $charset = null) : string {

        if (null === $charset) {
            return self::DEFAULT_CHARSET;
        }

        if (null === ($encoding = self::CHARSETS[\strtolower($charset)] ?? null)) {
            throw new InvalidArgumentException(\sprintf(
                'Unsupported charset: %s', $charset
            ));
        }

        return $encoding;
    }
}
