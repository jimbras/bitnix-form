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
    Bitnix\Form\Sanitizer;

/**
 * @version 0.1.0
 */
final class Select extends AbstractControl {

    /**
     * @var bool
     */
    private bool $multiple;

    /**
     * @var string
     */
    private string $charset;

    /**
     * @var Options
     */
    private Options $options;

    /**
     * @param string $name
     * @param null|Options $options
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, Options $options = null, array $config = []) {
        parent::__construct(
            $name,
            [
                'multiple' => $multiple = 0 === \substr_compare($name, '[]', -2, 2)
            ],
            $config
        );

        $this->options = $options ?: new Optlist();
        if (!$multiple && ($count = \count($this->options->selected())) > 1) {
            throw new InvalidArgumentException(\sprintf(
                'Too many selected options for select %s, got %s, but only one is allowed',
                    $this->name(),
                    $count
            ));
        }

        $this->multiple = $multiple;
        $this->charset = $this->attributes()->charset();
    }

    /**
     * @return mixed
     */
    public function selected() {
        $selected = $this->options->selected();
        return $this->multiple ? $selected : ($selected[0] ?? null);
    }

    /**
     * @param array $input
     * @return mixed
     */
    protected function multiple(array $input) {
        return $input;
    }

    /**
     * @param Sanitizer $sanitizer
     * @param mixed $value
     * @return mixed
     */
    protected function update(Sanitizer $sanitizer, $value) {
        $selected = $this->options->select(...((array) $value));
        return $this->multiple ? $selected : ($selected[0] ?? null);
    }

    /**
     * @param array $attrs
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render(array $attrs = []) : string {
        return \sprintf(
            '<select %s>%s</select>',
                $this->attributes()->render($attrs),
                $this->options->render($this->charset)
        );
    }
}
