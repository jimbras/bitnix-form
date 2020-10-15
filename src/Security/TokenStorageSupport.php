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

namespace Bitnix\Form\Security;

/**
 * @version 0.1.0
 */
trait TokenStorageSupport {

    /**
     * @var array
     */
    private array $tokens;

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name) : bool {
        return isset($this->tokens[$name]);
    }

    /**
     * @param string $name
     * @param bool $delete
     * @return string
     * @throws TokenNotFound
     */
    public function fetch(string $name, bool $delete = true) : string {

        if (!isset($this->tokens[$name])) {
            throw new TokenNotFound(\sprintf(
                'Unable to find token: "%s"', $name
            ));
        }

        $value = $this->tokens[$name];

        if ($delete) {
            unset($this->tokens[$name]);
        }

        return $value;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function store(string $name, string $value) : void {
        $this->tokens[$name] = $value;
    }

    /**
     * @param string $name
     */
    public function remove(string $name) : void {
        if (isset($this->tokens[$name])) {
            unset($this->tokens[$name]);
        }
    }
}
