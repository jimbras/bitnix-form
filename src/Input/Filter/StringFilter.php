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

namespace Bitnix\Form\Input\Filter;

use UnexpectedValueException,
    Bitnix\Form\Input\Filter;

/**
 * @version 0.1.0
 */
abstract class StringFilter implements Filter {

    /**
     * @var string
     */
    private ?string $default;

    /**
     * @param null|string $default
     */
    public function __construct(string $default = null) {
        $this->default = $default;
    }

    /**
     * @param string $input
     * @return string
     * @throws \Bitnix\\Form\SecurityException
     */
    protected abstract function apply(string $input) : ?string;

    /**
     * @param mixed $input
     * @return mixed
     * @throws \Bitnix\\Form\SecurityException
     * @throws \UnexpectedValueException
     */
    public function filter($input) {
        if (null === $input) {
            return $this->default;
        } else if (\is_string($input)) {
            return $this->apply($input);
        } else if (\is_array($input)) {
            $result = [];
            foreach ($input as $key => $value) {
                $result[$key] = $this->filter($value);
            }
            return $result;
        } else {
            throw new \UnexpectedValueException(\sprintf(
                'Expected string or null but got %s', \gettype($input)
            ));
        }
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return static::CLASS;
    }
}
