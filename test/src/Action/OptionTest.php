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
class OptionTest extends TestCase {

    public function testConstructor() {
        $option = new Option('Foo');
        $this->assertEquals('Foo', $option->label());
        $this->assertEquals('Foo', $option->value());
        $this->assertFalse($option->selected());

        $option = new Option('Foo', 'foo', true);
        $this->assertEquals('Foo', $option->label());
        $this->assertEquals('foo', $option->value());
        $this->assertTrue($option->selected());
    }

    public function testSelect() {
        $option = new Option('Foo');
        $this->assertFalse($option->selected());
        $this->assertTrue($option->select(true));
        $this->assertTrue($option->selected());
        $this->assertFalse($option->select(false));
        $this->assertFalse($option->selected());
    }

    public function testRender() {
        $option = new Option('Foo');
        $this->assertEquals('<option>Foo</option>', $option->render());

        $option = new Option('Foo', 'foo');
        $this->assertEquals('<option value="foo">Foo</option>', $option->render());

        $option = new Option('Foo', null, true);
        $this->assertEquals('<option selected>Foo</option>', $option->render());

        $option = new Option('Foo', 'foo', true);
        $this->assertEquals('<option value="foo" selected>Foo</option>', $option->render());
    }

    public function testSanitize() {
        $option = new Option('<b>Foo</b>', '"foo"');
        $this->assertEquals('Foo', $option->label());
        $this->assertEquals('"foo"', $option->value());
        $this->assertEquals('<option value="&quot;foo&quot;">Foo</option>', $option->render());

        $option = new Option("<b>F\noo</b>", "f\noo");
        $this->assertEquals('Foo', $option->label());
        $this->assertEquals('foo', $option->value());
        $this->assertEquals('<option value="foo">Foo</option>', $option->render());
    }

    public function testToString() {
        $this->assertIsString((string) new Option('Foo'));
    }
}
