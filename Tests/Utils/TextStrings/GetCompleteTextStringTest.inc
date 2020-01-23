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

/* testTextStringAtEndOfFile */
// This has to be the last test in the file without a new line after it.
echo 'first line
last line'