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

use Bitnix\Form\Input\Filter,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class FilterGroupTest extends TestCase {

    public function testGroup() {
        $filter1 = $this->createMock(Filter::CLASS);
        $filter1
            ->expects($this->once())
            ->method('filter')
            ->with(null)
            ->will($this->returnValue(null));

        $filter2 = $this->createMock(Filter::CLASS);
        $filter2
            ->expects($this->once())
            ->method('filter')
            ->with(null)
            ->will($this->returnValue('filtered'));

        $filter = new FilterGroup($filter1, $filter2);
        $this->assertEquals('filtered', $filter->filter(null));
    }

    public function testToString() {
        $this->assertIsString((string) new FilterGroup());
    }

}
