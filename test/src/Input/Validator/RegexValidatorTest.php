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
class RegexValidatorTest extends TestCase {

    public function testInvalidRegex() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new RegexValidator('kaput', '~[a-z]+');
    }

    public function testValidator() {
        $rule = new RegexValidator('kaput', '~^[a-z]+$~');
        $this->assertEquals([], $rule->validate('foo'));
        $this->assertEquals(['kaput'], $rule->validate('foo bar'));
    }

    public function testValidateRequiresString() {
        $this->expectException(\UnexpectedValueException::CLASS);
        $rule = new RegexValidator('kaput', '~^[a-z]+$~');
        $rule->validate([]);
    }

    public function testToString() {
        $this->assertIsString((string) new RegexValidator('kaput', '~^[a-z]+$~'));
    }

}
