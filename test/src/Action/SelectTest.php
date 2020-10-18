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
    Bitnix\Form\SecurityException,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class SelectTest extends TestCase {

    public function testInvalidConstructor() {
        $this->expectException(\InvalidArgumentException::CLASS);
        $select = new Select('foo', new Optlist([
            new Option('Foo', null, true),
            new Option('Bar', null, true)
        ]));
    }

    public function testSelected() {
        $select = new Select('foo', new Optlist([
            new Option('Foo'),
            new Option('Bar')
        ]));
        $this->assertNull($select->selected());

        $select = new Select('foo', new Optlist([
            new Option('Foo'),
            new Option('Bar', null, true)
        ]));
        $this->assertEquals('Bar', $select->selected());

        $select = new Select('foo[]', new Optlist([
            new Option('Foo'),
            new Option('Bar', null, true)
        ]));
        $this->assertEquals(['Bar'], $select->selected());

        $select = new Select('foo[]', new Optlist([
            new Option('Foo', 'f', true),
            new Option('Bar', null, true)
        ]));
        $this->assertEquals(['f', 'Bar'], $select->selected());
    }

    public function testRender() {
        $select = new Select('foo', new Optlist([
            new Option('Foo')
        ]));
        $html = $select->render(['multiple' => true]);
        $this->assertStringStartsWith('<select', $html);
        $this->assertStringContainsString(' name="foo"', $html);
        $this->assertStringContainsString(' id="foo"', $html);
        $this->assertStringNotContainsString('multiple', $html);
        $this->assertStringContainsString('<option>Foo</option>', $html);
        $this->assertStringEndsWith('</select>', $html);

        $select = new Select('foo[]', new Optlist([
            new Option('Foo')
        ]));
        $html = $select->render();
        $this->assertStringContainsString(' multiple', $html);
    }

    public function testProcessSubmitted() {
        $select = new Select('foo', new Optlist([
            new Option('Bar')
        ]));
        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->once())
            ->method('filter')
            ->with('foo', 'Bar')
            ->will($this->returnValue('Bar'));
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with('foo', 'Bar')
            ->will($this->returnValue([]));

        $this->assertEquals('Bar', $select->process($validator, ['foo' => 'Bar']));
        $this->assertTrue($select->valid());
        $this->assertEquals([], $select->errors());
        $this->assertEquals('Bar', $select->selected());
    }

    public function testProcessSubmittedMultiple() {
        $select = new Select('foo[]', new Optlist([
            new Option('Bar'),
            new Option('Baz')
        ]));
        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->once())
            ->method('filter')
            ->with('foo', ['Bar'])
            ->will($this->returnValue(['Bar']));
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with('foo', ['Bar'])
            ->will($this->returnValue([]));

        $this->assertEquals(['Bar'], $select->process($validator, ['foo' => ['Bar']]));
        $this->assertTrue($select->valid());
        $this->assertEquals([], $select->errors());
        $this->assertEquals(['Bar'], $select->selected());
    }

    public function testProcessError1() {
        $this->expectException(SecurityException::CLASS);
        $select = new Select('foo', new Optlist([
            new Option('Bar'),
            new Option('Baz')
        ]));
        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->never())
            ->method('filter');
        $validator
            ->expects($this->never())
            ->method('validate');

        $select->process($validator, ['foo' => ['Bar']]);

    }

    public function testProcessError2() {
        $this->expectException(SecurityException::CLASS);
        $select = new Select('foo[]', new Optlist([
            new Option('Bar'),
            new Option('Baz')
        ]));
        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->never())
            ->method('filter');
        $validator
            ->expects($this->never())
            ->method('validate');

        $select->process($validator, ['foo' => 'Bar']);

    }

    public function testToString() {
        $this->assertIsString((string) new Select('foo'));
    }
}
