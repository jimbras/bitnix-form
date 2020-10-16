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
final class RequiredValidator implements Validator {

    /**
     * @var string
     */
    private string $error;

    /**
     * @var Validator
     */
    private ?Validator $next;

    /**
     * @param string $error
     * @param null|Validator $next
     */
    public function __construct(string $error, Validator $next = null) {
        $this->error = $error;
        $this->next = $next;
    }

    /**
     * @param mixed $input
     * @return array
     * @throws \Bitnix\\Form\SecurityException
     * @throws \UnexpectedValueException
     */
    public function validate($input) : array {
        if (null === $input || '' === $input || [] === $input) {
            return [$this->error];
        }
        return $this->next ? $this->next->validate($input) : [];
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
