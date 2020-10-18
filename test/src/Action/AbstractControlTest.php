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
    Bitnix\Form\Sanitizer,
    Bitnix\Form\SecurityException,
    Bitnix\Form\Util\Attributes,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class AbstractControlTest extends TestCase {

    public function testIdentity() {
        $control = $this->control();
        $this->assertEquals('test', $control->name());
        $this->assertEquals('test', $control->attribute('name'));
        $this->assertEquals('test', $control->attribute('id'));

        $control = $this->control('test[]');
        $this->assertEquals('test', $control->name());
        $this->assertEquals('test[]', $control->attribute('name'));
        $this->assertEquals('test', $control->attribute('id'));

        $control = $this->control('test[control]');
        $this->assertEquals('control', $control->name());
        $this->assertEquals('test[control]', $control->attribute('name'));
        $this->assertEquals('test-control', $control->attribute('id'));

        $control = $this->control('test[control][field]');
        $this->assertEquals('field', $control->name());
        $this->assertEquals('test[control][field]', $control->attribute('name'));
        $this->assertEquals('test-control-field', $control->attribute('id'));

        $control = $this->control('test[control][field][]');
        $this->assertEquals('field', $control->name());
        $this->assertEquals('test[control][field][]', $control->attribute('name'));
        $this->assertEquals('test-control-field', $control->attribute('id'));
    }

    /**
     * @dataProvider unsupportedNames
     */
    public function testUnsupportedNames(string $name) {
        $this->expectException(\InvalidArgumentException::CLASS);
        $this->control($name);
    }

    public function unsupportedNames() : array {
        return [
            [''],
            ["\r\n\t "],
            ["multi\nline"],
            ['foo[][]']
        ];
    }

    public function testProcess() {
        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->once())
            ->method('filter')
            ->with('test', 'input')
            ->will($this->returnValue('input'));
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with('test', 'input')
            ->will($this->returnValue(['kaput']));

        $control = $this->control();
        $control
            ->expects($this->once())
            ->method('update')
            ->with($validator, 'input')
            ->will($this->returnValue('input'));

        $this->assertTrue($control->valid());
        $this->assertEquals([], $control->errors());

        $this->assertEquals('input', $control->process($validator, ['test' => 'input']));

        $this->assertFalse($control->valid());
        $this->assertEquals(['kaput'], $control->errors());
    }

    public function testAttributes() {
        $control = $this->control('test', ['foo' => 'bar'], ['foo' => 'baz', 'zig' => 'zag', 'label' => 'Test']);
        $this->assertEquals('bar', $control->attribute('foo'));
        $this->assertEquals('zag', $control->attribute('zig'));
        $this->assertNull($control->attribute('zoid'));
        $this->assertEquals('berg', $control->attribute('zoid', 'berg'));

        $reflection = new ReflectionObject($control);
        $method = $reflection->getMethod('attributes');
        $method->setAccessible(true);
        $attrs = $method->invoke($control);
        $this->assertInstanceOf(Attributes::CLASS, $attrs);
        $this->assertEquals('UTF-8', $attrs->charset());
        $this->assertEquals(['name' => 'test', 'id' => 'test', 'foo' => 'bar', 'zig' => 'zag'], $attrs->all());
    }

    public function testLabel() {
        $control = $this->control();
        $this->assertNull($control->label());

        $control = $this->control('test', [], ['label' => 'Test']);
        $this->assertEquals('Test', $control->label());
    }

    public function testUsage() {
        $control = $this->control();
        $this->assertNull($control->usage());

        $control = $this->control('test', [], ['usage' => 'Test usage']);
        $this->assertEquals('Test usage', $control->usage());
    }

    public function testExtract() {
        $control = $this->control();
        $reflection = new ReflectionObject($control);
        $method = $reflection->getMethod('extract');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($control, []));
        $this->assertEquals('foo', $method->invoke($control, ['test' => 'foo']));

        $control = $this->control('test[foo]');
        $reflection = new ReflectionObject($control);
        $method = $reflection->getMethod('extract');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($control, []));
        $this->assertEquals('bar', $method->invoke($control, ['test' => ['foo' => 'bar']]));

        $control = $this->control('test[]', [], ['index' => 1]);
        $reflection = new ReflectionObject($control);
        $method = $reflection->getMethod('extract');
        $method->setAccessible(true);
        $this->assertEquals('bar', $method->invoke($control, ['test' => ['foo', 'bar']]));

        $control = $this->control('test[foo][]', [], ['index' => 1]);
        $reflection = new ReflectionObject($control);
        $method = $reflection->getMethod('extract');
        $method->setAccessible(true);
        $this->assertEquals('baz', $method->invoke($control, ['test' => ['foo' => ['bar', 'baz']]]));
    }

    public function testExtractUnexpectedMultipleValuesError() {
        $this->expectException(SecurityException::CLASS);
        $control = $this->control();
        $reflection = new ReflectionObject($control);
        $method = $reflection->getMethod('extract');
        $method->setAccessible(true);
        $this->assertEquals('foo', $method->invoke($control, ['test' => ['foo', 'bar']]));
    }

    public function testExtractUnexpectedSingleValueError() {
        $this->expectException(SecurityException::CLASS);
        $control = $this->control('test[]');
        $reflection = new ReflectionObject($control);
        $method = $reflection->getMethod('extract');
        $method->setAccessible(true);
        $this->assertEquals('foo', $method->invoke($control, ['test' => 'foo']));
    }

    public function testToString() {
        $this->assertIsString((string) $this->control());
    }

    private function control($name = 'test', array $attrs = [], array $config = []) : Control {
        return $this->getMockBuilder(AbstractControl::CLASS)
            ->setConstructorArgs([$name, $attrs, $config])
            ->getMockForAbstractClass();
    }
}
