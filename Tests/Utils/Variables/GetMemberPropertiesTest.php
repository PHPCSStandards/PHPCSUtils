<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Variables;

use PHPCSUtils\Tests\BackCompat\BCFile\GetMemberPropertiesTest as BCFile_GetMemberPropertiesTest;

/**
 * Tests for the \PHPCSUtils\Utils\Variables::getMemberProperties method.
 *
 * @covers \PHPCSUtils\Utils\Variables::getMemberProperties
 *
 * @group variables
 *
 * @since 1.0.0
 */
class GetMemberPropertiesTest extends BCFile_GetMemberPropertiesTest
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\Utils\Variables';

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * Overloaded to re-use the `$caseFile` from the BCFile test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetMemberPropertiesTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Data provider.
     *
     * @see testGetMemberProperties()
     *
     * @return array
     */
    public function dataGetMemberProperties()
    {
        $data = parent::dataGetMemberProperties();

        /*
         * Remove the data set related to the invalid interface/enum properties.
         * These will now throw an exception instead.
         */
        foreach ($data as $key => $value) {
            if ($value[0] === '/* testInterfaceProperty */' || $value[0] === '/* testEnumProperty */') {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
