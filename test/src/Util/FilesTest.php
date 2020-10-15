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

namespace Bitnix\Form\Util;

use PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class FilesTest extends TestCase {

    public function testInputTypeError() {
        $this->expectException(\RuntimeException::CLASS);
        Files::fix(['files' => $this]);
    }

    public function testMalformedInputError() {
        $this->expectException(\RuntimeException::CLASS);
        Files::fix(['files' => [
            'name' => '',
            'type' => ''
        ]]);
    }

    public function testDefaultInput() {
        try {
            $files = $_FILES;
            $data = [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => 4,
                'size'     => 0
            ];
            $file = (object) $data;
            $_FILES = [
                'files' => $data
            ];
            $this->assertSame(['files' => $file], Files::fix(null, fn($input) => $file));
        } finally {
            $_FILES = $files;
        }
    }

    /**
     * @dataProvider files
     */
    public function testExpectedValue(array $in, array $out, callable $converter = null) {
        $this->assertSame($out, Files::fix($in, $converter));
    }

    public function files() : array {
        return [

            // empty
            [
                [],
                []
            ],

            // no uploadd
            [
                [
                    'files' => [
                        'name'     => '',
                        'type'     => '',
                        'tmp_name' => '',
                        'error'    => 4,
                        'size'     => 0
                    ]
                ],
                []
            ],

            // no upload + custom converter
            [
                [
                    'files' => [
                        'name'     => '',
                        'type'     => '',
                        'tmp_name' => '',
                        'error'    => 4,
                        'size'     => 0
                    ]
                ],
                [
                    'files' => [
                        'name'     => '',
                        'type'     => '',
                        'tmp_name' => '',
                        'error'    => 4,
                        'size'     => 0
                    ]
                ],
                fn($file) => $file
            ],

            // one field upload
            [
                [
                    'files' => [
                        'name'     => 'file.txt',
                        'type'     => 'text/plain',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 10
                    ]
                ],
                [
                    'files' => [
                        'name'     => 'file.txt',
                        'type'     => 'text/plain',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 10
                    ]
                ]
            ],

            // multiple fields upload
            [
                [
                    'images' => [
                        'name'     => 'image.png',
                        'type'     => 'image.png',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 100
                    ],
                    'files' => [
                        'name'     => 'file.txt',
                        'type'     => 'text/plain',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 10
                    ]
                ],
                [
                    'images' => [
                        'name'     => 'image.png',
                        'type'     => 'image.png',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 100
                    ],
                    'files' => [
                        'name'     => 'file.txt',
                        'type'     => 'text/plain',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 10
                    ]
                ]
            ],

            // multiple fields (one simple, one list) upload
            [
                [
                    'images' => [
                        'name'     => 'image.png',
                        'type'     => 'image.png',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 100
                    ],
                    'files' => [
                        'name'     => ['file.txt'],
                        'type'     => ['text/plain'],
                        'tmp_name' => ['/tmp/phpiBOoCn'],
                        'error'    => [0],
                        'size'     => [10]
                    ]
                ],
                [
                    'images' => [
                        'name'     => 'image.png',
                        'type'     => 'image.png',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 100
                    ],
                    'files' => [
                        [
                            'error'    => 0,
                            'name'     => 'file.txt',
                            'type'     => 'text/plain',
                            'tmp_name' => '/tmp/phpiBOoCn',
                            'size'     => 10
                        ]
                    ]
                ]
            ],

            // multiple fields (one simple, one empty list) upload
            [
                [
                    'images' => [
                        'name'     => 'image.png',
                        'type'     => 'image.png',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 100
                    ],
                    'files' => [
                        'name'     => [''],
                        'type'     => [''],
                        'tmp_name' => [''],
                        'error'    => [4],
                        'size'     => [0]
                    ]
                ],
                [
                    'images' => [
                        'name'     => 'image.png',
                        'type'     => 'image.png',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 100
                    ],
                    'files' => []
                ]
            ],

            // multiple fields (one simple, one list) upload
            [
                [
                    'images' => [
                        'name'     => 'image.png',
                        'type'     => 'image.png',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 100
                    ],
                    'files' => [
                        'name'     => ['file.txt', 'other.txt'],
                        'type'     => ['text/plain', 'text/plain'],
                        'tmp_name' => ['/tmp/phpiBOoCn', '/tmp/php7895B'],
                        'error'    => [0, 0],
                        'size'     => [10, 10]
                    ]
                ],
                [
                    'images' => [
                        'name'     => 'image.png',
                        'type'     => 'image.png',
                        'tmp_name' => '/tmp/phpiBOoCn',
                        'error'    => 0,
                        'size'     => 100
                    ],
                    'files' => [
                        [
                            'error'    => 0,
                            'name'     => 'file.txt',
                            'type'     => 'text/plain',
                            'tmp_name' => '/tmp/phpiBOoCn',
                            'size'     => 10
                        ],
                        [
                            'error'    => 0,
                            'name'     => 'other.txt',
                            'type'     => 'text/plain',
                            'tmp_name' => '/tmp/php7895B',
                            'size'     => 10
                        ]
                    ]
                ]
            ],

            // nested empty fields
            [
                [
                    'files' => [
                        'name' => [
                            'user' => [
                                'uploads' => [
                                    ''
                                ]
                            ]
                        ],
                        'type' => [
                            'user' => [
                                'uploads' => [
                                    ''
                                ]
                            ]
                        ],
                        'tmp_name' => [
                            'user' => [
                                'uploads' => [
                                    ''
                                ]
                            ]
                        ],
                        'error' => [
                            'user' => [
                                'uploads' => [
                                    4
                                ]
                            ]
                        ],
                        'size' => [
                            'user' => [
                                'uploads' => [
                                    0
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'files' => [
                        'user' => [
                            'uploads' => []
                        ]
                    ]
                ]
            ],

            // nested submitted fields
            [
                [
                    'files' => [
                        'name' => [
                            'user' => [
                                'uploads' => [
                                    'foo'
                                ]
                            ]
                        ],
                        'type' => [
                            'user' => [
                                'uploads' => [
                                    'text/plain'
                                ]
                            ]
                        ],
                        'tmp_name' => [
                            'user' => [
                                'uploads' => [
                                    '/tmp/file'
                                ]
                            ]
                        ],
                        'error' => [
                            'user' => [
                                'uploads' => [
                                    0
                                ]
                            ]
                        ],
                        'size' => [
                            'user' => [
                                'uploads' => [
                                    10
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'files' => [
                        'user' => [
                            'uploads' => [
                                [
                                    'error'    => 0,
                                    'name'     => 'foo',
                                    'type'     => 'text/plain',
                                    'tmp_name' => '/tmp/file',
                                    'size'     => 10
                                ]
                            ]
                        ]
                    ]
                ]
            ],

            // nested submitted fields
            [
                [
                    'files' => [
                        'name' => [
                            'user' => [
                                'uploads' => [
                                    'foo',
                                    'bar'
                                ]
                            ]
                        ],
                        'type' => [
                            'user' => [
                                'uploads' => [
                                    'text/plain',
                                    'text/plain'
                                ]
                            ]
                        ],
                        'tmp_name' => [
                            'user' => [
                                'uploads' => [
                                    '/tmp/file',
                                    '/tmp/other'
                                ]
                            ]
                        ],
                        'error' => [
                            'user' => [
                                'uploads' => [
                                    0,
                                    0
                                ]
                            ]
                        ],
                        'size' => [
                            'user' => [
                                'uploads' => [
                                    10,
                                    20
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'files' => [
                        'user' => [
                            'uploads' => [
                                [
                                    'error'    => 0,
                                    'name'     => 'foo',
                                    'type'     => 'text/plain',
                                    'tmp_name' => '/tmp/file',
                                    'size'     => 10
                                ],
                                [
                                    'error'    => 0,
                                    'name'     => 'bar',
                                    'type'     => 'text/plain',
                                    'tmp_name' => '/tmp/other',
                                    'size'     => 20
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

}
