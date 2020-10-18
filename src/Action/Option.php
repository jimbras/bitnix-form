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

/**
 * @version 0.1.0
 */
final class Option {

    /**
     * @var string
     */
    private string $label;

    /**
     * @var string
     */
    private ?string $value;

    /**
     * @var bool
     */
    private bool $selected;

    /**
     * @param string $label
     * @param null|string $value
     * @param bool $selected
     */
    public function __construct(string $label, string $value = null, bool $selected = false) {
        $this->label = \preg_replace('~[\t\r\n\0\x0B]~', '', \strip_tags($label));
        $this->value = $value === null ? $value : \preg_replace('~[\t\r\n\0\x0B]~', '', $value);
        $this->selected = $selected;
    }

    /**
     * @return string
     */
    public function label() : string {
        return $this->label;
    }

    /**
     * @return string
     */
    public function value() : string {
        return $this->value ?? $this->label;
    }

    /**
     * @param bool $flag
     * @return bool
     */
    public function select(bool $flag) : bool {
        $this->selected = $flag;
        return $flag;
    }

    /**
     * @return bool
     */
    public function selected() : bool {
        return $this->selected;
    }

    /**
     * @param string $charset
     * @return string
     */
    public function render(string $charset = 'UTF-8') : string {
        $value = $this->value === null
            ? ''
            : ' value="'
                . \htmlspecialchars($this->value, \ENT_QUOTES, $charset, false)
                . '"';
        $selected = $this->selected ? ' selected' : '';

        return \sprintf(
            '<option%s%s>%s</option>',
                $value,
                $selected,
                \htmlspecialchars($this->label, \ENT_QUOTES, $charset, false)
        );
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }

}
