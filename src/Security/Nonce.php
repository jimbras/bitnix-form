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
    Bitnix\Form\Token;

/**
 * @version 0.1.0
 */
final class Nonce implements Token {

    private const OPTIONS = [
        'size'     => 32,
        'ttl'      => 0,
        'throttle' => 0
    ];

    /**
     * @var TokenStorage
     */
    private TokenStorage $tokens;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var int
     */
    private int $size;

    /**
     * @var int
     */
    private int $ttl;

    /**
     * @var int
     */
    private int $throttle;

    /**
     * @param TokenStorage $tokens
     * @param string $name
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function __construct(TokenStorage $tokens, string $name, array $options = []) {
        $options += self::OPTIONS;
        $this->name = 'token_' . \md5($name);
        $this->size = $this->value($options['size'], 'size', 16);
        $this->ttl = $this->value($options['ttl'], 'ttl');
        $this->throttle = $this->value($options['throttle'], 'throttle');
        $this->tokens = $tokens;
    }

    /**
     * @param int $value
     * @param string $key
     * @param int $min
     * @return int
     * @throws InvalidArgumentException
     */
    private function value(int $value, string $key, int $min = 0) : int {

        if ($value >= $min) {
            return $value;
        }

        throw new InvalidArgumentException(\sprintf(
            'Nonce %s value must be >= %s, got %s',
                $key,
                $min,
                $value
        ));
    }

    /**
     * @return string
     */
    public function name() : string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function generate() : string {
        $secret = \bin2hex(\random_bytes($this->size));
        $token = \bin2hex(\random_bytes($this->size));
        $time = ($this->throttle || $this->ttl) ? \time() : 0;
        $this->tokens->store($this->name, $token . '|' . $secret . '|' . $time);
        return $this->hash($token, $secret);
    }

    /**
     * @param string $token
     * @param string $salt
     * @return string
     */
    private function hash(string $token, string $secret) : string {
        return \hash_hmac('SHA256', $token, $secret, false);
    }

    /**
     * @return string
     */
    public function render() : string {
        return \sprintf(
            '<input type="hidden" name="%s" id="%s" value="%s">',
                $this->name,
                $this->name,
                $this->generate()
        );
    }

    /**
     * @param array $input
     * @throws TokenNotFound
     * @throws SecurityException
     */
    public function validate(array $input) : void {

        $received = $input[$this->name] ?? '';

        list ($token, $secret, $time)
            = \explode('|', $this->tokens->fetch($this->name), 3);


        if (!\hash_equals($this->hash($token, $secret), $received)) {
            throw new InvalidToken(\sprintf(
                'Invalid token value for %s', $this->name
            ));
        }

        if ($time) {
            $now = \time();

            if ($this->throttle && ($now < ($time + $this->throttle))) {
                throw new SecurityException(\sprintf(
                    'Token %s was submitted too fast...', $this->name
                ));
            }

            if ($this->ttl && ($now > ($time + $this->ttl))) {
                throw new ExpiredToken(\sprintf(
                    'Token %s is expired', $this->name
                ));
            }
        }

    }

    /**
     * @return string
     */
    public function __toString() : string {
        return \sprintf(
            '%s (name=%s)',
                self::CLASS,
                $this->name
        );
    }

}
