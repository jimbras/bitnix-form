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

use Bitnix\Form\SecurityException,
    Bitnix\Form\Input\Reporter,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class AbstractFormTest extends TestCase {

    public function testDefaultConstructor() {
        $form = $this->form();
        $this->assertEquals('test', $form->attribute('id'));
        $this->assertEquals(AbstractForm::GET, $form->attribute('method'));
        $this->assertEquals(AbstractForm::URLENCODED, $form->attribute('enctype'));
        $this->assertEquals('utf-8', $form->attribute('accept-charset'));
        $this->assertFalse($form->attribute('action'));
    }

    public function testCustomConstructor() {
        $form = $this->form('test', [
            'method'  => 'POST',
            'enctype' => 'multipart/form-data',
            'accept-charset' => 'iso-8859-1',
            'action' => 'foo'
        ]);
        $this->assertEquals('test', $form->attribute('id'));
        $this->assertEquals(AbstractForm::POST, $form->attribute('method'));
        $this->assertEquals(AbstractForm::MULTIPART, $form->attribute('enctype'));
        $this->assertEquals('iso-8859-1', $form->attribute('accept-charset'));
        $this->assertEquals('foo', $form->attribute('action'));
    }

    public function testAcceptedCharsets() {
        $form = $this->form('test', ['accept-charset' => 'utf-8 iso-8859-1']);
        $this->assertEquals('utf-8 iso-8859-1', $form->attribute('accept-charset'));
    }

    public function testInvalidCharset() {
        $this->expectException(\InvalidArgumentException::CLASS);
        $this->form('test', ['accept-charset' => 'utf-8 fake-666']);
    }

    public function testInvalidId() {
        $this->expectException(\InvalidArgumentException::CLASS);
        $this->form("\tes\t");
    }

    public function testInvalidMethod() {
        $this->expectException(\InvalidArgumentException::CLASS);
        $this->form('test', ['method' => 'foo']);
    }

    public function testInvalidEnctype() {
        $this->expectException(\InvalidArgumentException::CLASS);
        $this->form('test', ['enctype' => 'foo']);
    }

    public function testError() {
        $form = $this->form();

        $this->assertTrue($form->valid());
        $this->assertEquals([], $form->errors());

        $form->error('foo');
        $form->error('bar');
        $form->error('foo');

        $this->assertFalse($form->valid());
        $this->assertEquals(['foo', 'bar'], $form->errors());
    }

    public function testWidget() {
        $widget = $this->createMock(Control::CLASS);
        $widget
            ->expects($this->any())
            ->method('name')
            ->will($this->returnValue('control'));

        $form = $this->form('test', [], $widget);
        $this->assertSame($widget, $form->widget('control'));
    }

    public function testWidgetError() {
        $this->expectException(\LogicException::CLASS);
        $form = $this->form();
        $form->widget('control');
    }

    public function testRender() {
        $form = $this->form('test', ['action' => 'foo']);
        $html = $form->open(['action' => 'bar']);
        $this->assertStringStartsWith('<form', $html);
        $this->assertStringEndsWith('>', $html);
        $this->assertStringContainsString('id="test"', $html);
        $this->assertStringContainsString('action="foo"', $html);
        $this->assertStringContainsString('method="get"', $html);
        $this->assertStringContainsString('accept-charset="utf-8"', $html);
        $this->assertStringContainsString('enctype="application/x-www-form-urlencoded"', $html);

        $this->assertEquals('</form>', $form->close());
    }

    public function testProcess() {
        $input = ['foo' => 'bar'];

        $control = $this->createMock(Control::CLASS);
        $control
            ->expects($this->any())
            ->method('name')
            ->will($this->returnValue('foo'));
        $control
            ->expects($this->once())
            ->method('process')
            ->will($this->returnValue('bar'));

        $form = $this->form('test', [], $control);
        $this->assertEquals($input, $form->process($input));
        $this->assertEquals([], $form->errors());
        $this->assertTrue($form->valid());
    }

    public function testProcessError() {
        $input = ['foo' => 'bar'];

        $control = $this->createMock(Control::CLASS);
        $control
            ->expects($this->any())
            ->method('name')
            ->will($this->returnValue('foo'));
        $control
            ->expects($this->once())
            ->method('process')
            ->will($this->returnValue('bar'));

        $validator = $this->createMock(Reporter::CLASS);
        $validator
            ->expects($this->once())
            ->method('errors')
            ->will($this->returnValue(['kaput']));

        $form = $this->form('test', [], $control);
        $form
            ->expects($this->once())
            ->method('reporter')
            ->will($this->returnValue($validator));

        $this->assertNull($form->process($input));
        $this->assertEquals(['kaput'], $form->errors());
        $this->assertFalse($form->valid());
    }

    public function testProcessAttack() {
        $this->expectException(SecurityException::CLASS);
        $control = $this->createMock(Control::CLASS);
        $control
            ->expects($this->any())
            ->method('name')
            ->will($this->returnValue('foo'));
        $control
            ->expects($this->once())
            ->method('process')
            ->will($this->throwException(new \UnexpectedValueException()));

        $form = $this->form('test', [], $control);
        $form->process(['foo' => 'bar']);
    }

    public function testToString() {
        $this->assertIsString((string) $this->form());
    }

    private function form(string $name = 'test', array $attrs = [], Control ...$widgets) : AbstractForm {
        return $this->getMockBuilder(AbstractForm::CLASS)
            ->setConstructorArgs([$name, $attrs, ...$widgets])
            ->getMockForAbstractClass();
    }
}
