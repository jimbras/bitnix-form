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

use InvalidArgumentException;

/**
 * @version 0.1.0
 */
final class Attributes {

    /**
     * @var string
     */
    private string $charset;

    /**
     * @var array
     */
    private array $protected;

    /**
     * @var array
     */
    private array $attributes;

    /**
     * @param array $protected
     * @param array $attributes
     * @param null|string $charset
     * @throws InvalidArgumentException
     */
    public function __construct(array $protected, array $attributes = [], string $charset = null) {
        $this->charset = Charset::filter($charset);
        $this->protected = $this->filter($protected);
        $this->attributes = \array_diff_key($this->filter($attributes), $this->protected);
    }

    /**
     * @param array $attrs
     * @return array
     * @throws InvalidArgumentException
     */
    private function filter(array $attrs) : array {
        $filtered = [];
        foreach ($attrs as $key => $value) {
            $filtered[$key] = $this->accept($key, $value);
        }
        return $filtered;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function accept($key, $value) {
        if (!\is_string($key) || !\preg_match('~^[a-zA-Z][a-zA-Z0-9_:\-\.]*$~', $key)) {
            throw new InvalidArgumentException(\sprintf(
                'Invalid attribute key: %s', $key
            ));
        }

        if (null === $value) {
            return '';
        } else if (\is_string($value)) {
            if (\preg_match('~\R~', $value)) {
                throw new InvalidArgumentException(\sprintf(
                    'Multiline attribute value found for key %s', $key
                ));
            }
            return \htmlspecialchars($value, \ENT_QUOTES, $this->charset, false);
        } else if (\is_numeric($value)) {
            return (string) $value;
        } else if (\is_bool($value)) {
            return $value;
        } else {
            throw new InvalidArgumentException(\sprintf(
                'Invalid attribute value for key %s: %s',
                    $key,
                    \gettype($value)
            ));
        }
    }

    /**
     * @return string
     */
    public function charset() : string {
        return $this->charset;
    }

    /**
     * @param array $override
     * @return array
     * @throws InvalidArgumentException
     */
    public function all(array $override = []) : array {
        return $this->protected
            + ($override
                ? ($this->filter($override) + $this->attributes)
                : $this->attributes
            );
    }

    /**
     * @param string $attr
     * @return bool
     */
    public function has(string $attr) : bool {
        return isset($this->protected[$attr]) || isset($this->attributes[$attr]);
    }

    /**
     * @param string $attr
     * @param mixed $default
     * @return mixed
     */
    public function get(string $attr, $default = null) {
        return $this->protected[$attr] ?? $this->attributes[$attr] ?? $default;
    }

    /**
     * @param string $attr
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function set(string $attr, $value) : void {

        $filtered = $this->accept($attr, $value);

        if (isset($this->protected[$attr])) {
            $this->protected[$attr] = $filtered;
        } else {
            $this->attributes[$attr] = $filtered;
        }
    }

    /**
     * @param string $attr
     */
    public function remove(string $attr) : void {
        if (isset($this->protected[$attr])) {
            $this->protected[$attr] = false;
        } else if (isset($this->attributes[$attr])) {
            unset($this->attributes[$attr]);
        }
    }

    /**
     * @param array $override
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(array $override = []) : string {
        $buffer = '';
        $attrs = $this->all($override);
        foreach ($attrs as $name => $value) {
            if (\is_string($value)) {
                $buffer .= ($name . '="' . $value . '" ');
            } else if (true === $value) {
                $buffer .= ($name . ' ');
            }
        }
        return \trim($buffer);
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
