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
final class Button extends AbstractControl {

    /**
     * @var string
     */
    private ?string $content = null;

    /**
     * @param string $name
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, array $config = []) {
        if (isset($config['content'])) {
            $this->content = $config['content'];
            unset($config['content']);
        }
        parent::__construct(
            $name,
            [
                'type'  => $config['type'] ?? 'submit',
                'value' => $config['value'] ?? $name
            ],
            $config
        );
    }

    /**
     * @return string
     */
    public function value() : string {
        return $this->attributes()->get('value');
    }

    /**
     * @return null|string
     */
    public function content() : ?string {
        return $this->content;
    }

    /**
     * @param mixed $value
     */
    protected function update($value) : void {
        $this->attributes()->set('value', $value);
    }

    /**
     * @param array $attrs
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render(array $attrs = []) : string {
        return \sprintf(
            '<button %s>%s</button>',
                $this->attributes()->render($attrs),
                $this->content
        );
    }

}
