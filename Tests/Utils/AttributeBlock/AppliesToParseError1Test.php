<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2025 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\AttributeBlock;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\AttributeBlock;

/**
 * Test for the \PHPCSUtils\Utils\AttributeBlock::appliesTo() method.
 *
 * @covers \PHPCSUtils\Utils\AttributeBlock::appliesTo
 *
 * @group attributes
 *
 * @since 1.2.0
 */
final class AppliesToParseError1Test extends PolyfilledTestCase
{

    /**
     * Verify that a broken attribute results in a `false` return when passed the attribute opener.
     *
     * @return void
     */
    public function testUnfinishedAttributeWithOpener()
    {
        $targetPtr = $this->getTargetToken('/* testLiveCoding */', \T_ATTRIBUTE);
        $result    = AttributeBlock::appliesTo(self::$phpcsFile, $targetPtr);

        $this->assertFalse($result);
    }

    /**
     * Verify that if a token from within a broken attribute is passed, an exception is thrown.
     *
     * @dataProvider dataUnfinishedAttributeWithTokenWithin
     *
     * @param int|string  $tokenType    Type of token to select as the target.
     * @param string|null $tokenContent Optional. Token content of the target token.
     *
     * @return void
     */
    public function testUnfinishedAttributeWithTokenWithin($tokenType, $tokenContent = null)
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type T_ATTRIBUTE, T_ATTRIBUTE_END or a token within an attribute'
        );

        $targetPtr = $this->getTargetToken('/* testLiveCoding */', $tokenType, $tokenContent);
        AttributeBlock::appliesTo(self::$phpcsFile, $targetPtr);
    }

    /**
     * Data provider.
     *
     * @see testUnfinishedAttributeWithTokenWithin()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataUnfinishedAttributeWithTokenWithin()
    {
        return [
            'attribute name' => [
                'tokenType'    => \T_STRING,
                'tokenContent' => 'AttributeName',
            ],
            'parenthesis' => [
                'tokenType' => \T_OPEN_PARENTHESIS,
            ],
            'attribute parameter' => [
                'tokenType' => \T_LNUMBER,
            ],
        ];
    }
}
