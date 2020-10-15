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

namespace Bitnix\Form\Security;

use PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
abstract class TokenStorageTestCase extends TestCase {

    protected abstract function storage() : TokenStorage;

    public function testHas() {
        $storage = $this->storage();
        $this->assertFalse($storage->has('foo'));
        $storage->store('foo', 'bar');
        $this->assertTrue($storage->has('foo'));
    }

    public function testFetchError() {
        $this->expectException(TokenNotFound::CLASS);
        $storage = $this->storage();
        $storage->fetch('foo');
    }

    public function testFetch() {
        $storage = $this->storage();
        $storage->store('foo', 'bar');
        $this->assertEquals('bar', $storage->fetch('foo'));
        $this->assertFalse($storage->has('foo'));

        $storage->store('foo', 'bar');
        $this->assertEquals('bar', $storage->fetch('foo', false));
        $this->assertTrue($storage->has('foo'));
    }

    public function testRemove() {
        $storage = $this->storage();
        $storage->store('foo', 'bar');
        $storage->remove('foo');
        $this->assertFalse($storage->has('foo'));
    }

    public function testToString() {
        $this->assertIsString((string) $this->storage());
    }

}
