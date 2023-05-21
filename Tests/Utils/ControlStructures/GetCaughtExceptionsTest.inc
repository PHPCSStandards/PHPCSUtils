<?php

/* testNotCatch */
try {

/* testSingleCatchNameOnly */
} catch (RuntimeException $e) {

/* testSingleCatchNameLeadingBackslash */
} catch (\RuntimeException $e) {

/* testSingleCatchPartiallyQualified */
} catch ( MyNS\RuntimeException $e) {

/* testSingleCatchFullyQualified */
} catch ( \MyNS\RuntimeException $e) {

/* testSingleCatchPartiallyQualifiedWithCommentAndWhitespace */
} catch ( My\NS \ Sub\ /* comment */ RuntimeException $e) {

/* testSingleCatchNamespaceOperator */
} catch ( namespace\RuntimeException $e) {

/* testMultiCatchSingleNames */
} catch (RuntimeException | ParseErrorException | AnotherException $e) {

/* testMultiCatchCompoundNames */
} catch (\NS\RuntimeException | My\ParseErrorException | namespace \ AnotherException $e) {

/* testPHP8NonCapturingCatch */
} catch (RuntimeException | AnotherException) {

/* testMissingExceptionName */
} catch ($e) {

/* testMultiMissingExceptionNames */
} catch ( | $e) {

/* testLiveCoding */
// Intentional parse error.
} catch (
