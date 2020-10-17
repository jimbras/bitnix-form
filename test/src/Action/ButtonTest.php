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
class ButtonTest extends TestCase {

    public function testConstructor() {

        $button = new Button('foo', 'Some <b>text</b>', [
            'label' => 'Foo',
            'usage' => 'Foo usage...'
        ]);

        $this->assertEquals('foo', $button->attribute('name'));
        $this->assertEquals('foo', $button->attribute('value'));
        $this->assertEquals('Foo', $button->label());
        $this->assertEquals('Foo usage...', $button->usage());
    }


    public function testRender() {
        $button = new Button('foo', 'Some <b>text</b>', [
            'value' => 'bar'
        ]);
        $html = $button->render();
        $this->assertStringStartsWith('<button', $html);
        $this->assertStringContainsString('name="foo"', $html);
        $this->assertStringContainsString('id="foo"', $html);
        $this->assertStringContainsString('value="bar"', $html);
        $this->assertStringContainsString('>Some <b>text</b><', $html);
        $this->assertStringEndsWith('</button>', $html);
    }

    public function testProcess() {
        $button = new Button('foo', 'Some <b>text</b>');

        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->once())
            ->method('filter')
            ->with('foo', 'bar')
            ->will($this->returnValue('bar'));
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with('foo', 'bar')
            ->will($this->returnValue([]));

        $this->assertEquals('bar', $button->process($validator, ['foo' => 'bar']));
        $this->assertTrue($button->valid());
        $this->assertEquals([], $button->errors());
    }

}
