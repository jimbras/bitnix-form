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

namespace Bitnix\Form\Input;

use PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class SummaryReporterTest extends TestCase {

    public function testFilters() {
        $filter = $this->createMock(Filter::CLASS);
        $filter
            ->expects($this->once())
            ->method('filter')
            ->with('bar')
            ->will($this->returnValue('baz'));

        $builder = new SummaryReporter();
        $reporter = $builder
            ->filter('foo', $filter)
            ->build();

        $this->assertEquals('baz', $reporter->filter('foo', 'bar'));
        $this->assertEquals('zag', $reporter->filter('zig', 'zag'));

        // filter reset
        $reporter = $builder->build();
        $this->assertEquals('bar', $reporter->filter('foo', 'bar'));
    }

    public function testReporters() {
        $validator1 = $this->createMock(Validator::CLASS);
        $validator1
            ->expects($this->once())
            ->method('validate')
            ->with('bar')
            ->will($this->returnValue(['kaput']));

        $validator2 = $this->createMock(Validator::CLASS);
        $validator2
            ->expects($this->once())
            ->method('validate')
            ->with('baz')
            ->will($this->returnValue(['kaput']));

        $builder = new SummaryReporter();
        $reporter = $builder
            ->validator('foo', $validator1, 'oops')
            ->validator('bar', $validator2, 'my bad...')
            ->build();

        $this->assertEquals(['kaput'], $reporter->validate('foo', 'bar'));
        $this->assertEquals(['kaput'], $reporter->validate('bar', 'baz'));
        $this->assertEquals([], $reporter->validate('zig', 'zag'));
        $this->assertEquals(['oops', 'my bad...'], $reporter->errors());

        // validator reset
        $reporter = $builder->build();
        $this->assertEquals([], $reporter->validate('foo', 'bar'));
    }

    public function testToString() {
        $builder = new SummaryReporter();
        $this->assertIsString((string) $builder);
        $this->assertIsString((string) $builder->build());
    }
}
