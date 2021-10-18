<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 * @author      Wojtek Wnuk <wojtek@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Test\Unit;

use PHPUnit\Framework\TestCase;
use Nanobots\DataPatchCreator\Converters\ObjectToString;

class ArrayToStringTest extends TestCase
{

    /** @var ObjectToString */
    protected $objectToStringConverter;

    /**  */
    public function setUp(): void
    {
        $this->objectToStringConverter = new ObjectToString();
    }

    /**
     *
     */
    public function testArrayNotationToString()
    {
        $array = ['10', '20', '30', '40'];
        $complexArray = [
            'test' => [
                'some-value' => false,
                'another-value' => null,
            ]
        ];

        $updatedArrayNotation = <<<EOL
[
    0 => '10',
    1 => '20',
    2 => '30',
    3 => '40',
]
EOL;

        $expectedComplexArray = <<<EOL
[
    'test' => [
        'some-value' => false,
        'another-value' => NULL,
    ],
]
EOL;

        $this->assertSame($updatedArrayNotation, $this->objectToStringConverter->convertDataArrayToString($array));
        $this->assertSame($updatedArrayNotation, $this->objectToStringConverter->convertDataArrayToString($array));
        $this->assertSame(
            $expectedComplexArray,
            $this->objectToStringConverter->convertDataArrayToString($complexArray)
        );
    }
}
