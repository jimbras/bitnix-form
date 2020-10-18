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
final class Textarea extends AbstractControl {

    /**
     * @var string
     */
    private ?string $content = null;

    /**
     * @var string
     */
    private string $charset;

    /**
     * @param string $name
     * @param null|string $content
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, string $content = null, array $config = []) {
        parent::__construct($name, [], $config);
        $this->content = $content;
        $this->charset = $this->attributes()->charset();
    }

    /**
     * @return null|string
     */
    public function content() : ?string {
        return $this->content;
    }

    /**
     * @param Sanitizer $sanitizer
     * @param mixed $value
     * @return mixed
     */
    protected function update(Sanitizer $sanitizer, $value) {
        $this->content = $value;
        return $value;
    }

    /**
     * @param array $attrs
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render(array $attrs = []) : string {
        return \sprintf(
            '<textarea %s>%s</textarea>',
                $this->attributes()->render($attrs),
                \htmlspecialchars((string) $this->content, \ENT_QUOTES, $this->charset, false)
        );
    }

}
