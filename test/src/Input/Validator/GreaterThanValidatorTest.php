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

use PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class GreaterThanValidatorTest extends TestCase {

    public function testValidate() {
        $rule = new GreaterThanValidator('kaput');
        $this->assertEquals(['kaput'], $rule->validate(-1));
        $this->assertEquals(['kaput'], $rule->validate(0));
        $this->assertEquals([], $rule->validate(1));

        $rule = new GreaterThanValidator('kaput', 10, true);
        $this->assertEquals(['kaput'], $rule->validate(-1));
        $this->assertEquals([], $rule->validate(10));
        $this->assertEquals([], $rule->validate('11'));
        $this->assertEquals([], $rule->validate(12.3));
    }

    public function testInputMustBeNumeric() {
        $this->expectException(\UnexpectedValueException::CLASS);
        $rule = new GreaterThanValidator('kaput');
        $rule->validate('foo');
    }

    public function testToString() {
        $this->assertIsString((string) new GreaterThanValidator('kaput'));
    }

}
