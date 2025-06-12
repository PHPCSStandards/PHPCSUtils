<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\AbstractSniffs\AbstractArrayDeclaration;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Tokens\Collections;

/**
 * Tests for the \PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff class.
 *
 * @covers \PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff
 *
 * @since 1.0.0
 */
final class AbstractArrayDeclarationSniffTest extends PolyfilledTestCase
{

    /**
     * List of methods in the abstract which should be mocked.
     *
     * Needed for PHPUnit cross-version support as PHPUnit 4.x does not have a
     * `setMethodsExcept()` method yet.
     *
     * @var array<string>
     */
    public $methodsToMock = [
        'processOpenClose',
        'processKey',
        'processNoKey',
        'processArrow',
        'processValue',
        'processComma',
    ];

    /**
     * Test receiving an expected exception when an non-integer token pointer is passed.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->never())
            ->method('processOpenClose');

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->never())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->never())
            ->method('processValue');

        $mockObj->expects($this->never())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, false);
    }

    /**
     * Test receiving an expected exception when an invalid token pointer is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->never())
            ->method('processOpenClose');

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->never())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->never())
            ->method('processValue');

        $mockObj->expects($this->never())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, 100000);
    }

    /**
     * Test that the abstract sniff correctly bows out when presented with a token which is not an array.
     *
     * @return void
     */
    public function testShortList()
    {
        $target = $this->getTargetToken('/* testShortList */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->never())
            ->method('processOpenClose');

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->never())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->never())
            ->method('processValue');

