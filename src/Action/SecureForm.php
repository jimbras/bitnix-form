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

use Bitnix\Form\Token;

/**
 * @version 0.1.0
 */
abstract class SecureForm extends AbstractForm {

    /**
     * @var Token
     */
    private Token $token;

    /**
     * @param Token $token
     * @param string $id
     * @param array $attrs
     * @param Control ...$widgets
     * @param InvalidArgumentException
     */
    public function __construct(Token $token, string $id, array $attrs, Control ...$widgets) {
        parent::__construct($id, $attrs, ...$widgets);
        $this->token = $token;
    }

    /**
     * @return Token
     */
    protected function token() : Token {
        return $this->token;
    }

    /**
     * @param array $input
     * @throws \Bitnix\Form\SecurityException
     */
    protected function accept(array $input) : void {
        $this->token->validate($input);
    }

    /**
     * @return string
     */
    protected function footer() : string {
        return $this->token->render();
    }
}
