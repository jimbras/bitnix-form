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
class RadioTest extends TestCase {

    public function testConstructor() {
        $input = new Radio('foo', [
            'value' => 'bar',
            'label' => 'Foo',
            'usage' => 'Foo usage...'
        ]);

        $this->assertEquals('radio', $input->attribute('type'));
        $this->assertEquals('foo', $input->attribute('name'));
        $this->assertEquals('bar', $input->attribute('value'));
        $this->assertFalse($input->attribute('checked'));
        $this->assertEquals('Foo', $input->label());
        $this->assertEquals('Foo usage...', $input->usage());

        $input = new Radio('foo', [
            'value' => 'bar',
            'checked' => true
        ]);

        $this->assertTrue($input->attribute('checked'));

        $input = new Radio('foo', [
            'value' => 'bar',
            'checked' => true
        ]);

        $this->assertTrue($input->attribute('checked'));
    }

    public function testUpdateNotSubmitted() {
        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->once())
            ->method('filter')
            ->with('foo', null)
            ->will($this->returnValue(null));
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with('foo', null)
            ->will($this->returnValue([]));

        $input = new Radio('foo', ['value' => 'bar', 'checked' => true]);

        $html = $input->render(['checked' => false]);
        $this->assertStringContainsString('checked', $html);

        $this->assertTrue($input->attribute('checked'));
        $this->assertNull($input->process($validator, []));
        $this->assertFalse($input->attribute('checked'));

        $html = $input->render(['checked' => true]);
        $this->assertStringNotContainsString('checked', $html);
    }

    public function testUpdateSubmitted() {
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

        $input = new Radio('foo', ['value' => 'bar']);

        $html = $input->render(['checked' => true]);
        $this->assertStringNotContainsString('checked', $html);

        $this->assertFalse($input->attribute('checked'));
        $this->assertEquals('bar', $input->process($validator, ['foo' => 'bar']));
        $this->assertTrue($input->attribute('checked'));

        $html = $input->render(['checked' => false]);
        $this->assertStringContainsString('checked', $html);
    }

}
