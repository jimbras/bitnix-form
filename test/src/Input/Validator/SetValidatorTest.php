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

use Bitnix\Form\SecurityException,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class SetValidatorTest extends TestCase {

    public function testInvalidConstructor() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new SetValidator('kaput', []);
    }

    public function testValidate() {
        $validator = new SetValidator('kaput', ['a', 'b', 'c']);
        $this->assertEquals([], $validator->validate('a'));
        $this->assertEquals(['kaput'], $validator->validate('d'));
    }

    public function testStrictValidation() {
        $this->expectException(SecurityException::CLASS);
        $validator = new SetValidator('kaput', ['a', 'b', 'c'], true);
        $validator->validate('d');
    }

    public function testToString() {
        $this->assertIsString((string) new SetValidator('kaput', ['a', 'b', 'c']));
    }
}
