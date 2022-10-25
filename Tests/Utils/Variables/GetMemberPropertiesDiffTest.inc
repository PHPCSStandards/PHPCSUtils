<?php

interface Base
{
    /* testInterfaceProperty */
    protected $anonymous;
}

enum Suit
{
    /* testEnumProperty */
    protected $anonymous;
}

$anon = class() {
    /* testPHP82PseudoTypeTrue */
    public true $pseudoTypeTrue;

    /* testPHP82NullablePseudoTypeTrue */
    static protected ?true $pseudoTypeNullableTrue;

    /* testPHP82PseudoTypeTrueInUnion */
    private int|string|true $pseudoTypeTrueInUnion;

    /* testPHP82PseudoTypeFalseAndTrue */
    // Intentional fatal error - Type contains both true and false, bool should be used instead, but that's not the concern of the method.
    readonly true|FALSE $pseudoTypeFalseAndTrue;
};
