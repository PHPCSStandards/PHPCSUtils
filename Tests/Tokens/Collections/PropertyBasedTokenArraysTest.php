<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Tokens\Collections;

use PHPCSUtils\Tokens\Collections;
use ReflectionProperty;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Tokens\Collections::__callStatic
 *
 * @since 1.0.0
 */
final class PropertyBasedTokenArraysTest extends TestCase
{

    /**
     * Test that undeclared methods which are intended to just return the property, do so correctly.
     *
     * @dataProvider dataPropertyBasedTokenArrays
     *
     * @param string $name The token array name.
     *
     * @return void
     */
    public function testPropertyBasedTokenArrays($name)
    {
        $reflProp = new ReflectionProperty('PHPCSUtils\Tokens\Collections', $name);
        (\PHP_VERSION_ID < 80100) && $reflProp->setAccessible(true);
        $expected = $reflProp->getValue();
        (\PHP_VERSION_ID < 80100) && $reflProp->setAccessible(false);

        $this->assertSame($expected, Collections::$name());
    }

    /**
     * Data provider.
     *
     * @see testPropertyBasedTokenArrays() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataPropertyBasedTokenArrays()
    {
        $names = [
            'alternativeControlStructureSyntaxes',
            'alternativeControlStructureSyntaxClosers',
            'arrayTokens',
            'classModifierKeywords',
            'closedScopes',
            'constantModifierKeywords',
            'controlStructureTokens',
            'functionDeclarationTokens',
            'incrementDecrementOperators',
            'listTokens',
            'namespaceDeclarationClosers',
            'nameTokens',
            'objectOperators',
            'ooCanExtend',
            'ooCanImplement',
            'ooConstantScopes',
            'ooHierarchyKeywords',
            'ooPropertyScopes',
            'phpOpenTags',
            'propertyModifierKeywords',
            'shortArrayTokens',
            'shortListTokens',
            'ternaryOperators',
            'textStringStartTokens',
        ];

        $data = [];
        foreach ($names as $name) {
            $data[$name] = [$name];
        }

        return $data;
    }

    /**
     * Test calling a token property method for a token array which doesn't exist.
     *
     * @return void
     */
    public function testUndeclaredTokenArray()
    {
        $this->expectException('PHPCSUtils\Exceptions\InvalidTokenArray');
        $this->expectExceptionMessage('Call to undefined method PHPCSUtils\Tokens\Collections::notATokenArray()');

        Collections::notATokenArray();
    }
}
