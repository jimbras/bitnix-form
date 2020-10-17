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
class TextareaTest extends TestCase {

    public function testConstructor() {

        $textarea = new Textarea('foo', 'Some <b>text</b>', [
            'label' => 'Foo',
            'usage' => 'Foo usage...'
        ]);

        $this->assertEquals('foo', $textarea->attribute('name'));
        $this->assertEquals('Some <b>text</b>', $textarea->content());
        $this->assertEquals('Foo', $textarea->label());
        $this->assertEquals('Foo usage...', $textarea->usage());
    }

    public function testRender() {
        $textarea = new Textarea('foo', 'Some <b>text</b>');
        $html = $textarea->render();
        $this->assertStringStartsWith('<textarea', $html);
        $this->assertStringContainsString('name="foo"', $html);
        $this->assertStringContainsString('id="foo"', $html);
        $this->assertStringContainsString('>Some &lt;b&gt;text&lt;/b&gt;<', $html);
        $this->assertStringEndsWith('</textarea>', $html);
    }

    public function testProcess() {
        $textarea = new Textarea('foo');

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
            ->will($this->returnValue(['kaput']));

        $this->assertEquals('bar', $textarea->process($validator, ['foo' => 'bar']));
        $this->assertFalse($textarea->valid());
        $this->assertEquals(['kaput'], $textarea->errors());
    }

}
