/* testNonUnaryPlus */
result = 1 + 2;

/* testNonUnaryMinus */
result = 1-2;

/* testUnaryMinusColon */
$.localScroll({offset: {top: -32}});

switch (result) {
	/* testUnaryMinusCase */
	case -1:
		break;
}

/* testUnaryMinusTernaryThen */
result = x?-y:z;

/* testUnaryPlusTernaryElse */
result = x ? y : +z;

/* testUnaryMinusIfCondition */
if (true || -1 == b) {}
