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
class StepValidatorTest extends TestCase {

    public function testInvalidStepValue() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new StepValidator(0, 'kaput');
    }

    public function testValidate() {
        $rule = new StepValidator(1, 'kaput');
        $this->assertEquals([], $rule->validate(1));
        $this->assertEquals([], $rule->validate(0));
        $this->assertEquals([], $rule->validate(10));
        $this->assertEquals([], $rule->validate('-10'));
        $this->assertEquals(['kaput'], $rule->validate(10.5));

        $rule = new StepValidator(0.25, 'kaput');
        $this->assertEquals([], $rule->validate(1.75));
        $this->assertEquals([], $rule->validate(0));
        $this->assertEquals([], $rule->validate(10.25));
        $this->assertEquals([], $rule->validate('-10'));
        $this->assertEquals(['kaput'], $rule->validate(10.51));
    }

    public function testInputMustBeNumeric() {
        $this->expectException(\UnexpectedValueException::CLASS);
        $rule = new StepValidator(1, 'kaput');
        $rule->validate('foo');
    }

    public function testToString() {
        $this->assertIsString((string) new StepValidator(1, 'kaput'));
    }

}
