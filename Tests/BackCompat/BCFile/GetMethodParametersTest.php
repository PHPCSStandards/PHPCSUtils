<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:getMethodParameters method.
 *
 * This class is imported from the PHP_CodeSniffer project.
 *
 * Copyright of the original code in this class as per the import:
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <jrf@phpcodesniffer.info>
 *
 * With documentation contributions from:
 * @author    Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @author    George Mponos <gmponos@gmail.com>
 *
 * @copyright 2010-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

class GetMethodParametersTest extends UtilityMethodTestCase
{


    /**
     * Verify pass-by-reference parsing.
     *
     * @return void
     */
    public function testPassByReference()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => '&$var',
            'pass_by_reference' => true,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPassByReference()


    /**
     * Verify array hint parsing.
     *
     * @return void
     */
    public function testArrayHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'array $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'array',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrayHint()


    /**
     * Verify type hint parsing.
     *
     * @return void
     */
    public function testTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => 'foo $var1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'foo',
            'nullable_type'     => false,
        ];

        $expected[1] = [
            'name'              => '$var2',
            'content'           => 'bar $var2',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'bar',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testTypeHint()


    /**
     * Verify self type hint parsing.
     *
     * @return void
     */
    public function testSelfTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'self $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'self',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testSelfTypeHint()


    /**
     * Verify nullable type hint parsing.
     *
     * @return void
     */
    public function testNullableTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '?int $var1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?int',
            'nullable_type'     => true,
        ];

        $expected[1] = [
            'name'              => '$var2',
            'content'           => '?\bar $var2',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?\bar',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNullableTypeHint()


    /**
     * Verify variable.
     *
     * @return void
     */
    public function testVariable()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => '$var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testVariable()


    /**
     * Verify default value parsing with a single function param.
     *
     * @return void
     */
    public function testSingleDefaultValue()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '$var1=self::CONSTANT',
            'default'           => 'self::CONSTANT',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testSingleDefaultValue()


    /**
     * Verify default value parsing.
     *
     * @return void
     */
    public function testDefaultValues()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '$var1=1',
            'default'           => '1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$var2',
            'content'           => "\$var2='value'",
            'default'           => "'value'",
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testDefaultValues()


    /**
     * Verify "bitwise and" in default value !== pass-by-reference.
     *
     * @return void
     */
    public function testBitwiseAndConstantExpressionDefaultValue()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$a',
            'content'           => '$a = 10 & 20',
            'default'           => '10 & 20',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBitwiseAndConstantExpressionDefaultValue()


    /**
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     *
     * @return void
     */
    private function getMethodParametersTestHelper($commentString, $expected)
    {
        $function = $this->getTargetToken($commentString, [T_FUNCTION]);
        $found    = BCFile::getMethodParameters(self::$phpcsFile, $function);

        $this->assertArraySubset($expected, $found, true);

    }//end getMethodParametersTestHelper()


}//end class
