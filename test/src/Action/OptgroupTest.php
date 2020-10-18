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

use PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class OptgroupTest extends TestCase {

    public function testConstructor() {
        $options = new Optgroup([
            'Foo' => [
                new Option('Bar'),
                new Option('Baz')
            ],
            'Zig' => [
                new Option('Zag'),
                new Option('Zoid')
            ]
        ]);
        $this->assertEquals([], $options->selected());

        $options = new Optgroup([
            'Foo' => [
                new Option('Bar'),
                new Option('Baz')
            ],
            'Zig' => [
                new Option('Zag', null, true),
                new Option('Zoid')
            ]
        ]);
        $this->assertEquals(['Zag'], $options->selected());
    }

    public function testSelect() {
        $options = new Optgroup([
            'Foo' => [
                new Option('Bar'),
                new Option('Baz')
            ],
            'Zig' => [
                new Option('Zag'),
                new Option('Zoid')
            ]
        ]);
        $this->assertEquals(['Baz', 'Zag'], $options->select('Baz', 'Zag'));
        $this->assertEquals(['Baz', 'Zag'], $options->selected());

        $this->assertEquals([], $options->select());
        $this->assertEquals([], $options->selected());
    }

    public function testRender() {
        $options = new Optgroup([
            'Foo' => [
                new Option('Bar'),
                new Option('Baz')
            ],
            'Zig' => [
                new Option('Zag'),
                new Option('Zoid')
            ]
        ]);
        $html = $options->render();
        $this->assertStringContainsString('<optgroup label="Foo">', $html);
        $this->assertStringContainsString('<option>Bar</option>', $html);
        $this->assertStringContainsString('<option>Baz</option></optgroup>', $html);

        $this->assertStringContainsString('<optgroup label="Zig">', $html);
        $this->assertStringContainsString('<option>Zag</option>', $html);
        $this->assertStringContainsString('<option>Zoid</option></optgroup>', $html);
    }

    public function testToString() {
        $this->assertIsString((string) new Optgroup([]));
    }
}
