<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\TypeString;

use PHPCSUtils\Utils\TypeString;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\TypeString::getKeywordTypes() method.
 *
 * @covers \PHPCSUtils\Utils\TypeString::getKeywordTypes
 *
 * @since 1.1.0
 */
final class GetKeywordTypesTest extends TestCase
{

    /**
     * Perfunctory test for the getKeywordTypes() method.
     *
     * @return void
     */
    public function testGetKeywordTypes()
    {
        $list = TypeString::getKeywordTypes();

        $this->assertIsArray($list, 'Return value was not an array');
        $this->assertCount(17, $list, 'Returned array did not contain the expected number of items');
    }
}
