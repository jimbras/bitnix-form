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
    Bitnix\Form\Input\Validator;

/**
 * @version 0.1.0
 */
final class RegexValidator implements Validator {

    use StringSupport;

    /**
     * @var string
     */
    private string $pattern;

    /**
     * @var string
     */
    private string $error;

    /**
     * @param string $pattern
     * @param string $error
     * @throws InvalidArgumentException
     */
    public function __construct(string $pattern, string $error) {
        if (false === @\preg_match($pattern, '')) {
            throw new InvalidArgumentException(\sprintf(
                'Invalid validation pattern: %s', $pattern
            ));
        }
        $this->pattern = $pattern;
        $this->error = $error;
    }

    /**
     * @param mixed $input
     * @return array
     * @throws \Bitnix\\Form\SecurityException
     * @throws \UnexpectedValueException
     */
    public function validate($input) : array {
        return \preg_match($this->pattern, $this->enforceString($input))
            ? []
            : [$this->error];
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
