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

namespace Bitnix\Form\Action;

use ReflectionObject,
    Bitnix\Form\SecurityException,
    Bitnix\Form\Token,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class SecureFormTest extends TestCase {

    public function testRender() {
        $token = $this->createMock(Token::CLASS);
        $token
            ->expects($this->once())
            ->method('render')
            ->will($this->returnValue('<!-- token -->'));

        $form = $this->form($token);
        $html = $form->close();
        $this->assertEquals('<!-- token --></form>', $html);
    }

    public function testProcess() {
        $token = $this->createMock(Token::CLASS);
        $token
            ->expects($this->once())
            ->method('validate')
            ->with([]);
        $this->form($token)->process([]);
    }

    public function testToken() {
        $token = $this->createMock(Token::CLASS);
        $form = $this->form($token);
        $getter = (new ReflectionObject($form))->getMethod('token');
        $getter->setAccessible(true);
        $this->assertSame($token, $getter->invoke($form));
    }

    private function form(Token $token) : AbstractForm {
        return $this->getMockBuilder(SecureForm::CLASS)
            ->setConstructorArgs([$token, 'test', []])
            ->getMockForAbstractClass();
    }
}
