<?php

/* testNotATextString */
return $something;

/* testNotFirstTextStringToken */
echo 'first line
second line
third line
fourth line';

/* testSingleLineConstantEncapsedString */
echo 'single line text string';

/* testMultiLineConstantEncapsedString */
echo "first line
second line
third line
fourth line";

/* testSingleLineDoubleQuotedString */
echo "single $line text string";

/* testMultiLineDoubleQuotedString */
echo "first line
second $line
third line
fourth line";

/* testHeredocString */
echo <<<EOD
first line
second $line
third line
fourth line
EOD;

/* testNowdocString */
echo <<<'EOD'
first line
second line
third line
fourth line
EOD;

/* testMultipleProblemEmbedsInSingleLineDoubleQuotedString */
echo "My ${foo["${bar}"]} and ${foo["${bar['baz']}"]} and also ${foo->{"${'a'}"}}";

/* testProblemEmbedAtEndOfLineInMultiLineDoubleQuotedString */
echo "Testing ${foo["${bar['baz']}"]}
and more ${foo["${bar}"]} testing";

/* testMultilineProblemEmbedInMultiLineDoubleQuotedString */
echo "Testing ${foo["${bar
  ['baz']
}"]} and more testing";

/* testTextStringAtEndOfFile */
// This has to be the last test in the file without a new line after it.
echo 'first line
last line'