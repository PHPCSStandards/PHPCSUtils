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
 * @group collections
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
        /*
         * While most properties are still `public`, some aren't, so we may as well set
         * the test up to use reflection from the start, as the intention is to make
         * all relevant properties `private`.
         */
        $reflProp = new ReflectionProperty('PHPCSUtils\Tokens\Collections', $name);
        $reflProp->setAccessible(true);
        $expected = $reflProp->getValue();
        $reflProp->setAccessible(false);

        $this->assertSame($expected, Collections::$name());
    }

    /**
     * Data provider.
     *
     * @see testPropertyBasedTokenArrays() For the array format.
     *
     * @return array
     */
    public function dataPropertyBasedTokenArrays()
    {
        $names = [
            'arrayOpenTokensBC',
            'arrayTokens',
            'arrayTokensBC',
            'classModifierKeywords',
            'closedScopes',
            'constantModifierKeywords',
            'controlStructureTokens',
            'functionDeclarationTokens',
            'incrementDecrementOperators',
            'listTokens',
            'listTokensBC',
            'namespaceDeclarationClosers',
            'nameTokens',
            'objectOperators',
            'phpOpenTags',
            'propertyModifierKeywords',
            'shortArrayListOpenTokensBC',
            'shortArrayTokens',
            'shortArrayTokensBC',
            'shortListTokens',
            'shortListTokensBC',
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
