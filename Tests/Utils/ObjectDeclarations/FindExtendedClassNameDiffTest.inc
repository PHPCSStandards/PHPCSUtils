<?php

/* testDeclarationWithComments */
class testDeclarationWithComments
    extends
    // phpcs:ignore Stnd.Cat.Sniff -- For reasons.
    \Package\SubDir /* comment */ \ /* comment */ SomeClass /* comment */ {}

/* testExtendedClassUsingNamespaceOperator */
class testWithNSOperator extends namespace\Bar {}

/* testExtendedClassStrayComma */
class testExtendedClassStrayComma extends , testClass {} // Intentional parse error.
