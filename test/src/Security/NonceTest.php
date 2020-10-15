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

use InvalidArgumentException,
    Bitnix\Form\ExpiredToken,
    Bitnix\Form\InvalidToken,
    Bitnix\Form\SecurityException,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class NonceTest extends TestCase {

    public function testInvalidTtlValue() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new Nonce(new RuntimeTokens(), 'test', ['ttl' => -1]);
    }

    public function testInvalidThrottleValue() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new Nonce(new RuntimeTokens(), 'test', ['throttle' => -1]);
    }

    public function testInvalidSizeValue() {
        $this->expectException(\InvalidArgumentException::CLASS);
        new Nonce(new RuntimeTokens(), 'test', ['size' => 15]);
    }

    public function testName() {
        $token = new Nonce(new RuntimeTokens(), 'test');
        $this->assertEquals('token_' . \md5('test'), $token->name());
    }

    public function testGenerate() {
        $tokens = new RuntimeTokens();
        $token = new Nonce($tokens, 'test');
        $token->generate();
        $this->assertTrue($tokens->has($token->name()));
    }

    public function testRender() {
        $tokens = new RuntimeTokens();
        $token = new Nonce($tokens, 'test');
        $html = $token->render();
        $this->assertTrue($tokens->has($token->name()));
        $this->assertStringStartsWith('<input type="hidden" ', $html);
        $this->assertStringContainsString(' name="' . $token->name() . '"', $html);
        $this->assertStringContainsString(' id="' . $token->name() . '"', $html);
        $this->assertStringContainsString(' value="', $html);
        $this->assertStringEndsWith('>', $html);
    }

    public function testNonceValid() {
        $tokens = new RuntimeTokens();
        $token = new Nonce($tokens, 'test');
        $token->validate([$token->name() => $token->generate()]);
        $this->assertTrue(true);
    }

    public function testNonceValidTtl() {
        $tokens = new RuntimeTokens();
        $token = new Nonce($tokens, 'test', ['ttl' => 10]);
        $token->validate([$token->name() => $token->generate()]);
        $this->assertTrue(true);
    }

    public function testNonceExpiredTtl() {
        $this->expectException(\Bitnix\Form\ExpiredToken::CLASS);
        $tokens = new RuntimeTokens();
        $token = new Nonce($tokens, 'test', ['ttl' => 10]);
        $value = $token->generate();
        $stored = \explode('|', $tokens->fetch($token->name()));
        $mocked = $stored[0] . '|' . $stored[1] . '|' . ($stored[2] - 20);
        $tokens->store($token->name(), $mocked);
        $token->validate([$token->name() => $value]);
        $this->assertTrue(true);
    }

    public function testNonceInvalidThrottle() {
        $this->expectException(\Bitnix\Form\SecurityException::CLASS);
        $tokens = new RuntimeTokens();
        $token = new Nonce($tokens, 'test', ['throttle' => 10]);
        $token->validate([$token->name() => $token->generate()]);
        $this->assertTrue(true);
    }

    public function testNonceValidThrottle() {
        $tokens = new RuntimeTokens();
        $token = new Nonce($tokens, 'test', ['throttle' => 10]);
        $value = $token->generate();
        $stored = \explode('|', $tokens->fetch($token->name()));
        $mocked = $stored[0] . '|' . $stored[1] . '|' . ($stored[2] - 20);
        $tokens->store($token->name(), $mocked);
        $token->validate([$token->name() => $value]);
        $this->assertTrue(true);
    }

    public function testInvalidToken() {
        $this->expectException(\Bitnix\Form\InvalidToken::CLASS);
        $token = new Nonce(new RuntimeTokens(), 'test');
        $token->generate();
        $token->validate([$token->name() => 'tampered...']);
    }

    public function testToString() {
        $this->assertIsString((string) new Nonce(new RuntimeTokens(), 'test'));
    }
}
