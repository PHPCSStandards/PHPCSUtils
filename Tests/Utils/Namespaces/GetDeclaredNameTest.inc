<?php

/* testGlobalNamespaceCurlies */
namespace {}

/* testNamespaceSemiColon */
namespace Vendor;

/* testNamespaceCurlies */
namespace Vendor\Package\Sublevel\End {}

/* testNamespaceCurliesNoSpaceAtEnd */
namespace Vendor\Package\Sublevel\Deeperlevel\End{}

/* testNamespaceCloseTag */
namespace My\Name ?>
<?php

/* testNamespaceCloseTagNoSpaceAtEnd */
namespace My\Other\Name?>
<?php

/* testNamespaceLotsOfWhitespace */
namespace          Vendor \
    Package\
        Sub		\
            Deeperlevel\
                End             ;

/* testNamespaceWithCommentsWhitespaceAndAnnotations */
namespace /*comment*/ Vendor\/*comment*/
    Package\Sublevel  \ //phpcs:ignore Standard.Category.Sniff -- for reasons.
            Deeper\ // Another comment
                End             ;

/* testNamespaceOperator */
$a = namespace\functionName();

/* testParseErrorReservedKeywords */
// Intentional parse error. Reserved word used in namespace, but not our concern.
namespace Vendor\while\Package\protected\name\try\this ;

/* testParseErrorSemiColon */
// Intentional parse error.
namespace;

// Intentional parse error. This has to be the last test in the file.
/* testLiveCoding */
namespace Vendor\
