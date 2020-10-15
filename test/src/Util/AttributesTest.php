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

namespace Bitnix\Form\Util;

use InvalidArgumentException,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class AttributesTest extends TestCase {

    public function testInvalidCharsetThrowsException() {
        $this->expectException(InvalidArgumentException::CLASS);
        new Attributes([], [], 'bad-charset-123');
    }

    public function testCharset() {
        $attrs = new Attributes([], []);
        $this->assertEquals('UTF-8', $attrs->charset());

        $attrs = new Attributes([], [], 'iso8859-1');
        $this->assertEquals('ISO-8859-1', $attrs->charset());
    }

    /**
     * @dataProvider invalidKeys
     */
    public function testInvalidAttributeKeyThrowsException($key) {
        $this->expectException(InvalidArgumentException::CLASS);
        new Attributes([$key => 'foo']);
    }

    public function invalidKeys() : array {
        return [
            [''],
            [10],
            [12.3],
            ['_'],
            ['123a'],
            ['a*b']
        ];
    }

    /**
     * @dataProvider validAttributes
     */
    public function testExpectedAttributes($in, $out) {
        $attrs = new Attributes(['foo' => $in]);
        $this->assertSame($out, $attrs->get('foo'));
    }

    public function validAttributes() : array {
        return [
            [null, ''],
            ['', ''],
            [1, '1'],
            [2.34, '2.34'],
            [true, true],
            [false, false]
        ];
    }

    /**
     * @dataProvider invalidAttributes
     */
    public function testInvalidAttributesThrowsException($attr) {
        $this->expectException(InvalidArgumentException::CLASS);
        new Attributes(['foo' => $attr]);
    }

    public function invalidAttributes() : array {
        return [
            ["multi\nline"],
            [['foo']],
            [$this],
            [STDIN]
        ];
    }

    public function testAll() {
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $this->assertEquals(['foo' => 'bar', 'zig' => 'zag'], $attrs->all());
        $this->assertEquals(['foo' => 'bar', 'zig' => 'zag'], $attrs->all(['foo' => 'baz']));
        $this->assertEquals(['foo' => 'bar', 'zig' => 'zoid'], $attrs->all(['zig' => 'zoid']));
        $this->assertEquals(['foo' => 'bar', 'zig' => '&quot;zoid&quot;'], $attrs->all(['zig' => '"zoid"']));
        $this->assertEquals(['foo' => 'bar', 'zig' => false], $attrs->all(['zig' => false]));
        $this->assertEquals(['foo' => 'bar', 'zig' => true], $attrs->all(['zig' => true]));
    }

    public function testAllError() {
        $this->expectException(InvalidArgumentException::CLASS);
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $attrs->all(['foo' => $this]);
    }

    public function testHas() {
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $this->assertTrue($attrs->has('foo'));
        $this->assertTrue($attrs->has('zig'));
        $this->assertFalse($attrs->has('zoid'));
    }

    public function testGet() {
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $this->assertEquals('bar', $attrs->get('foo'));
        $this->assertEquals('bar', $attrs->get('foo', 'baz'));
        $this->assertEquals('zag', $attrs->get('zig'));
        $this->assertNull($attrs->get('zoid'));
        $this->assertEquals('berg', $attrs->get('zoid', 'berg'));
    }

    public function testSet() {
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $attrs->set('foo', 'baz');
        $this->assertEquals('baz', $attrs->get('foo'));

        $attrs->set('zig', 'zoid');
        $this->assertEquals('zoid', $attrs->get('zig'));

        $attrs->set('zoid', 'berg');
        $this->assertEquals('berg', $attrs->get('zoid'));
        $attrs->set('zoid', '"berg"');
        $this->assertEquals('&quot;berg&quot;', $attrs->get('zoid'));
    }

    public function testSetError() {
        $this->expectException(InvalidArgumentException::CLASS);
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $attrs->set('foo', $this);
    }

    public function testRemove() {
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $attrs->remove('foo');
        $this->assertFalse($attrs->get('foo'));

        $attrs->remove('zig');
        $this->assertNull($attrs->get('zig'));
    }

    public function testRender() {
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $this->assertEquals('foo="bar" zig="zag"', $attrs->render());
        $this->assertEquals('foo="bar" zig="zag"', $attrs->render(['foo' => 'baz']));
        $this->assertEquals('foo="bar" zig="zoid"', $attrs->render(['zig' => 'zoid']));
        $this->assertEquals('foo="bar" zig="&quot;zoid&quot;"', $attrs->render(['zig' => '"zoid"']));
        $this->assertEquals('foo="bar"', $attrs->render(['zig' => false]));
        $this->assertEquals('foo="bar" zig', $attrs->render(['zig' => true]));
    }

    public function testRenderError() {
        $this->expectException(InvalidArgumentException::CLASS);
        $attrs = new Attributes(['foo' => 'bar'], ['zig' => 'zag']);
        $attrs->render(['foo' => $this]);
    }

    public function testToString() {
        $this->assertIsString((string) new Attributes([]));
    }
}
