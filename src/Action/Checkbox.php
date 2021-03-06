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
final class Checkbox extends Input {

    /**
     * @param string $name
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name, array $config = []) {
        parent::__construct('checkbox', $name, $config, $this->defaults($config));
    }

    /**
     * @param array $config
     * @return array
     */
    private function defaults(array $config) : array {

        if ($config['indeterminate'] ?? false) {
            return [
                'checked'       => false,
                'indeterminate' => true
            ];
        }

        return [
            'checked'       => (bool) ($config['checked'] ?? false),
            'indeterminate' => false
        ];
    }

    /**
     * @param Sanitizer $sanitizer
     * @param mixed $value
     * @return mixed
     */
    protected function update(Sanitizer $sanitizer, $value) {
        $attrs = $this->attributes();
        $attrs->set('indeterminate', false);
        $attrs->set('checked', $value === $attrs->get('value'));
        return $value;
    }
}