        $mockObj->expects($this->never())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test that the abstract sniff correctly bows out after the processOpenClose() method
     * when presented with an empty array.
     *
     * @return void
     */
    public function testEmptyArray()
    {
        $target = $this->getTargetToken('/* testEmptyArray */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose');

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->never())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->never())
            ->method('processValue');

        $mockObj->expects($this->never())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test all features of the abstract sniff when presented with a single line short array
     * without array keys and without trailing comma after the last array item.
     *
     * @return void
     */
    public function testSingleLineShortArrayNoKeysNoTrailingComma()
    {
        $target = $this->getTargetToken(
            '/* testSingleLineShortArrayNoKeysNoTrailingComma */',
            Collections::arrayOpenTokensBC()
        );

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->identicalTo($target),
                $this->identicalTo($target + 5)
            );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(2),
            'processNoKey',
            [
                [self::$phpcsFile, $target + 1, 1],
                [self::$phpcsFile, $target + 3, 2],
            ]
        );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(2),
            'processValue',
            [
                [self::$phpcsFile, $target + 1, $target + 1, 1],
                [self::$phpcsFile, $target + 3, $target + 4, 2],
            ]
        );

        $mockObj->expects($this->once())
            ->method('processComma')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->identicalTo($target + 2),
                $this->identicalTo(1)
            );

        $mockObj->process(self::$phpcsFile, $target);

        // Verify that the properties have been correctly set.
        $this->assertPropertySame($target, 'stackPtr', $mockObj);
        $this->assertPropertySame($target, 'arrayOpener', $mockObj);
        $this->assertPropertySame(($target + 5), 'arrayCloser', $mockObj);
        $this->assertPropertySame(2, 'itemCount', $mockObj);
        $this->assertPropertySame(true, 'singleLine', $mockObj);
    }

    /**
     * Test all features of the abstract sniff when presented with a mutli line long array
     * with array keys, double arrows and with a trailing comma after the last array item.
     *
     * @return void
     */
    public function testMultiLineLongArrayKeysTrailingComma()
    {
        $target = $this->getTargetToken(
            '/* testMultiLineLongArrayKeysTrailingComma */',
            Collections::arrayOpenTokensBC()
        );

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->identicalTo($target + 1),
                $this->identicalTo($target + 35)
            );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(3),
            'processKey',
            [
                [self::$phpcsFile, $target + 2, $target + 5, 1],
                [self::$phpcsFile, $target + 10, $target + 13, 2],
                [self::$phpcsFile, $target + 18, $target + 21, 3],
            ]
        );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(3),
            'processArrow',
            [
                [self::$phpcsFile, $target + 6, 1],
                [self::$phpcsFile, $target + 14, 2],
                [self::$phpcsFile, $target + 22, 3],
            ]
        );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(3),
            'processValue',
            [
                [self::$phpcsFile, $target + 7, $target + 8, 1],
                [self::$phpcsFile, $target + 15, $target + 16, 2],
                [self::$phpcsFile, $target + 23, $target + 24, 3],
            ],
            [null, null, true] // Testing short-circuiting the loop.
        );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(2),
            'processComma',
            [
                [self::$phpcsFile, $target + 9, 1],
                [self::$phpcsFile, $target + 17, 2],
            ]
        );

        $mockObj->process(self::$phpcsFile, $target);

        // Verify that the properties have been correctly set.
        $this->assertPropertySame($target, 'stackPtr', $mockObj);
        $this->assertPropertySame(($target + 1), 'arrayOpener', $mockObj);
        $this->assertPropertySame(($target + 35), 'arrayCloser', $mockObj);
        $this->assertPropertySame(4, 'itemCount', $mockObj);
        $this->assertPropertySame(false, 'singleLine', $mockObj);
    }

    /**
     * Test all features of the abstract sniff when presented with a multi line short array with
     * a mix of items with and without array keys and with a trailing comma after the last array item.
     *
     * @return void
     */
    public function testMultiLineShortArrayMixedKeysNoKeys()
    {
        $target = $this->getTargetToken(
            '/* testMultiLineShortArrayMixedKeysNoKeys */',
            Collections::arrayOpenTokensBC()
        );

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->identicalTo($target),
                $this->identicalTo($target + 22)
            );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(2),
            'processKey',
            [
                [self::$phpcsFile, $target + 1, $target + 4, 1],
                [self::$phpcsFile, $target + 13, $target + 16, 3],
            ]
        );

        $mockObj->expects($this->once())
            ->method('processNoKey')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->identicalTo($target + 9),
                $this->identicalTo(2)
            );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(2),
            'processArrow',
            [
                [self::$phpcsFile, $target + 5, 1],
                [self::$phpcsFile, $target + 17, 3],
            ]
        );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(3),
            'processValue',
            [
                [self::$phpcsFile, $target + 6, $target + 7, 1],
                [self::$phpcsFile, $target + 9, $target + 11, 2],
                [self::$phpcsFile, $target + 18, $target + 19, 3],
            ]
        );

        $this->setExpectationWithConsecutiveArgs(
            $mockObj,
            $this->exactly(3),
            'processComma',
            [
                [self::$phpcsFile, $target + 8, 1],
                [self::$phpcsFile, $target + 12, 2],
                [self::$phpcsFile, $target + 20, 3],
            ]
        );

        $mockObj->process(self::$phpcsFile, $target);

        // Verify that the properties have been correctly set.
        $this->assertPropertySame($target, 'stackPtr', $mockObj);
        $this->assertPropertySame($target, 'arrayOpener', $mockObj);
        $this->assertPropertySame(($target + 22), 'arrayCloser', $mockObj);
        $this->assertPropertySame(3, 'itemCount', $mockObj);
        $this->assertPropertySame(false, 'singleLine', $mockObj);
    }

    /**
     * Test the abstract sniff correctly ignores empty array items (parse error).
     *
     * @return void
     */
    public function testEmptyArrayItem()
    {
        $target = $this->getTargetToken('/* testEmptyArrayItem */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose');

        $mockObj->expects($this->exactly(1))
            ->method('processKey');

        $mockObj->expects($this->exactly(1))
            ->method('processNoKey');

        $mockObj->expects($this->exactly(2))
            ->method('processValue');

        $mockObj->expects($this->once())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test short-circuiting the sniff on the call to processOpenClose().
     *
     * @return void
     */
    public function testShortCircuitOnProcessOpenClose()
    {
        $target = $this->getTargetToken('/* testShortCircuit */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose')
            ->willReturn(true);

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->never())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->never())
            ->method('processValue');

        $mockObj->expects($this->never())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test short-circuiting the sniff on the call to processKey().
     *
     * @return void
     */
    public function testShortCircuitOnProcessKey()
    {
        $target = $this->getTargetToken('/* testShortCircuit */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose');

        $mockObj->expects($this->once())
            ->method('processKey')
            ->willReturn(true);

        $mockObj->expects($this->once())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->once())
            ->method('processValue');

        $mockObj->expects($this->once())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test short-circuiting the sniff on the call to processNoKey().
     *
     * @return void
     */
    public function testShortCircuitOnProcessNoKey()
    {
        $target = $this->getTargetToken('/* testShortCircuit */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose');

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->once())
            ->method('processNoKey')
            ->willReturn(true);

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->never())
            ->method('processValue');

        $mockObj->expects($this->never())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test short-circuiting the sniff on the call to processArrow().
     *
     * @return void
     */
    public function testShortCircuitOnProcessArrow()
    {
        $target = $this->getTargetToken('/* testShortCircuit */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose');

        $mockObj->expects($this->once())
            ->method('processKey');

        $mockObj->expects($this->once())
            ->method('processNoKey');

        $mockObj->expects($this->once())
            ->method('processArrow')
            ->willReturn(true);

        $mockObj->expects($this->once())
            ->method('processValue');

        $mockObj->expects($this->once())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test short-circuiting the sniff on the call to processValue().
     *
     * @return void
     */
    public function testShortCircuitOnProcessValue()
    {
        $target = $this->getTargetToken('/* testShortCircuit */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose');

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->once())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->once())
            ->method('processValue')
            ->willReturn(true);

        $mockObj->expects($this->never())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test short-circuiting the sniff on the call to processComma().
     *
     * @return void
     */
    public function testShortCircuitOnProcessComma()
    {
        $target = $this->getTargetToken('/* testShortCircuit */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->once())
            ->method('processOpenClose');

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->once())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->once())
            ->method('processValue');

        $mockObj->expects($this->once())
            ->method('processComma')
            ->willReturn(true);

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Test that the abstract sniff correctly bows out when presented with an unfinished array.
     *
     * @return void
     */
    public function testBowOutOnUnfinishedArray()
    {
        $target = $this->getTargetToken('/* testLiveCoding */', Collections::arrayOpenTokensBC());

        $mockObj = $this->getMockedClassUnderTest();

        $mockObj->expects($this->never())
            ->method('processOpenClose');

        $mockObj->expects($this->never())
            ->method('processKey');

        $mockObj->expects($this->never())
            ->method('processNoKey');

        $mockObj->expects($this->never())
            ->method('processArrow');

        $mockObj->expects($this->never())
            ->method('processValue');

        $mockObj->expects($this->never())
            ->method('processComma');

        $mockObj->process(self::$phpcsFile, $target);
    }

    /**
     * Helper method to retrieve a mock object for the abstract class.
     *
     * The `setMethods()` method was silently deprecated in PHPUnit 9 and removed in PHPUnit 10.
     *
     * Note: direct access to the `getMockBuilder()` method is soft deprecated as of PHPUnit 10,
     * and expected to be hard deprecated in PHPUnit 11 and removed in PHPUnit 12.
     * Dealing with that is something for a later iteration of the test suite.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockedClassUnderTest()
    {
        $mockedObj = $this->getMockBuilder(
            '\PHPCSUtils\Tests\AbstractSniffs\AbstractArrayDeclaration\ArrayDeclarationSniffMock'
        );

        if (\method_exists($mockedObj, 'onlyMethods')) {
            // PHPUnit 8+.
            return $mockedObj->onlyMethods($this->methodsToMock)
                ->getMock();
        }

        // PHPUnit < 8.
        return $mockedObj->setMethods($this->methodsToMock)
            ->getMock();
    }
}
