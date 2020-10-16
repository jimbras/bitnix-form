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

namespace Bitnix\Form\Input;

/**
 * @version 0.1.0
 */
final class ErrorReporter {

    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @var array
     */
    private array $validators = [];

    /**
     * @param string $name
     * @param Filter $filter
     * @return self
     */
    public function filter(string $name, Filter $filter) : self {
        $this->filters[$name] = $filter;
        return $this;
    }

    /**
     * @param string $name
     * @param Validator $validator
     * @return self
     */
    public function validator(string $name, Validator $validator) : self {
        $this->validators[$name] = $validator;
        return $this;
    }

    /**
     * @return Reporter
     */
    public function build() : Reporter {
        $object = new class($this->filters, $this->validators) implements Reporter {

            private bool $dirty;
            private array $filters;
            private array $validators;
            private array $errors = [];

            public function __construct(array $filters, array $validators) {
                $this->filters = $filters;
                $this->validators = $validators;
            }

            public function errors() : array {
                if ($this->dirty) {
                    $this->dirty = false;
                    $this->errors = \array_keys(\array_flip($this->errors));
                }
                return $this->errors;
            }

            public function filter(string $name, $input) {
                if (isset($this->filters[$name])) {
                    $input = $this->filters[$name]->filter($input);
                }
                return $input;
            }

            public function validate(string $name, $input) : array {
                if (isset($this->validators[$name])) {
                    $errors = $this->validators[$name]->validate($input);
                    if ($errors) {
                        $this->dirty = true;
                        $this->errors = \array_merge($this->errors, $errors);
                        return $errors;
                    }
                }
                return [];
            }

            public function __toString() : string {
                return self::CLASS;
            }
        };

        $this->filters = $this->validators = [];
        return $object;
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
