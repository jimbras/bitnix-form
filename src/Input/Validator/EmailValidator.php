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
final class EmailValidator implements Validator {

    use StringSupport;

    /**
     * @var string
     */
    private string $error;

    /**
     * @var int
     */
    private int $flag;

    /**
     * @param string $error
     * @param bool $unicode
     */
    public function __construct(string $error, bool $unicode = false) {
        $this->error = $error;
        $this->flag = $unicode ? \FILTER_FLAG_EMAIL_UNICODE : 0;
    }

    /**
     * @param mixed $input
     * @return array
     * @throws \Bitnix\\Form\SecurityException
     * @throws \UnexpectedValueException
     */
    public function validate($input) : array {
        return \filter_var($this->enforceString($input), \FILTER_VALIDATE_EMAIL, $this->flag)
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
