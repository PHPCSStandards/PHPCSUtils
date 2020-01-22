<?php

/* testDeclarationWithComments */
class testDeclarationWithComments
    extends
    // phpcs:ignore Stnd.Cat.Sniff -- For reasons.
    \Package\SubDir /* comment */ \ /* comment */ SomeClass /* comment */ {}

/* testExtendedClassStrayComma */
class testExtendedClassStrayComma extends , testClass {} // Intentional parse error.
