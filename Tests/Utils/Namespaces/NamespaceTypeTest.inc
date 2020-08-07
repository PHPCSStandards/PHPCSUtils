<?php

/* testNamespaceDeclaration */
namespace MyNamespace;

/* testNamespaceDeclarationWithComment */
namespace /*comment*/ My\Other\Namespace;

/* testNamespaceDeclarationScoped */
namespace {
}

/* testNamespaceOperator */
$a = namespace\functionCall();

/* testNamespaceOperatorWithAnnotation */
$a = namespace
     // phpcs:ignore Standard.Category.SniffName -- for reasons.
     \functionCall();

/* testNamespaceOperatorInConditional */
if ( $a === $b ) {
    echo namespace\CONSTANT_NAME;
}

/* testNamespaceOperatorInClosedScope */
function closedScope() {
    echo namespace\ClassName::method();

    while( true ) {
        /* testNamespaceOperatorInParentheses */
        function_call( namespace\ClassName::$property );
    }
}

/* testNamespaceOperatorGlobalNamespaceStartOfStatementFunctionCall */
namespace\functionCall();

/* testNamespaceOperatorGlobalNamespaceStartOfStatementCombiWithNonConfusingToken1 */
namespace\CONSTANT === 'test' or die();

/* testNamespaceOperatorGlobalNamespaceStartOfStatementCombiWithNonConfusingToken2 */
namespace\ClassName::$property++;

/* testNamespaceOperatorGlobalNamespaceStartOfStatementCombiWithNonConfusingToken3 */
namespace\CONSTANT['key'];

/* testNamespaceOperatorGlobalNamespaceStartOfStatementCombiWithNonConfusingToken4 */
namespace\functionReturningObj()?->chained();


/* testParseErrorScopedNamespaceDeclaration */
function testScope() {
    namespace My\Namespace;
}

/* testParseErrorConditionalNamespace */
if ( $a === $b ) {
    namespace MyName\space;
}

/* testFatalErrorDeclarationLeadingSlash */
namespace \MyNamespace;

/* testParseErrorDoubleColon */
$a = namespace::Something();

/* testParseErrorSemiColon */
namespace;

// Intentional parse error. This has to be the last test in the file.
/* testLiveCoding */
namespace
