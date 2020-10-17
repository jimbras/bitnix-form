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
class FileTest extends TestCase {

    public function testConstructor() {

        $input = new File('foo', [
            'value' => 'bar',
            'label' => 'Foo',
            'usage' => 'Foo usage...'
        ]);

        $this->assertEquals('file', $input->attribute('type'));
        $this->assertEquals('foo', $input->attribute('name'));
        $this->assertSame('', $input->attribute('value'));

        $this->assertEquals('Foo', $input->label());
        $this->assertEquals('Foo usage...', $input->usage());
    }

    public function testUpdateEmpty() {
        $input = new File('foo');
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

        $this->assertNull($input->process($validator, []));
        $this->assertSame('', $input->attribute('value'));
    }

    public function testUpdateSubmitted() {
        $input = new File('foo');
        $file = [
            'error' => 0,
            'name' => 'cool',
            'size' => 123,
            'tmp_name' => 'tmp',
            'type' => 'text/plain'
        ];
        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->once())
            ->method('filter')
            ->with('foo', $file)
            ->will($this->returnValue($file));
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with('foo', $file)
            ->will($this->returnValue([]));

        $this->assertEquals($file, $input->process($validator, ['foo' => $file]));
        $this->assertSame('', $input->attribute('value'));
    }

    public function testUpdateError() {
        $this->expectException(SecurityException::CLASS);

        $input = new File('foo');
        $validator = $this->createMock(Sanitizer::CLASS);
        $validator
            ->expects($this->never())
            ->method('filter');
        $validator
            ->expects($this->never())
            ->method('validate');

        $input->process($validator, ['foo' => []]);
    }

}
