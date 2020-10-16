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

use Bitnix\Form\Input\Validator;

/**
 * @version 0.1.0
 */
final class GreaterThanValidator implements Validator {

    use NumberSupport;

    /**
     * @var string
     */
    private string $error;

    /**
     * @var float
     */
    private float $min;

    /**
     * @var bool
     */
    private bool $inclusive;

    /**
     * @param string $error
     * @param float $min
     * @param bool $inclusive
     */
    public function __construct(string $error, float $min = 0.0, bool $inclusive = false) {
        $this->error = $error;
        $this->min = $min;
        $this->inclusive = $inclusive;
    }

    /**
     * @param mixed $input
     * @return array
     * @throws \Bitnix\\Form\SecurityException
     * @throws \UnexpectedValueException
     */
    public function validate($input) : array {
        $number = $this->enforceNumber($input);
        $valid = $this->inclusive
            ? ($number >= $this->min)
            : ($number > $this->min);
        return $valid ? [] : [$this->error];
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }

}
