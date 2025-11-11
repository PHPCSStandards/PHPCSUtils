<?php

/* testNotAnAttributeOpener */
echo 'foo';

/* testEmptyAttributeBlock */
#[]

/* testSingleAttributeNoParens */
#[SingleAttributeNoParens]

/* testSingleAttributeNamespaceRelativeNoParens */
#[namespace\Relative]

/* testSingleAttributePartiallyQualifiedNoParensTrailingComma */
#[Partially\Qualified\Name,]

/* testSingleAttributeFullyQualifiedNoParensSpacey */
#[  \Fully\Qualified\Name  ]

/* testSingleAttributeParensNoParams */
#[SingleAttributeParensNoParams()]

/* testSingleAttributeParensNoParamsSpaceyWithComment */
#[ SingleAttributeParensNoParamsSpacey ( /*comment*/ ) /*comment*/ ]

/* testSingleAttributeParensWithParamsTrailingComma */
#[SingleAttributeParensWithParams(1, [1, 2, 3], SOME_CONSTANT,),]

/* testSingleAttributeParensWithParamsMultiLine */
#[
    SingleAttributeParensWithParamsMultiLine(
        1,
        [1, 2, 3],
        SOME_CONSTANT * 5,
        new Obj,
    )
]

/* testSingleAttributeWithNamedParams */
#[SingleAttributeWithNamedParams(
    prop: 'val',
    other: 5,
)]

/* testMultipleAttributesSingleLine */
#[FirstAttribute(), namespace\SecondAttribute, \ThirdAttribute(1, 2), Partially\FourthAttribute('foo')]

/* testMultipleAttributesSingleLineTrailingCommaSpacey */
#[   FirstAttribute()  ,   \Fully\Qualified\SecondAttribute   ,   ]

/* testMultipleAttributesMultiLineWithComments */
#[
    \FirstAttribute(),
    // Reason for having this attribute.
    Partially\SecondAttribute,
    /**
     * Docblock, because, why not ?
     */
    namespace\ThirdAttribute(1, (2 + 3)),
    /* Another comment. */
    FourthAttribute('foo')
]

function hasLotsOfAttributes() {}
