<?php

class ConstructorPropertyPromotionNoTypes {
    /* testPHP8ConstructorPropertyPromotionNoTypes */
    public function __construct(
        public $x = 0.0,
        protected $y = '',
        private $z = null,
    ) {}
}

class ConstructorPropertyPromotionWithTypes {
    /* testPHP8ConstructorPropertyPromotionWithTypes */
    public function __construct(protected float|int $x, public ?string &$y = 'test', private mixed $z) {}
}

class ConstructorPropertyPromotionAndNormalParams {
    /* testPHP8ConstructorPropertyPromotionAndNormalParam */
    public function __construct(public int $promotedProp, ?int $normalArg) {}
}

/* testPHP8ConstructorPropertyPromotionGlobalFunction */
// Intentional fatal error. Property promotion not allowed in non-constructor, but that's not the concern of this method.
function globalFunction(private $x) {}

abstract class ConstructorPropertyPromotionAbstractMethod {
    /* testPHP8ConstructorPropertyPromotionAbstractMethod */
    // Intentional fatal error.
    // 1. Property promotion not allowed in abstract method, but that's not the concern of this method.
    // 2. Variadic arguments not allowed in property promotion, but that's not the concern of this method.
    // 3. The callable type is not supported for properties, but that's not the concern of this method.
    abstract public function __construct(public callable $y, private ...$x);
}
