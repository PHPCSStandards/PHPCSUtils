<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getDeclarationName() method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getDeclarationName
 *
 * @group objectdeclarations
 *
 * @since 1.1.0
 */
class GetDeclarationNameParseError2Test extends PolyfilledTestCase
{

    /**
     * Test receiving an empty string in case of a parse error.
     *
     * @dataProvider dataGetDeclarationName
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = BCFile::getDeclarationName(self::$phpcsFile, $target);
        $this->assertSame('', $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclarationName() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataGetDeclarationName()
    {
        return [
            'unfinished closure/live coding' => [
                'testMarker' => '/* testLiveCoding */',
                'targetType' => \T_FUNCTION,
            ],
        ];
    }
}
