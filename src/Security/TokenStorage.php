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
interface TokenStorage {

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name) : bool;

    /**
     * @param string $name
     * @param bool $delete
     * @return string
     * @throws TokenNotFound
     */
    public function fetch(string $name, bool $delete = true) : string;

    /**
     * @param string $name
     * @param string $value
     */
    public function store(string $name, string $value) : void;

    /**
     * @param string $name
     */
    public function remove(string $name) : void;

}
