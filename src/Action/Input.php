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

use Bitnix\Form\Sanitizer;

/**
 * @version 0.1.0
 */
abstract class Input extends AbstractControl {

    /**
     * @param string $type
     * @param string $name
     * @param array $config
     * @param array $defaults
     * @throws \InvalidArgumentException
     */
    public function __construct(string $type, string $name, array $config, array $defaults = []) {
        parent::__construct(
            $name,
            ['type' => $type, 'value' => $config['value'] ?? null] + $defaults,
            $config
        );
    }

    /**
     * @return string
     */
    public function type() : string {
        return $this->attributes()->get('type');
    }

    /**
     * @return null|string
     */
    public function value() : ?string {
        return $this->attributes()->get('value');
    }

    /**
     * @param Sanitizer $sanitizer
     * @param mixed $value
     * @return mixed
     */
    protected function update(Sanitizer $sanitizer, $value) {
        $this->attributes()->set('value', $value);
        return $value;
    }

    /**
     * @param array $attrs
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render(array $attrs = []) : string {
        return \sprintf(
            '<input %s>', $this->attributes()->render($attrs)
        );
    }
}
