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
final class ValidatorGroup implements Validator {

    /**
     * @var array
     */
    private array $group;

    /**
     * @param Validator ...$validators
     */
    public function __construct(Validator ...$validators) {
        $this->group = $validators;
    }

    /**
     * @param mixed $input
     * @return array
     * @throws \Bitnix\\Form\SecurityException
     * @throws \UnexpectedValueException
     */
    public function validate($input) : array {
        $errors = [];
        foreach ($this->group as $validator) {
            if ($failed = $validator->validate($input)) {
                $errors = \array_merge($errors, $failed);
            }
        }
        return $errors ? \array_keys(\array_flip($errors)) : [];
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }

}
