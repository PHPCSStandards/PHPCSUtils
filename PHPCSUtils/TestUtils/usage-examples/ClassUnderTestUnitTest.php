<?php

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use YourStandard\ClassUnderTest;

class ClassUnderTestUnitTest extends UtilityMethodTestCase {

    /**
     * Testing utility method MyMethod.
     *
     * @dataProvider dataMyMethod
     *
     * @covers \YourStandard\ClassUnderTest::MyMethod
     *
     * @param string $commentString The comment which prefaces the target token in the test file.
     * @param string $expected      The expected return value.
     *
     * @return void
     */
    public function testMyMethod($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, [\T_TOKEN_CONSTANT, \T_ANOTHER_TOKEN]);
        $class    = new ClassUnderTest();
        $result   = $class->MyMethod(self::$phpcsFile, $stackPtr);
        // Or for static utility methods:
        $result   = ClassUnderTest::MyMethod(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data Provider.
     *
     * @see ClassUnderTestUnitTest::testMyMethod() For the array format.
     *
     * @return array
     */
    public function dataMyMethod()
    {
        return array(
            array('/* testTestCaseDescription * /', false),
        );
    }
}
