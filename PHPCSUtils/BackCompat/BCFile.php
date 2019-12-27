<?php
/**
 * Represents a piece of content being checked during the run.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Files;

use PHP_CodeSniffer\Util;
use PHP_CodeSniffer\Exceptions\RuntimeException;

class File
{

    /**
     * Returns the declaration names for classes, interfaces, traits, and functions.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface, trait, or function.
     *
     * @return string|null The name of the class, interface, trait, or function;
     *                     or NULL if the function or class is anonymous.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      T_FUNCTION, T_CLASS, T_ANON_CLASS,
     *                                                      T_CLOSURE, T_TRAIT, or T_INTERFACE.
     */
    public function getDeclarationName($stackPtr)
    {
        $tokenCode = $this->tokens[$stackPtr]['code'];

        if ($tokenCode === T_ANON_CLASS || $tokenCode === T_CLOSURE) {
            return null;
        }

        if ($tokenCode !== T_FUNCTION
            && $tokenCode !== T_CLASS
            && $tokenCode !== T_INTERFACE
            && $tokenCode !== T_TRAIT
        ) {
            throw new RuntimeException('Token type "'.$this->tokens[$stackPtr]['type'].'" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT');
        }

        if ($tokenCode === T_FUNCTION
            && strtolower($this->tokens[$stackPtr]['content']) !== 'function'
        ) {
            // This is a function declared without the "function" keyword.
            // So this token is the function name.
            return $this->tokens[$stackPtr]['content'];
        }

        $content = null;
        for ($i = $stackPtr; $i < $this->numTokens; $i++) {
            if ($this->tokens[$i]['code'] === T_STRING) {
                $content = $this->tokens[$i]['content'];
                break;
            }
        }

        return $content;

    }//end getDeclarationName()


    /**
     * Returns the method parameters for the specified function token.
     *
     * Also supports passing in a USE token for a closure use group.
     *
     * Each parameter is in the following format:
     *
     * <code>
     *   0 => array(
     *         'name'                => '$var',  // The variable name.
     *         'token'               => integer, // The stack pointer to the variable name.
     *         'content'             => string,  // The full content of the variable definition.
     *         'pass_by_reference'   => boolean, // Is the variable passed by reference?
     *         'reference_token'     => integer, // The stack pointer to the reference operator
     *                                           // or FALSE if the param is not passed by reference.
     *         'variable_length'     => boolean, // Is the param of variable length through use of `...` ?
     *         'variadic_token'      => integer, // The stack pointer to the ... operator
     *                                           // or FALSE if the param is not variable length.
     *         'type_hint'           => string,  // The type hint for the variable.
     *         'type_hint_token'     => integer, // The stack pointer to the start of the type hint
     *                                           // or FALSE if there is no type hint.
     *         'type_hint_end_token' => integer, // The stack pointer to the end of the type hint
     *                                           // or FALSE if there is no type hint.
     *         'nullable_type'       => boolean, // TRUE if the var type is nullable.
     *         'comma_token'         => integer, // The stack pointer to the comma after the param
     *                                           // or FALSE if this is the last param.
     *        )
     * </code>
     *
     * Parameters with default values have an additional array indexs of:
     *         'default'             => string,  // The full content of the default value.
     *         'default_token'       => integer, // The stack pointer to the start of the default value.
     *         'default_equal_token' => integer, // The stack pointer to the equals sign.
     *
     * @param int $stackPtr The position in the stack of the function token
     *                      to acquire the parameters for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_FUNCTION, T_CLOSURE, or T_USE.
     */
    public function getMethodParameters($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_FUNCTION
            && $this->tokens[$stackPtr]['code'] !== T_CLOSURE
            && $this->tokens[$stackPtr]['code'] !== T_USE
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_USE');
        }

        if ($this->tokens[$stackPtr]['code'] === T_USE) {
            $opener = $this->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1));
            if ($opener === false || isset($this->tokens[$opener]['parenthesis_owner']) === true) {
                throw new RuntimeException('$stackPtr was not a valid T_USE');
            }
        } else {
            if (isset($this->tokens[$stackPtr]['parenthesis_opener']) === false) {
                // Live coding or syntax error, so no params to find.
                return [];
            }

            $opener = $this->tokens[$stackPtr]['parenthesis_opener'];
        }

        if (isset($this->tokens[$opener]['parenthesis_closer']) === false) {
            // Live coding or syntax error, so no params to find.
            return [];
        }

        $closer = $this->tokens[$opener]['parenthesis_closer'];

        $vars            = [];
        $currVar         = null;
        $paramStart      = ($opener + 1);
        $defaultStart    = null;
        $equalToken      = null;
        $paramCount      = 0;
        $passByReference = false;
        $referenceToken  = false;
        $variableLength  = false;
        $variadicToken   = false;
        $typeHint        = '';
        $typeHintToken   = false;
        $typeHintEndToken = false;
        $nullableType     = false;

        for ($i = $paramStart; $i <= $closer; $i++) {
            // Check to see if this token has a parenthesis or bracket opener. If it does
            // it's likely to be an array which might have arguments in it. This
            // could cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($this->tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $this->tokens[$i]['parenthesis_closer']) {
                    $i = ($this->tokens[$i]['parenthesis_closer'] + 1);
                }
            }

            if (isset($this->tokens[$i]['bracket_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $this->tokens[$i]['bracket_closer']) {
                    $i = ($this->tokens[$i]['bracket_closer'] + 1);
                }
            }

            switch ($this->tokens[$i]['code']) {
            case T_BITWISE_AND:
                if ($defaultStart === null) {
                    $passByReference = true;
                    $referenceToken  = $i;
                }
                break;
            case T_VARIABLE:
                $currVar = $i;
                break;
            case T_ELLIPSIS:
                $variableLength = true;
                $variadicToken  = $i;
                break;
            case T_CALLABLE:
                if ($typeHintToken === false) {
                    $typeHintToken = $i;
                }

                $typeHint        .= $this->tokens[$i]['content'];
                $typeHintEndToken = $i;
                break;
            case T_SELF:
            case T_PARENT:
            case T_STATIC:
                // Self and parent are valid, static invalid, but was probably intended as type hint.
                if (isset($defaultStart) === false) {
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint        .= $this->tokens[$i]['content'];
                    $typeHintEndToken = $i;
                }
                break;
            case T_STRING:
                // This is a string, so it may be a type hint, but it could
                // also be a constant used as a default value.
                $prevComma = false;
                for ($t = $i; $t >= $opener; $t--) {
                    if ($this->tokens[$t]['code'] === T_COMMA) {
                        $prevComma = $t;
                        break;
                    }
                }

                if ($prevComma !== false) {
                    $nextEquals = false;
                    for ($t = $prevComma; $t < $i; $t++) {
                        if ($this->tokens[$t]['code'] === T_EQUAL) {
                            $nextEquals = $t;
                            break;
                        }
                    }

                    if ($nextEquals !== false) {
                        break;
                    }
                }

                if ($defaultStart === null) {
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint        .= $this->tokens[$i]['content'];
                    $typeHintEndToken = $i;
                }
                break;
            case T_NS_SEPARATOR:
                // Part of a type hint or default value.
                if ($defaultStart === null) {
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint        .= $this->tokens[$i]['content'];
                    $typeHintEndToken = $i;
                }
                break;
            case T_NULLABLE:
                if ($defaultStart === null) {
                    $nullableType     = true;
                    $typeHint        .= $this->tokens[$i]['content'];
                    $typeHintEndToken = $i;
                }
                break;
            case T_CLOSE_PARENTHESIS:
            case T_COMMA:
                // If it's null, then there must be no parameters for this
                // method.
                if ($currVar === null) {
                    continue 2;
                }

                $vars[$paramCount]            = [];
                $vars[$paramCount]['token']   = $currVar;
                $vars[$paramCount]['name']    = $this->tokens[$currVar]['content'];
                $vars[$paramCount]['content'] = trim($this->getTokensAsString($paramStart, ($i - $paramStart)));

                if ($defaultStart !== null) {
                    $vars[$paramCount]['default']       = trim($this->getTokensAsString($defaultStart, ($i - $defaultStart)));
                    $vars[$paramCount]['default_token'] = $defaultStart;
                    $vars[$paramCount]['default_equal_token'] = $equalToken;
                }

                $vars[$paramCount]['pass_by_reference']   = $passByReference;
                $vars[$paramCount]['reference_token']     = $referenceToken;
                $vars[$paramCount]['variable_length']     = $variableLength;
                $vars[$paramCount]['variadic_token']      = $variadicToken;
                $vars[$paramCount]['type_hint']           = $typeHint;
                $vars[$paramCount]['type_hint_token']     = $typeHintToken;
                $vars[$paramCount]['type_hint_end_token'] = $typeHintEndToken;
                $vars[$paramCount]['nullable_type']       = $nullableType;

                if ($this->tokens[$i]['code'] === T_COMMA) {
                    $vars[$paramCount]['comma_token'] = $i;
                } else {
                    $vars[$paramCount]['comma_token'] = false;
                }

                // Reset the vars, as we are about to process the next parameter.
                $defaultStart    = null;
                $equalToken      = null;
                $paramStart      = ($i + 1);
                $passByReference = false;
                $referenceToken  = false;
                $variableLength  = false;
                $variadicToken   = false;
                $typeHint        = '';
                $typeHintToken   = false;
                $nullableType    = false;

                $paramCount++;
                break;
            case T_EQUAL:
                $defaultStart = $this->findNext(Util\Tokens::$emptyTokens, ($i + 1), null, true);
                $equalToken   = $i;
                break;
            }//end switch
        }//end for

        return $vars;

    }//end getMethodParameters()


    /**
     * Returns the visibility and implementation properties of a method.
     *
     * The format of the return value is:
     * <code>
     *   array(
     *    'scope'                => 'public', // Public, private, or protected
     *    'scope_specified'      => true,     // TRUE if the scope keyword was found.
     *    'return_type'          => '',       // The return type of the method.
     *    'return_type_token'    => integer,  // The stack pointer to the start of the return type
     *                                        // or FALSE if there is no return type.
     *    'nullable_return_type' => false,    // TRUE if the return type is nullable.
     *    'is_abstract'          => false,    // TRUE if the abstract keyword was found.
     *    'is_final'             => false,    // TRUE if the final keyword was found.
     *    'is_static'            => false,    // TRUE if the static keyword was found.
     *    'has_body'             => false,    // TRUE if the method has a body
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the function token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                        T_FUNCTION token.
     */
    public function getMethodProperties($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_FUNCTION
            && $this->tokens[$stackPtr]['code'] !== T_CLOSURE
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE');
        }

        if ($this->tokens[$stackPtr]['code'] === T_FUNCTION) {
            $valid = [
                T_PUBLIC      => T_PUBLIC,
                T_PRIVATE     => T_PRIVATE,
                T_PROTECTED   => T_PROTECTED,
                T_STATIC      => T_STATIC,
                T_FINAL       => T_FINAL,
                T_ABSTRACT    => T_ABSTRACT,
                T_WHITESPACE  => T_WHITESPACE,
                T_COMMENT     => T_COMMENT,
                T_DOC_COMMENT => T_DOC_COMMENT,
            ];
        } else {
            $valid = [
                T_STATIC      => T_STATIC,
                T_WHITESPACE  => T_WHITESPACE,
                T_COMMENT     => T_COMMENT,
                T_DOC_COMMENT => T_DOC_COMMENT,
            ];
        }

        $scope          = 'public';
        $scopeSpecified = false;
        $isAbstract     = false;
        $isFinal        = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$this->tokens[$i]['code']]) === false) {
                break;
            }

            switch ($this->tokens[$i]['code']) {
            case T_PUBLIC:
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
                $scopeSpecified = true;
                break;
            case T_ABSTRACT:
                $isAbstract = true;
                break;
            case T_FINAL:
                $isFinal = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }//end switch
        }//end for

        $returnType         = '';
        $returnTypeToken    = false;
        $nullableReturnType = false;
        $hasBody            = true;

        if (isset($this->tokens[$stackPtr]['parenthesis_closer']) === true) {
            $scopeOpener = null;
            if (isset($this->tokens[$stackPtr]['scope_opener']) === true) {
                $scopeOpener = $this->tokens[$stackPtr]['scope_opener'];
            }

            $valid = [
                T_STRING       => T_STRING,
                T_CALLABLE     => T_CALLABLE,
                T_SELF         => T_SELF,
                T_PARENT       => T_PARENT,
                T_NS_SEPARATOR => T_NS_SEPARATOR,
            ];

            for ($i = $this->tokens[$stackPtr]['parenthesis_closer']; $i < $this->numTokens; $i++) {
                if (($scopeOpener === null && $this->tokens[$i]['code'] === T_SEMICOLON)
                    || ($scopeOpener !== null && $i === $scopeOpener)
                ) {
                    // End of function definition.
                    break;
                }

                if ($this->tokens[$i]['code'] === T_NULLABLE) {
                    $nullableReturnType = true;
                }

                if (isset($valid[$this->tokens[$i]['code']]) === true) {
                    if ($returnTypeToken === false) {
                        $returnTypeToken = $i;
                    }

                    $returnType .= $this->tokens[$i]['content'];
                }
            }

            $end     = $this->findNext([T_OPEN_CURLY_BRACKET, T_SEMICOLON], $this->tokens[$stackPtr]['parenthesis_closer']);
            $hasBody = $this->tokens[$end]['code'] === T_OPEN_CURLY_BRACKET;
        }//end if

        if ($returnType !== '' && $nullableReturnType === true) {
            $returnType = '?'.$returnType;
        }

        return [
            'scope'                => $scope,
            'scope_specified'      => $scopeSpecified,
            'return_type'          => $returnType,
            'return_type_token'    => $returnTypeToken,
            'nullable_return_type' => $nullableReturnType,
            'is_abstract'          => $isAbstract,
            'is_final'             => $isFinal,
            'is_static'            => $isStatic,
            'has_body'             => $hasBody,
        ];

    }//end getMethodProperties()


    /**
     * Returns the visibility and implementation properties of a class member var.
     *
     * The format of the return value is:
     *
     * <code>
     *   array(
     *    'scope'           => string,  // Public, private, or protected.
     *    'scope_specified' => boolean, // TRUE if the scope was explicitly specified.
     *    'is_static'       => boolean, // TRUE if the static keyword was found.
     *    'type'            => string,  // The type of the var (empty if no type specifed).
     *    'type_token'      => integer, // The stack pointer to the start of the type
     *                                  // or FALSE if there is no type.
     *    'type_end_token'  => integer, // The stack pointer to the end of the type
     *                                  // or FALSE if there is no type.
     *    'nullable_type'   => boolean, // TRUE if the type is nullable.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_VARIABLE token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                        T_VARIABLE token, or if the position is not
     *                                                        a class member variable.
     */
    public function getMemberProperties($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_VARIABLE) {
            throw new RuntimeException('$stackPtr must be of type T_VARIABLE');
        }

        $conditions = array_keys($this->tokens[$stackPtr]['conditions']);
        $ptr        = array_pop($conditions);
        if (isset($this->tokens[$ptr]) === false
            || ($this->tokens[$ptr]['code'] !== T_CLASS
            && $this->tokens[$ptr]['code'] !== T_ANON_CLASS
            && $this->tokens[$ptr]['code'] !== T_TRAIT)
        ) {
            if (isset($this->tokens[$ptr]) === true
                && $this->tokens[$ptr]['code'] === T_INTERFACE
            ) {
                // T_VARIABLEs in interfaces can actually be method arguments
                // but they wont be seen as being inside the method because there
                // are no scope openers and closers for abstract methods. If it is in
                // parentheses, we can be pretty sure it is a method argument.
                if (isset($this->tokens[$stackPtr]['nested_parenthesis']) === false
                    || empty($this->tokens[$stackPtr]['nested_parenthesis']) === true
                ) {
                    $error = 'Possible parse error: interfaces may not include member vars';
                    $this->addWarning($error, $stackPtr, 'Internal.ParseError.InterfaceHasMemberVar');
                    return [];
                }
            } else {
                throw new RuntimeException('$stackPtr is not a class member var');
            }
        }

        // Make sure it's not a method parameter.
        if (empty($this->tokens[$stackPtr]['nested_parenthesis']) === false) {
            $parenthesis = array_keys($this->tokens[$stackPtr]['nested_parenthesis']);
            $deepestOpen = array_pop($parenthesis);
            if ($deepestOpen > $ptr
                && isset($this->tokens[$deepestOpen]['parenthesis_owner']) === true
                && $this->tokens[$this->tokens[$deepestOpen]['parenthesis_owner']]['code'] === T_FUNCTION
            ) {
                throw new RuntimeException('$stackPtr is not a class member var');
            }
        }

        $valid = [
            T_PUBLIC    => T_PUBLIC,
            T_PRIVATE   => T_PRIVATE,
            T_PROTECTED => T_PROTECTED,
            T_STATIC    => T_STATIC,
            T_VAR       => T_VAR,
        ];

        $valid += Util\Tokens::$emptyTokens;

        $scope          = 'public';
        $scopeSpecified = false;
        $isStatic       = false;

        $startOfStatement = $this->findPrevious(
            [
                T_SEMICOLON,
                T_OPEN_CURLY_BRACKET,
                T_CLOSE_CURLY_BRACKET,
            ],
            ($stackPtr - 1)
        );

        for ($i = ($startOfStatement + 1); $i < $stackPtr; $i++) {
            if (isset($valid[$this->tokens[$i]['code']]) === false) {
                break;
            }

            switch ($this->tokens[$i]['code']) {
            case T_PUBLIC:
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
                $scopeSpecified = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }
        }//end for

        $type         = '';
        $typeToken    = false;
        $typeEndToken = false;
        $nullableType = false;

        if ($i < $stackPtr) {
            // We've found a type.
            $valid = [
                T_STRING       => T_STRING,
                T_CALLABLE     => T_CALLABLE,
                T_SELF         => T_SELF,
                T_PARENT       => T_PARENT,
                T_NS_SEPARATOR => T_NS_SEPARATOR,
            ];

            for ($i; $i < $stackPtr; $i++) {
                if ($this->tokens[$i]['code'] === T_VARIABLE) {
                    // Hit another variable in a group definition.
                    break;
                }

                if ($this->tokens[$i]['code'] === T_NULLABLE) {
                    $nullableType = true;
                }

                if (isset($valid[$this->tokens[$i]['code']]) === true) {
                    $typeEndToken = $i;
                    if ($typeToken === false) {
                        $typeToken = $i;
                    }

                    $type .= $this->tokens[$i]['content'];
                }
            }

            if ($type !== '' && $nullableType === true) {
                $type = '?'.$type;
            }
        }//end if

        return [
            'scope'           => $scope,
            'scope_specified' => $scopeSpecified,
            'is_static'       => $isStatic,
            'type'            => $type,
            'type_token'      => $typeToken,
            'type_end_token'  => $typeEndToken,
            'nullable_type'   => $nullableType,
        ];

    }//end getMemberProperties()


    /**
     * Returns the visibility and implementation properties of a class.
     *
     * The format of the return value is:
     * <code>
     *   array(
     *    'is_abstract' => false, // true if the abstract keyword was found.
     *    'is_final'    => false, // true if the final keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_CLASS token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_CLASS token.
     */
    public function getClassProperties($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_CLASS) {
            throw new RuntimeException('$stackPtr must be of type T_CLASS');
        }

        $valid = [
            T_FINAL       => T_FINAL,
            T_ABSTRACT    => T_ABSTRACT,
            T_WHITESPACE  => T_WHITESPACE,
            T_COMMENT     => T_COMMENT,
            T_DOC_COMMENT => T_DOC_COMMENT,
        ];

        $isAbstract = false;
        $isFinal    = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$this->tokens[$i]['code']]) === false) {
                break;
            }

            switch ($this->tokens[$i]['code']) {
            case T_ABSTRACT:
                $isAbstract = true;
                break;

            case T_FINAL:
                $isFinal = true;
                break;
            }
        }//end for

        return [
            'is_abstract' => $isAbstract,
            'is_final'    => $isFinal,
        ];

    }//end getClassProperties()


    /**
     * Determine if the passed token is a reference operator.
     *
     * Returns true if the specified token position represents a reference.
     * Returns false if the token represents a bitwise operator.
     *
     * @param int $stackPtr The position of the T_BITWISE_AND token.
     *
     * @return boolean
     */
    public function isReference($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_BITWISE_AND) {
            return false;
        }

        $tokenBefore = $this->findPrevious(
            Util\Tokens::$emptyTokens,
            ($stackPtr - 1),
            null,
            true
        );

        if ($this->tokens[$tokenBefore]['code'] === T_FUNCTION) {
            // Function returns a reference.
            return true;
        }

        if ($this->tokens[$tokenBefore]['code'] === T_DOUBLE_ARROW) {
            // Inside a foreach loop or array assignment, this is a reference.
            return true;
        }

        if ($this->tokens[$tokenBefore]['code'] === T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (isset(Util\Tokens::$assignmentTokens[$this->tokens[$tokenBefore]['code']]) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        $tokenAfter = $this->findNext(
            Util\Tokens::$emptyTokens,
            ($stackPtr + 1),
            null,
            true
        );

        if ($this->tokens[$tokenAfter]['code'] === T_NEW) {
            return true;
        }

        if (isset($this->tokens[$stackPtr]['nested_parenthesis']) === true) {
            $brackets    = $this->tokens[$stackPtr]['nested_parenthesis'];
            $lastBracket = array_pop($brackets);
            if (isset($this->tokens[$lastBracket]['parenthesis_owner']) === true) {
                $owner = $this->tokens[$this->tokens[$lastBracket]['parenthesis_owner']];
                if ($owner['code'] === T_FUNCTION
                    || $owner['code'] === T_CLOSURE
                ) {
                    $params = $this->getMethodParameters($this->tokens[$lastBracket]['parenthesis_owner']);
                    foreach ($params as $param) {
                        $varToken = $tokenAfter;
                        if ($param['variable_length'] === true) {
                            $varToken = $this->findNext(
                                (Util\Tokens::$emptyTokens + [T_ELLIPSIS]),
                                ($stackPtr + 1),
                                null,
                                true
                            );
                        }

                        if ($param['token'] === $varToken
                            && $param['pass_by_reference'] === true
                        ) {
                            // Function parameter declared to be passed by reference.
                            return true;
                        }
                    }
                }//end if
            } else {
                $prev = false;
                for ($t = ($this->tokens[$lastBracket]['parenthesis_opener'] - 1); $t >= 0; $t--) {
                    if ($this->tokens[$t]['code'] !== T_WHITESPACE) {
                        $prev = $t;
                        break;
                    }
                }

                if ($prev !== false && $this->tokens[$prev]['code'] === T_USE) {
                    // Closure use by reference.
                    return true;
                }
            }//end if
        }//end if

        // Pass by reference in function calls and assign by reference in arrays.
        if ($this->tokens[$tokenBefore]['code'] === T_OPEN_PARENTHESIS
            || $this->tokens[$tokenBefore]['code'] === T_COMMA
            || $this->tokens[$tokenBefore]['code'] === T_OPEN_SHORT_ARRAY
        ) {
            if ($this->tokens[$tokenAfter]['code'] === T_VARIABLE) {
                return true;
            } else {
                $skip   = Util\Tokens::$emptyTokens;
                $skip[] = T_NS_SEPARATOR;
                $skip[] = T_SELF;
                $skip[] = T_PARENT;
                $skip[] = T_STATIC;
                $skip[] = T_STRING;
                $skip[] = T_NAMESPACE;
                $skip[] = T_DOUBLE_COLON;

                $nextSignificantAfter = $this->findNext(
                    $skip,
                    ($stackPtr + 1),
                    null,
                    true
                );
                if ($this->tokens[$nextSignificantAfter]['code'] === T_VARIABLE) {
                    return true;
                }
            }//end if
        }//end if

        return false;

    }//end isReference()


    /**
     * Returns the content of the tokens from the specified start position in
     * the token stack for the specified length.
     *
     * @param int  $start       The position to start from in the token stack.
     * @param int  $length      The length of tokens to traverse from the start pos.
     * @param bool $origContent Whether the original content or the tab replaced
     *                          content should be used.
     *
     * @return string The token contents.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position does not exist.
     */
    public function getTokensAsString($start, $length, $origContent=false)
    {
        if (is_int($start) === false || isset($this->tokens[$start]) === false) {
            throw new RuntimeException('The $start position for getTokensAsString() must exist in the token stack');
        }

        if (is_int($length) === false || $length <= 0) {
            return '';
        }

        $str = '';
        $end = ($start + $length);
        if ($end > $this->numTokens) {
            $end = $this->numTokens;
        }

        for ($i = $start; $i < $end; $i++) {
            // If tabs are being converted to spaces by the tokeniser, the
            // original content should be used instead of the converted content.
            if ($origContent === true && isset($this->tokens[$i]['orig_content']) === true) {
                $str .= $this->tokens[$i]['orig_content'];
            } else {
                $str .= $this->tokens[$i]['content'];
            }
        }

        return $str;

    }//end getTokensAsString()


    /**
     * Returns the position of the first non-whitespace token in a statement.
     *
     * @param int       $start  The position to start searching from in the token stack.
     * @param int|array $ignore Token types that should not be considered stop points.
     *
     * @return int
     */
    public function findStartOfStatement($start, $ignore=null)
    {
        $endTokens = Util\Tokens::$blockOpeners;

        $endTokens[T_COLON]            = true;
        $endTokens[T_COMMA]            = true;
        $endTokens[T_DOUBLE_ARROW]     = true;
        $endTokens[T_SEMICOLON]        = true;
        $endTokens[T_OPEN_TAG]         = true;
        $endTokens[T_CLOSE_TAG]        = true;
        $endTokens[T_OPEN_SHORT_ARRAY] = true;

        if ($ignore !== null) {
            $ignore = (array) $ignore;
            foreach ($ignore as $code) {
                unset($endTokens[$code]);
            }
        }

        $lastNotEmpty = $start;

        for ($i = $start; $i >= 0; $i--) {
            if (isset($endTokens[$this->tokens[$i]['code']]) === true) {
                // Found the end of the previous statement.
                return $lastNotEmpty;
            }

            if (isset($this->tokens[$i]['scope_opener']) === true
                && $i === $this->tokens[$i]['scope_closer']
            ) {
                // Found the end of the previous scope block.
                return $lastNotEmpty;
            }

            // Skip nested statements.
            if (isset($this->tokens[$i]['bracket_opener']) === true
                && $i === $this->tokens[$i]['bracket_closer']
            ) {
                $i = $this->tokens[$i]['bracket_opener'];
            } else if (isset($this->tokens[$i]['parenthesis_opener']) === true
                && $i === $this->tokens[$i]['parenthesis_closer']
            ) {
                $i = $this->tokens[$i]['parenthesis_opener'];
            }

            if (isset(Util\Tokens::$emptyTokens[$this->tokens[$i]['code']]) === false) {
                $lastNotEmpty = $i;
            }
        }//end for

        return 0;

    }//end findStartOfStatement()


    /**
     * Returns the position of the last non-whitespace token in a statement.
     *
     * @param int       $start  The position to start searching from in the token stack.
     * @param int|array $ignore Token types that should not be considered stop points.
     *
     * @return int
     */
    public function findEndOfStatement($start, $ignore=null)
    {
        $endTokens = [
            T_COLON                => true,
            T_COMMA                => true,
            T_DOUBLE_ARROW         => true,
            T_SEMICOLON            => true,
            T_CLOSE_PARENTHESIS    => true,
            T_CLOSE_SQUARE_BRACKET => true,
            T_CLOSE_CURLY_BRACKET  => true,
            T_CLOSE_SHORT_ARRAY    => true,
            T_OPEN_TAG             => true,
            T_CLOSE_TAG            => true,
        ];

        if ($ignore !== null) {
            $ignore = (array) $ignore;
            foreach ($ignore as $code) {
                unset($endTokens[$code]);
            }
        }

        $lastNotEmpty = $start;

        for ($i = $start; $i < $this->numTokens; $i++) {
            if ($i !== $start && isset($endTokens[$this->tokens[$i]['code']]) === true) {
                // Found the end of the statement.
                if ($this->tokens[$i]['code'] === T_CLOSE_PARENTHESIS
                    || $this->tokens[$i]['code'] === T_CLOSE_SQUARE_BRACKET
                    || $this->tokens[$i]['code'] === T_CLOSE_CURLY_BRACKET
                    || $this->tokens[$i]['code'] === T_CLOSE_SHORT_ARRAY
                    || $this->tokens[$i]['code'] === T_OPEN_TAG
                    || $this->tokens[$i]['code'] === T_CLOSE_TAG
                ) {
                    return $lastNotEmpty;
                }

                return $i;
            }

            // Skip nested statements.
            if (isset($this->tokens[$i]['scope_closer']) === true
                && ($i === $this->tokens[$i]['scope_opener']
                || $i === $this->tokens[$i]['scope_condition'])
            ) {
                if ($i === $start && isset(Util\Tokens::$scopeOpeners[$this->tokens[$i]['code']]) === true) {
                    return $this->tokens[$i]['scope_closer'];
                }

                $i = $this->tokens[$i]['scope_closer'];
            } else if (isset($this->tokens[$i]['bracket_closer']) === true
                && $i === $this->tokens[$i]['bracket_opener']
            ) {
                $i = $this->tokens[$i]['bracket_closer'];
            } else if (isset($this->tokens[$i]['parenthesis_closer']) === true
                && $i === $this->tokens[$i]['parenthesis_opener']
            ) {
                $i = $this->tokens[$i]['parenthesis_closer'];
            } else if ($this->tokens[$i]['code'] === T_OPEN_USE_GROUP) {
                $end = $this->findNext(T_CLOSE_USE_GROUP, ($i + 1));
                if ($end !== false) {
                    $i = $end;
                }
            }

            if (isset(Util\Tokens::$emptyTokens[$this->tokens[$i]['code']]) === false) {
                $lastNotEmpty = $i;
            }
        }//end for

        return ($this->numTokens - 1);

    }//end findEndOfStatement()


    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * @param int              $stackPtr The position of the token we are checking.
     * @param int|string|array $types    The type(s) of tokens to search for.
     *
     * @return boolean
     */
    public function hasCondition($stackPtr, $types)
    {
        // Check for the existence of the token.
        if (isset($this->tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($this->tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $types      = (array) $types;
        $conditions = $this->tokens[$stackPtr]['conditions'];

        foreach ($types as $type) {
            if (in_array($type, $conditions, true) === true) {
                // We found a token with the required type.
                return true;
            }
        }

        return false;

    }//end hasCondition()


    /**
     * Return the position of the condition for the passed token.
     *
     * Returns FALSE if the token does not have the condition.
     *
     * @param int        $stackPtr The position of the token we are checking.
     * @param int|string $type     The type of token to search for.
     *
     * @return int
     */
    public function getCondition($stackPtr, $type)
    {
        // Check for the existence of the token.
        if (isset($this->tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($this->tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $conditions = $this->tokens[$stackPtr]['conditions'];
        foreach ($conditions as $token => $condition) {
            if ($condition === $type) {
                return $token;
            }
        }

        return false;

    }//end getCondition()


    /**
     * Returns the name of the class that the specified class extends.
     * (works for classes, anonymous classes and interfaces)
     *
     * Returns FALSE on error or if there is no extended class name.
     *
     * @param int $stackPtr The stack position of the class.
     *
     * @return string|false
     */
    public function findExtendedClassName($stackPtr)
    {
        // Check for the existence of the token.
        if (isset($this->tokens[$stackPtr]) === false) {
            return false;
        }

        if ($this->tokens[$stackPtr]['code'] !== T_CLASS
            && $this->tokens[$stackPtr]['code'] !== T_ANON_CLASS
            && $this->tokens[$stackPtr]['code'] !== T_INTERFACE
        ) {
            return false;
        }

        if (isset($this->tokens[$stackPtr]['scope_opener']) === false) {
            return false;
        }

        $classOpenerIndex = $this->tokens[$stackPtr]['scope_opener'];
        $extendsIndex     = $this->findNext(T_EXTENDS, $stackPtr, $classOpenerIndex);
        if (false === $extendsIndex) {
            return false;
        }

        $find = [
            T_NS_SEPARATOR,
            T_STRING,
            T_WHITESPACE,
        ];

        $end  = $this->findNext($find, ($extendsIndex + 1), ($classOpenerIndex + 1), true);
        $name = $this->getTokensAsString(($extendsIndex + 1), ($end - $extendsIndex - 1));
        $name = trim($name);

        if ($name === '') {
            return false;
        }

        return $name;

    }//end findExtendedClassName()


    /**
     * Returns the names of the interfaces that the specified class implements.
     *
     * Returns FALSE on error or if there are no implemented interface names.
     *
     * @param int $stackPtr The stack position of the class.
     *
     * @return array|false
     */
    public function findImplementedInterfaceNames($stackPtr)
    {
        // Check for the existence of the token.
        if (isset($this->tokens[$stackPtr]) === false) {
            return false;
        }

        if ($this->tokens[$stackPtr]['code'] !== T_CLASS
            && $this->tokens[$stackPtr]['code'] !== T_ANON_CLASS
        ) {
            return false;
        }

        if (isset($this->tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classOpenerIndex = $this->tokens[$stackPtr]['scope_opener'];
        $implementsIndex  = $this->findNext(T_IMPLEMENTS, $stackPtr, $classOpenerIndex);
        if ($implementsIndex === false) {
            return false;
        }

        $find = [
            T_NS_SEPARATOR,
            T_STRING,
            T_WHITESPACE,
            T_COMMA,
        ];

        $end  = $this->findNext($find, ($implementsIndex + 1), ($classOpenerIndex + 1), true);
        $name = $this->getTokensAsString(($implementsIndex + 1), ($end - $implementsIndex - 1));
        $name = trim($name);

        if ($name === '') {
            return false;
        } else {
            $names = explode(',', $name);
            $names = array_map('trim', $names);
            return $names;
        }

    }//end findImplementedInterfaceNames()


}//end class
