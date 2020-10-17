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
final class UrlValidator implements Validator {

    use StringSupport;

    const DEFAULT_FLAGS
        = \FILTER_FLAG_SCHEME_REQUIRED | \FILTER_FLAG_HOST_REQUIRED;

    /**
     * @var string
     */
    private string $error;

    /**
     * @var array
     */
    private array $protocols;

    /**
     * @var int
     */
    private int $flags;

    /**
     * @param string $error
     * @param array $protocols
     * @param int $flags
     */
    public function __construct(string $error, array $protocols = [], int $flags = self::DEFAULT_FLAGS) {
        $this->error = $error;
        $this->flags = $flags;
        $this->protocols = $protocols
            ? \array_flip(\array_map(fn($proto) => \strtolower($proto), $protocols))
            : [];
    }

    /**
     * @param mixed $input
     * @return array
     * @throws \Bitnix\\Form\SecurityException
     * @throws \UnexpectedValueException
     */
    public function validate($input) : array {
        $valid = \filter_var($this->enforceString($input), \FILTER_VALIDATE_URL, $this->flags);
        if ($valid && $this->protocols) {
            $proto = \strtolower((string) \parse_url($input, \PHP_URL_SCHEME));
            if (!isset($this->protocols[$proto])) {
                $valid = false;
            }
        }
        return $valid ? [] : [$this->error];
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
