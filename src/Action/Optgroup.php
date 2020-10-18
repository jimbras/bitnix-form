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

use Bitnix\Form\Util\Charset;

/**
 * @version 0.1.0
 */
final class Optgroup implements Options {

    /**
     * @var string
     */
    private string $charset;

    /**
     * @var array
     */
    private array $options = [];

    /**
     * @var array
     */
    private array $selected = [];

    /**
     * @param array $group
     * @param null|string $charset
     * @throws \InvalidArgumentException
     */
    public function __construct(array $group = [], string $charset = null) {
        $this->charset = Charset::filter($charset);
        foreach ($group as $label => $options) {
            $list = [];
            $label = \preg_replace('~[\t\r\n\0\x0B]~', '', \strip_tags($label));
            foreach ($options as $option) {
                $value = $option->value();
                $list[$value] = $option;
                if ($option->selected()) {
                    $this->selected[] = $value;
                }
            }
            $this->options[$label] = $list;
        }
    }

    /**
     * @return array
     */
    public function selected() : array {
        return $this->selected;
    }

    /**
     * @param string ...$options
     * @return array
     */
    public function select(string ...$options) : array {
        $this->selected = [];
        $data = \array_flip($options);
        foreach ($this->options as $options) {
            foreach ($options as $value => $option) {
                if ($option->select(isset($data[$value]))) {
                    $this->selected[] = $value;
                }
            }
        }
        return $this->selected;
    }

    /**
     * @return string
     */
    public function render() : string {
        $buffer = '';
        foreach ($this->options as $label => $options) {
            $group = '';
            foreach ($options as $option) {
                $group .= $option->render($this->charset);
            }
            $buffer .= \sprintf(
                '<optgroup label="%s">%s</optgroup>',
                    \htmlspecialchars($label, \ENT_QUOTES, $this->charset, false),
                    $group
            );
        }
        return $buffer;
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
