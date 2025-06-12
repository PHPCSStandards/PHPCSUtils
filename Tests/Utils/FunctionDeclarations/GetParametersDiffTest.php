<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FunctionDeclarations;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getParameters method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `getParameters()` method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getParameters
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class GetParametersDiffTest extends PolyfilledTestCase
{

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/DummyFile.inc';
        parent::setUpTestFile();
    }

    /**
     * Test passing a non-integer token pointer.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        FunctionDeclarations::getParameters(self::$phpcsFile, false);
    }

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object'
        );

        FunctionDeclarations::getParameters(self::$phpcsFile, 10000);
    }
}
