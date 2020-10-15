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

use RuntimeException,
    TypeError;

/**
 * @version 0.1.0
 */
final class Files {

    private const KEYS = [
        'error',
        'name',
        'size',
        'tmp_name',
        'type'
    ];

    private function __construct() {}

    /**
     * @param array $files
     * @param callable $converter
     * @return mixed
     * @throws RuntimeException
     */
    private static function fixFile(array $file, callable $converter) {

        if (!self::accept($file)) {
            throw new RuntimeException('Malformed uploaded file data');
        }

        if (!\is_array($file['name'])) {
            return $converter($file);
        }

        $result = [];

        foreach ($file['name'] as $key => $value) {

            $fixed = self::fixFile([
                'error'    => $file['error'][$key],
                'name'     => $value,
                'type'     => $file['type'][$key],
                'tmp_name' => $file['tmp_name'][$key],
                'size'     => $file['size'][$key],
            ], $converter);

            if (null !== $fixed) {
                $result[$key] = $fixed;
            }
        }

        return $result;
    }

    /**
     * @param array $file
     * @return bool
     */
    public static function accept(array $file) : bool {
        $keys = \array_keys($file);
        sort($keys);
        return self::KEYS === $keys;
    }

    /**
     * @param null|array $files
     * @param null|callable $converter
     * @return array
     * @throws RuntimeException
     */
    public static function fix(array $files = null, callable $converter = null) : array {

        if (null === $files) {
            $files = $_FILES;
        }

        if (empty($files)) {
            return $files;
        }

        if (null === $converter) {
            $converter = fn(array $file) => $file['error'] === 4 ? null : $file;
        }

        $filtered = [];

        try {
            foreach ($files as $key => $file) {
                $fixed = self::fixFile($file, $converter);
                if (null !== $fixed) {
                    $filtered[$key] = self::fixFile($file, $converter);
                }
            }
        } catch (TypeError $x) {
            throw new RuntimeException('Malformed uploaded file data');
        }

        return $filtered;
    }

}
