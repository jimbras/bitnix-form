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
    LogicException,
    UnexpectedValueException,
    Bitnix\Form\Form,
    Bitnix\Form\SecurityException,
    Bitnix\Form\Widget,
    Bitnix\Form\Util\Attributes,
    Bitnix\Form\Util\Charset,
    Bitnix\Form\Input\Reporter;

/**
 * @version 0.1.0
 */
abstract class AbstractForm implements Form {

    const GET  = 'get';
    const POST = 'post';

    private const METHODS = [
        self::GET  => true,
        self::POST => true
    ];

    const URLENCODED = 'application/x-www-form-urlencoded';
    const MULTIPART  = 'multipart/form-data';
    const PLAIN      = 'text/plain';

    private const ENCTYPES = [
        self::URLENCODED => true,
        self::MULTIPART  => true,
        self::PLAIN      => true
    ];

    /**
     * @var Attributes
     */
    private Attributes $attrs;

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @var array
     */
    private array $widgets = [];

    /**
     * @param string $id
     * @param array $attrs
     * @param Control ...$widgets
     * @param InvalidArgumentException
     */
    public function __construct(string $id, array $attrs, Control ...$widgets) {

        if (!\preg_match('~^[a-zA-Z][a-zA-Z0-9_:\-\.]*$~', $id)) {
            throw new InvalidArgumentException(\sprintf(
                'Unsupported form id: "%s"', $id
            ));
        }

        $accept = $charset = $attrs['accept-charset'] ?? 'utf-8';
        if (false !== \strpos($accept, ' ')) {
            $parts = \explode(' ', $accept);
            \array_walk($parts, fn($item) => Charset::filter($item));
            $charset = $parts[0];
        }

        $enctype = $this->value(
            'enctype', self::URLENCODED, self::ENCTYPES, $attrs
        );

        $method = $this->value(
            'method', self::GET, self::METHODS, $attrs
        );

        $this->attrs = new Attributes([
            'id'             => $id,
            'accept-charset' => $accept,
            'action'         => $attrs['action'] ?? false,
            'enctype'        => $enctype,
            'method'         => $method
        ], $attrs, $charset);

        foreach ($widgets as $widget) {
            $this->widgets[$widget->name()] = $widget;
        }
    }

    /**
     * @return Reporter
     */
    protected abstract function reporter() : Reporter;

    /**
     * @param string $key
     * @param string $default
     * @param array $default
     * @param array $attrs
     * @throws InvalidArgumentException
     */
    private function value(string $key, string $default, array $valid, array $attrs) : string {
        if (!isset($attrs[$key])) {
            return $default;
        }

        $value = \strtolower($attrs[$key]);
        if (!isset($valid[$value])) {
            throw new InvalidArgumentException(\sprintf(
                'Unsupported value for %s: %s', $key, $value
            ));
        }

        return $value;
    }

    /**
     * @param string $error
     */
    public function error(string $error) : void {
        if (!\in_array($error, $this->errors)) {
            $this->errors[] = $error;
        }
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
     * @param array $attrs
     * @return string
     * @throws \InvalidArgumentException
     */
    public function open(array $attrs = []) : string {
        return \sprintf(
            '<form %s>%s',
                $this->attrs->render($attrs),
                $this->header()
        );
    }

    /**
     * @return string
     */
    protected function header() : string {
        return '';
    }

    /**
     * @param string $name
     * @return Widget
     * @throws \LogicException
     */
    public function widget(string $name) : Widget {
        if (isset($this->widgets[$name])) {
            return $this->widgets[$name];
        }

        throw new LogicException(\sprintf(
            'Form %s knows nothing about widget %s',
                $this->attrs->get('id'),
                $name
        ));
    }

    /**
     * @return string
     */
    public function close() : string {
        return $this->footer() . '</form>';
    }

    /**
     * @return string
     */
    protected function footer() : string {
        return '';
    }

    /**
     * @param array $input
     * @return mixed
     * @throws \Bitnix\Form\SecurityException
     */
    public function process(array $input) {

        $this->accept($input);

        $result = [];
        $reporter = $this->reporter();

        try {
            foreach ($this->widgets as $name => $widget) {
                $result[$name] = $widget->process($reporter, $input);
            }
        } catch (UnexpectedValueException $x) {
            throw new SecurityException('Unexpected value detected...', 0, $x);
        }

        $this->errors = $reporter->errors();
        if (!$this->errors) {
            return $this->filter($result);
        }

        return null;
    }

    /**
     * @param array $input
     * @throws \Bitnix\Form\SecurityException
     */
    protected function accept(array $input) : void {
        // noop...
    }

    /**
     * @param array $result
     * @return mixed
     * @throws \Throwable
     */
    protected function filter(array $result) {
        return $result;
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return \sprintf(
            '%s (id=%s)',
                static::CLASS,
                $this->attrs->get('id')
        );
    }
}
