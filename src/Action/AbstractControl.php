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

use InvalidArgumentException,
    Bitnix\Form\Sanitizer,
    Bitnix\Form\SecurityException,
    Bitnix\Form\Util\Attributes;

/**
 * @version 0.1.0
 */
abstract class AbstractControl implements Control {

    private const DEFAULTS = [
        'index'   => 0,
        'charset' => null,
        'label'   => null,
        'usage'   => null
    ];

    /**
     * @var Attributes
     */
    private Attributes $attrs;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var array
     */
    private array $keys;

    /**
     * @var bool
     */
    private bool $multiple;

    /**
     * @var int
     */
    private int $index;

    /**
     * @var string
     */
    private ?string $label;

    /**
     * @var string
     */
    private ?string $usage;

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @param string $name
     * @param array $attrs
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, array $attrs, array $config) {
        $config += self::DEFAULTS;
        $id = $config['id'] ?? $attrs['id'] ?? null;

        $this->attrs = new Attributes(
            $this->bind($name, $id) + $attrs,
            \array_diff_key($config, self::DEFAULTS),
            $config['charset']
        );

        $this->label = $config['label'];
        $this->usage = $config['usage'];
        $this->index = $config['index'];
    }

    /**
     * @param Sanitizer $sanitizer
     * @param mixed $value
     * @return mixed
     */
    protected abstract function update(Sanitizer $sanitizer, $value);

    /**
     * @param string $name
     * @param null|string $id
     * @return array
     * @throws InvalidArgumentException
     */
    private function bind(string $name, string $id = null) : array {

        if (!\preg_match('~^[a-zA-Z][a-zA-Z0-9_:\-\.\[\]]*$~', $name)
            || \substr_count($name, '[]') > 1) {
            throw new InvalidArgumentException(\sprintf(
                'Unsupported widget name: "%s"', $name
            ));
        }

        $test = \str_replace(['[', ']'], ['-', ''], $name);
        if ($name === $test) {
            $this->name = $test;
            $this->keys = [$name];
            $this->multiple = false;
        } else {
            $test = \str_replace('--', '-', \trim($test, '-'));
            $this->keys = \array_filter(\explode('-', $test));
            $this->name = $this->keys[\array_key_last($this->keys)];
            $this->multiple = 0 === \substr_compare($name, '[]', -2, 2);
        }

        return [
            'name' => $name,
            'id'   => $id ?? $test
        ];
    }

    /**
     * @return bool
     */
    public function valid() : bool {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function errors() : array {
        return $this->errors;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function attribute(string $name, $default = null) {
        return $this->attrs->get($name, $default);
    }

    /**
     * @return Attributes
     */
    protected function attributes() : Attributes {
        return $this->attrs;
    }

    /**
     * @return string
     */
    public function name() : string {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function label() : ?string {
        return $this->label;
    }

    /**
     * @return null|string
     */
    public function usage() : ?string {
        return $this->usage;
    }

    /**
     * @param Sanitizer $sanitizer
     * @param array $input
     * @return mixed
     * @throws SecurityException
     * @throws \UnexpectedValueException
     */
    public function process(Sanitizer $sanitizer, array $input) {
        $value = $sanitizer->filter($this->name, $this->extract($input));
        $this->errors = $sanitizer->validate($this->name, $value);
        return $this->update($sanitizer, $value);
    }

    /**
     * @param array $input
     * @return bool
     */
    protected function accept(array $input) : bool {
        return false;
    }

    /**
     * @param array $input
     * @return mixed
     * @throws SecurityException
     */
    protected function extract(array $input) {

        foreach ($this->keys as $key) {
            if (!isset($input[$key])) {
                return null;
            }
            $input = $input[$key];
        }

        if (\is_array($input)) {
            if ($this->multiple) {
                $input = $this->multiple($input);
            } else if (!$this->accept($input)) {
                throw new SecurityException(\sprintf(
                    '%s received an unexpected value...', $this
                ));
            }
        } else if ($this->multiple) {
            throw new SecurityException(\sprintf(
                '%s received an unexpected value...', $this
            ));
        }

        return $input;
    }

    /**
     * @param array $input
     * @return mixed
     */
    protected function multiple(array $input) {
        return $input[$this->index] ?? null;
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return \sprintf(
            '%s (name=%s,id=%s)',
                static::CLASS,
                $this->name,
                $this->attrs->get('id')
        );
    }
}
