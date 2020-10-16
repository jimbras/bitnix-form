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

use Bitnix\Form\Input\Validator,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class RequiredValidatorTest extends TestCase {

    /**
     * @dataProvider invalidInput
     */
    public function testInvalidInput($input) {
        $next = $this->createMock(Validator::CLASS);
        $next
            ->expects($this->never())
            ->method('validate');
        $validator = new RequiredValidator('kaput', $next);
        $this->assertEquals(['kaput'], $validator->validate($input));
    }

    public function invalidInput() : array {
        return [
            [null],
            [''],
            [[]]
        ];
    }

    /**
     * @dataProvider validInput
     */
    public function testValidInput($input) {
        $next = $this->createMock(Validator::CLASS);
        $next
            ->expects($this->once())
            ->method('validate')
            ->with($input)
            ->will($this->returnValue(['kaput']));
        $validator = new RequiredValidator('kaput', $next);
        $this->assertEquals(['kaput'], $validator->validate($input));
    }

    public function validInput() : array {
        return [
            [false],
            ["  "],
            [['a', 'b', 'c']],
            [$this]
        ];
    }

    public function testToString() {
        $this->assertIsString((string) new RequiredValidator('kaput'));
    }
}
