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
