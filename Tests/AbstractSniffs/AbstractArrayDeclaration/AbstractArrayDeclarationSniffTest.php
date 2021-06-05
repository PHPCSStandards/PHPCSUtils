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

/**
 * Tests for the \PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff class.
 *
 * @covers \PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff
 *
 * @group abstracts
 *
 * @since 1.0.0
 */
class AbstractArrayDeclarationSniffTest extends PolyfilledTestCase
{

    /**
     * List of methods in the abstract which should be mocked.
     *
     * Needed for PHPUnit cross-version support as PHPUnit 4.x does not have a
     * `setMethodsExcept()` method yet.
     *
     * @var array
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
     * Test that the abstract sniff correctly bows out when presented with a token which is not an array.
     *
     * @return void
     */
    public function testShortList()
    {
        $target = $this->getTargetToken(
            '/* testShortList */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
        $target = $this->getTargetToken(
            '/* testEmptyArray */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

        $mockObj->expects($this->once())
            ->method('processOpenClose')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->equalTo($target),
                $this->equalTo($target + 5)
            );

        $mockObj->expects($this->exactly(2))
            ->method('processNoKey')
            ->withConsecutive(
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 1), $this->equalTo(1)],
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 3), $this->equalTo(2)]
            );

        $mockObj->expects($this->exactly(2))
            ->method('processValue')
            ->withConsecutive(
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 1),
                    $this->equalTo($target + 1),
                    $this->equalTo(1),
                ],
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 3),
                    $this->equalTo($target + 4),
                    $this->equalTo(2),
                ]
            );

        $mockObj->expects($this->once())
            ->method('processComma')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->equalTo($target + 2),
                $this->equalTo(1)
            );

        $mockObj->process(self::$phpcsFile, $target);

        // Verify that the properties have been correctly set.
        $this->assertAttributeValueSame($target, 'stackPtr', $mockObj);
        $this->assertAttributeValueSame($target, 'arrayOpener', $mockObj);
        $this->assertAttributeValueSame(($target + 5), 'arrayCloser', $mockObj);
        $this->assertAttributeValueSame(2, 'itemCount', $mockObj);
        $this->assertAttributeValueSame(true, 'singleLine', $mockObj);
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
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

        $mockObj->expects($this->once())
            ->method('processOpenClose')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->equalTo($target + 1),
                $this->equalTo($target + 35)
            );

        $mockObj->expects($this->exactly(3))
            ->method('processKey')
            ->withConsecutive(
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 2),
                    $this->equalTo($target + 5),
                    $this->equalTo(1),
                ],
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 10),
                    $this->equalTo($target + 13),
                    $this->equalTo(2),
                ],
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 18),
                    $this->equalTo($target + 21),
                    $this->equalTo(3),
                ]
            );

        $mockObj->expects($this->exactly(3))
            ->method('processArrow')
            ->withConsecutive(
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 6), $this->equalTo(1)],
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 14), $this->equalTo(2)],
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 22), $this->equalTo(3)]
            );

        $mockObj->expects($this->exactly(3))
            ->method('processValue')
            ->withConsecutive(
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 7),
                    $this->equalTo($target + 8),
                    $this->equalTo(1),
                ],
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 15),
                    $this->equalTo($target + 16),
                    $this->equalTo(2),
                ],
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 23),
                    $this->equalTo($target + 24),
                    $this->equalTo(3),
                ]
            )
            ->will($this->onConsecutiveCalls(null, null, true)); // Testing short-circuiting the loop.

        $mockObj->expects($this->exactly(2))
            ->method('processComma')
            ->withConsecutive(
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 9), $this->equalTo(1)],
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 17), $this->equalTo(2)]
            );

        $mockObj->process(self::$phpcsFile, $target);

        // Verify that the properties have been correctly set.
        $this->assertAttributeValueSame($target, 'stackPtr', $mockObj);
        $this->assertAttributeValueSame(($target + 1), 'arrayOpener', $mockObj);
        $this->assertAttributeValueSame(($target + 35), 'arrayCloser', $mockObj);
        $this->assertAttributeValueSame(4, 'itemCount', $mockObj);
        $this->assertAttributeValueSame(false, 'singleLine', $mockObj);
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
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

        $mockObj->expects($this->once())
            ->method('processOpenClose')
            ->with(
                $this->identicalTo(self::$phpcsFile),
                $this->equalTo($target),
                $this->equalTo($target + 22)
            );

        $mockObj->expects($this->exactly(2))
            ->method('processKey')
            ->withConsecutive(
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 1),
                    $this->equalTo($target + 4),
                    $this->equalTo(1),
                ],
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 13),
                    $this->equalTo($target + 16),
                    $this->equalTo(3),
                ]
            );

        $mockObj->expects($this->once())
            ->method('processNoKey')
            ->withConsecutive(
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 9), $this->equalTo(2)]
            );

        $mockObj->expects($this->exactly(2))
            ->method('processArrow')
            ->withConsecutive(
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 5), $this->equalTo(1)],
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 17), $this->equalTo(3)]
            );

        $mockObj->expects($this->exactly(3))
            ->method('processValue')
            ->withConsecutive(
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 6),
                    $this->equalTo($target + 7),
                    $this->equalTo(1),
                ],
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 9),
                    $this->equalTo($target + 11),
                    $this->equalTo(2),
                ],
                [
                    $this->identicalTo(self::$phpcsFile),
                    $this->equalTo($target + 18),
                    $this->equalTo($target + 19),
                    $this->equalTo(3),
                ]
            );

        $mockObj->expects($this->exactly(3))
            ->method('processComma')
            ->withConsecutive(
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 8), $this->equalTo(1)],
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 12), $this->equalTo(2)],
                [$this->identicalTo(self::$phpcsFile), $this->equalTo($target + 20), $this->equalTo(3)]
            );

        $mockObj->process(self::$phpcsFile, $target);

        // Verify that the properties have been correctly set.
        $this->assertAttributeValueSame($target, 'stackPtr', $mockObj);
        $this->assertAttributeValueSame($target, 'arrayOpener', $mockObj);
        $this->assertAttributeValueSame(($target + 22), 'arrayCloser', $mockObj);
        $this->assertAttributeValueSame(3, 'itemCount', $mockObj);
        $this->assertAttributeValueSame(false, 'singleLine', $mockObj);
    }

    /**
     * Test the abstract sniff correctly ignores empty array items (parse error).
     *
     * @return void
     */
    public function testEmptyArrayItem()
    {
        $target = $this->getTargetToken(
            '/* testEmptyArrayItem */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
        $target = $this->getTargetToken(
            '/* testShortCircuit */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
        $target = $this->getTargetToken(
            '/* testShortCircuit */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
        $target = $this->getTargetToken(
            '/* testShortCircuit */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
        $target = $this->getTargetToken(
            '/* testShortCircuit */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
        $target = $this->getTargetToken(
            '/* testShortCircuit */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
        $target = $this->getTargetToken(
            '/* testShortCircuit */',
            [\T_ARRAY, \T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );

        $mockObj = $this->getMockBuilder('\PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff')
            ->setMethods($this->methodsToMock)
            ->getMockForAbstractClass();

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
}
