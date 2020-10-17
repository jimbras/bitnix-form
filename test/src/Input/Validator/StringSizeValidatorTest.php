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
class StringSizeValidatorTest extends TestCase {

    public function testInvalidMinSize() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new StringSizeValidator('kaput', -1);
    }

    public function testInvalidSizeRange() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new StringSizeValidator('kaput', 10, 5);
    }

    public function testInvalidCharset() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new StringSizeValidator('kaput', 10, 20, 'bad-charset');
    }

    public function testValidate() {
        $rule = new StringSizeValidator('kaput');
        $this->assertEquals([], $rule->validate(''));
        $this->assertEquals([], $rule->validate(\str_repeat('x', 255)));
        $this->assertEquals(['kaput'], $rule->validate(\str_repeat('x', 256)));

        $rule = new StringSizeValidator('kaput', 10, 20);
        $this->assertEquals(['kaput'], $rule->validate(''));
        $this->assertEquals([], $rule->validate(\str_repeat('x', 10)));
        $this->assertEquals([], $rule->validate(\str_repeat('x', 20)));
        $this->assertEquals(['kaput'], $rule->validate(\str_repeat('x', 21)));
    }

    public function testRuleBadInput() {
        $this->expectException(\UnexpectedValueException::CLASS);
        $rule = new StringSizeValidator('kaput');
        $rule->validate($this);
    }

    public function testToString() {
        $this->assertIsString((string) new StringSizeValidator('kaput'));
    }

}
