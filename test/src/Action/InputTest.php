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

use Bitnix\Form\Sanitizer,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class InputTest extends TestCase {

    public function testConstructor() {
        $input = $this->input();

        $this->assertEquals('test', $input->type());
        $this->assertEquals('test', $input->attribute('type'));
        $this->assertEquals('input', $input->attribute('name'));

        $this->assertSame('', $input->value());
        $this->assertSame('', $input->attribute('value'));

        $input = $this->input([
            'value' => 'zig',
            'class' => 'zag'
        ]);

        $this->assertSame('zig', $input->attribute('value'));
        $this->assertSame('zag', $input->attribute('class'));
    }

    public function testProcess() {
        $control = $this->input();

        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->once())
            ->method('filter')
            ->with('input', 'value')
            ->will($this->returnValue('value'));
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with('input', 'value')
            ->will($this->returnValue(['kaput']));

        $this->assertTrue($control->valid());
        $this->assertEquals([], $control->errors());

        $this->assertEquals('value', $control->process($validator, ['input' => 'value']));

        $this->assertFalse($control->valid());
        $this->assertEquals(['kaput'], $control->errors());

        $this->assertEquals('value', $control->attribute('value'));
    }

    public function testRender() {
        $input = $this->input();
        $html = $input->render(['type' => 'fake', 'value' => 'fake']);

        $this->assertStringStartsWith('<input ', $html);
        $this->assertStringContainsString('type="test"', $html);
        $this->assertStringContainsString('value=""', $html);
        $this->assertStringContainsString('name="input"', $html);
        $this->assertStringContainsString('id="input"', $html);

        $this->assertStringEndsWith('>', $html);
    }

    private function input(array $config = [], array $attrs = []) : Input {
        return $this->getMockBuilder(Input::CLASS)
            ->setConstructorArgs(['test', 'input', $config, $attrs])
            ->getMockForAbstractClass();
    }

}
