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

namespace Bitnix\Form\Input\Filter;

use UnexpectedValueException,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class StringFilterTest extends TestCase {

    public function testToString() {
        $this->assertIsString((string) $this->filter());
    }

    public function testFilterReturnsDefaultForNullInput() {
        $filter = $this->filter('default');
        $this->assertEquals('default', $filter->filter(null));
    }

    public function testFilterProcessesStringInput() {
        $filter = $this->filter();
        $filter
            ->expects($this->once())
            ->method('apply')
            ->with('foo')
            ->will($this->returnValue('bar'));
        $this->assertEquals('bar', $filter->filter('foo'));
    }

    public function testFilterProcessesListsWithValidInput() {
        $filter = $this->filter();
        $filter
            ->expects($this->exactly(3))
            ->method('apply')
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            ['bar', 'bar', 'bar'],
            $filter->filter(['a', 'b', 'c'])
        );
    }

    public function testFilterProcessesMapsWithValidInput() {
        $filter = $this->filter();
        $filter
            ->expects($this->exactly(3))
            ->method('apply')
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            ['foo' => 'bar', 'zig' => 'bar', 'zoid' => 'bar'],
            $filter->filter(['foo' => 'bar', 'zig' => 'zag', 'zoid' => 'berg'])
        );
    }

    public function testFilterFailsForNonSupportedValues() {
        $this->expectException(UnexpectedValueException::CLASS);
        $this->filter()->filter($this);
    }

    public function testFilterFailsForNonSupportedListValues() {
        $this->expectException(UnexpectedValueException::CLASS);
        $this->filter()->filter([$this]);
    }

    public function testFilterFailsForNonSupportedMapValues() {
        $this->expectException(UnexpectedValueException::CLASS);
        $this->filter()->filter(['foo' => $this]);
    }

    private function filter(string $default = null) : StringFilter {
        return $this->getMockBuilder(StringFilter::CLASS)
            ->setConstructorArgs([$default])
            ->getMockForAbstractClass();
    }

}
