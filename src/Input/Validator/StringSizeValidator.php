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

namespace Bitnix\Form\Input\Validator;

use InvalidArgumentException,
    Bitnix\Form\Input\Validator,
    Bitnix\Form\Util\Charset;

/**
 * @version 0.1.0
 */
final class StringSizeValidator implements Validator {

    use StringSupport;

    /**
     * @var string
     */
    private string $error;

    /**
     * @var string
     */
    private string $charset;

    /**
     * @var int
     */
    private int $min;

    /**
     * @var int
     */
    private int $max;

    /**
     * @param string $error
     * @param int $min
     * @param int $max
     * @param string $charset
     * @throws InvalidArgumentException
     */
    public function __construct(string $error, int $min = 0, int $max = 255, string $charset = 'utf-8') {
        if ($min < 0 || $max < $min) {
            throw new InvalidArgumentException(\sprintf(
                'Invalid string size range, min=%s, max=%s', $min, $max
            ));
        }
        $this->error = $error;
        $this->min = $min;
        $this->max = $max;
        $this->charset = Charset::filter($charset);
    }

    /**
     * @param mixed $input
     * @return array
     * @throws \Bitnix\\Form\SecurityException
     * @throws \UnexpectedValueException
     */
    public function validate($input) : array {
        $string = $this->enforceString($input);
        $size = \mb_strlen($string, $this->charset);

        return ($this->min && $size < $this->min || $size > $this->max)
            ? [$this->error]
            : [];
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
