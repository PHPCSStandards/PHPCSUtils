<?php

$value = [
    /* testPrecededByArrowFunctionInArray - Expected */
    Url::make('View Song', fn($song) => $song->url())
        /* testPrecededByArrowFunctionInArray */
        ->onlyOnDetail(),

    new Panel('Information', [
        Text::make('Title')
    ]),
];

switch ($foo) {
    /* testCaseStatement */
    case 1:
        /* testInsideCaseStatement */
        $var = doSomething();
        /* testInsideCaseBreakStatement */
        break 2;

    case 2:
        /* testInsideCaseContinueStatement */
        continue 2;

    case 3:
        /* testInsideCaseReturnStatement */
        return false;

    case 4:
        /* testInsideCaseExitStatement */
        exit(1);

    case 5:
        /* testInsideCaseThrowStatement */
        throw new Exception();

    /* testDefaultStatement */
    default:
        /* testInsideDefaultContinueStatement */
        continue $var;
}
