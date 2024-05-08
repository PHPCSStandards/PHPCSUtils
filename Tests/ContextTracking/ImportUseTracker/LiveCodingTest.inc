<?php

/* testStartOfFile */

/* testNamespaceDeclaration */
namespace Vendor\Package\Name;

/* testSingleImportUse */
use function implode;

/* testGroupImportUse */
// Intentional parse error. Missing end of statement (close group use + semicolon/PHP close tag).
use Unfinished\{
    Statement,
    Missing\Closer,

