<?php

/* testUnacceptableToken */
function globalFunction() {}

/* testEmptyClass */
class EmptyClass extends TestCase {}

/* testEmptyInterface */
interface EmptyInterface extends AnotherInterface {}

/* testEmptyTrait */
trait EmptyTrait {}

/* testEmptyEnum */
enum EmptyEnum {}

/* testEmptyAnonClass */
$anon = new class implements SomeInterface {};

/* testClass */
abstract class testClass {

    use someTrait;
    // Test skipping over contents between {}.
    use A, B {
        B::smallTalk insteadof A;
        A::function insteadof B;
        B::bigTalk as function;
    }

    // Ensuring the utility method does not get confused over keywords being used as a constant name.

    /* markerClassConst1 */
    public const CONST = 'name';

    /* markerClassConst2 */
    const CASE = 'name';

    /* markerClassConst3 */
    final protected const FINAL = 'name';

    /* markerClassConst4 */
    const FUNCTION = 'name';

    /* markerClassConst5 */
    final protected const int|string UNION_TYPED = 'name';

    /* markerClassConst6 */
    private const (A&B)|null DNF_TYPED = null;


    /* markerClassProperty1 */
    public readonly string $prop = self::FUNCTION;

    /**
     * Docblock.
     */
    /* markerClassMethod1 */
    public function testDocblockSkipping() {
        // Do something.
    }

    #[TestAttributeUsingFunctionKeyword(self::FUNCTION)]
    #[
        MultilineAttribute,
        SecondAttribute
    ]
    #[ReturnTypeWillChange]
    /* markerClassMethod2 */
    public function testAttributesSkipping() {
        // Do something.
    }

    #[MyAttribute(self::FUNCTION)]
    #[
        MultilineAttribute,
        SecondAttribute
    ]
    /**
     * Docblock.
     */
    #[ReturnTypeWillChange]
    /* markerClassMethod3 */
    public function testDocblockAndAttributeSkipping() {
        // Do something.
    }

    /* markerClassMethod4 */
    private function testFunctionKeywordUsedInParams($a = self::FUNCTION) {}

    // Silly, but "legal" as of PHP 7.0.
    /* markerClassMethod5A */
    protected function function() {}

    /* markerClassMethod5B */
    protected function const() {}

    /* markerClassMethod5C */
    protected function case() {}

    /* markerClassMethod6 */
    protected static function skipOverContentsOfFunctionA () {
        if (function_exists('shouldNotBeListed') === false) {
            function shouldNotBeListed() {
                // Do something.
                function shouldAlsoNotBeListed($forReal) {}
            }
        }
    }

    /* markerClassMethod7 */
    protected static function skipOverContentsOfFunctionB () {
        // These functions should be listed for the anon class, but not for the wrapping class.
        /* testAnonClass */
        return new class() {
            /**
             * Kitchen sink to collect dynamic properties.
             *
             * @var array
             */
            /* markerAnonNestedProperty1 */
            private $sink = [];

            #[ReturnTypeWillChange]
            #[Multi, Attribute, AndMore([1,2,3])]
            /* markerAnonNestedMethod1 */
            public function __isset($name) {}
            /* markerAnonNestedMethod2 */
            public function __get($name) {}
            /* markerAnonNestedMethod3 */
            public function __set($name, $value) {}
            /* markerAnonNestedMethod4 */
            public function __unset($name) {}

            /**
             * Docblock.
             */
            /* markerAnonNestedMethod5 */
            private function doSomething() {}
        };
    }

    /* markerClassMethod8 */
    protected static function skipOverContentsOfFunctionC () {
        $closure = function ($ignoreThis) {};
    }

    /* markerClassMethod9 */
    protected function __construct( /* markerClassProperty2 */ public $property, /* markerClassProperty3 */ private $promotion) {}

    /* markerClassMethod10 */
    private function PrivateFunction() {}

    /* markerClassMethod11 */
    final public function FinalPublic() {
        // Intentional parse error. Illegal const declaration using const keyword without function.
        const Foo = 10;
    }

    /* markerClassMethod12 */
    abstract function implementMe();
}

/* testInterface */
interface testInterface {
    /* markerInterfaceConst1 */
    const IM = 'I am';
    /* markerInterfaceConst2 */
    final public const ?string WALKING = 'walking';
    /* markerInterfaceConst3 */
    public const int|false ON = false;
    /* markerInterfaceConst4 */
    public const (D&\N)|namespace\F SUNSHINE = Sunshine;

    /* markerInterfaceIllegalProperty */
    // Intentional parse error. Properties are not allowed in interfaces (until PHP 8.4), but that's not the concern of this method.
    public $invalid;

    public /* markerInterfaceMethod1 */ function oh_oh();
    public /* markerInterfaceMethod2 */ function im();
    public /* markerInterfaceMethod3 */ FUNCTION walking();
    public /* markerInterfaceMethod4 */ Function on();
    public /* markerInterfaceMethod5 */ function sunshine();
}

/* testTrait */
trait testTrait {
    /* markerTraitConst1 */
    const DO = 10;
    /* markerTraitConst2 */
    public const /*comment*/ ?array RE = [];
    /* markerTraitConst3 */
    final const
            mi = 'soup';

    /* markerTraitProperty1 */
    public $fa;
    /* markerTraitProperty2 */
    readonly
    string
    $sol;
    /* markerTraitProperty3 */
    var
    // phpcs:ignore Stnd.Cat.Something
    $LA = 'string';

    /* markerTraitMethod1 */
    public function ti() {}
    /* markerTraitMethod2 */
    protected static function DO() {}
    /* markerTraitMethod3 */
    abstract public function doReMiFa();
    /* markerTraitMethod4 */
    function solLaTiDo() {}
}

/* testAnonClassUnconventionalOrder */
$anon = new class() {
    /* markerAnonOrderMethod1 */
    function hereIGoAgain( $param ) {
        return 'on my own';
    }

    /* markerAnonOrderProperty1 */
    public ?string $goingDownTheOnlyRoad = "I've ever known";

    /* markerAnonOrderMethod2 */
    private static function LikeADrifter(int $I, string $Was, (\born&To)|namespace\Walk $alone) {}

    /* markerAnonOrderConst1 */
    protected const iterable AndIveMadeUp = ['MyMind'];

    /* markerAnonOrderMethod3 */
    final protected function Iaint($wasting) : NoMore {
        return new Time;
    }

    /* markerAnonOrderProperty2 */
    readonly (\Im&Just)|namespace\Another\Heart $inNeedOfRescue;

    /* markerAnonOrderConst2 */
    final const WAITING_ON_LOVES = 'sweet charity';

    /* markerAnonOrderMethod4 */
    public function /*comment*/ AndImGonnaHoldOn( $for, $the, Rest $ofMyDays) {}
};

/* testEnum */
enum testEnum: string implements ArrayAccess {
    /* markerEnumConst1 */
    final const SUIT = 'Something';

    /* markerEnumConst2 */
    public const ANOTHER = 10;

    /* markerEnumCase1 */
    case Hearts = 'H';
    /* markerEnumCase2 */
    case
          Diamonds = 'D';
    /* markerEnumCase3 */
    case /*comment*/ Clubs = 'C';
    /* markerEnumCase4 */
    case Spades = 'S';

    /* markerEnumIllegalProperty */
    // Intentional parse error. Properties are not allowed in enums, but that's not the concern of this method.
    public $invalid;

    /* markerEnumMethod1 */
    public function color(): string
    {
        return match($this) {
            Suit::Hearts, Suit::Diamonds => 'Red',
            Suit::Clubs, Suit::Spades => 'Black',
        };
    }

    /* markerEnumMethod2 */
    public function offsetGet($val) : mixed {
        // Do something.
    }

    /* markerEnumMethod3 */
    public function offsetExists($val): bool {
        // Do something.
    }

    /* markerEnumMethod4 */
    public function offsetSet($offset, $val): void {
        throw new Exception();
    }

    /* markerEnumMethod5 */
    public function offsetUnset($val): void {
        throw new Exception();
    }
}

/* testClassConstructorNoParams */
class ConstructorNoParams {
    /* markerCPP1_Constructor */
    public function __construct() {}
}

/* testInterfaceConstructorWithParamsNotProperties */
interface ConstructorWithParamsNotProperties {
    /* markerCPP2_Constructor */
    public function __Construct(int $paramA, Union|null $paramB, false|(D&N&F) $paramC = false);
}

/* testClassConstructorWithProperties */
class ConstructorWithProperties {
    /* markerCPP3_Constructor */
    public function __CONSTRUCT(
        /* markerCPP3_Property1 */
        private int $propA,
        /* markerCPP3_Property2 */
        protected readonly string $propB,
        /* markerCPP3_Property3 */
        readonly Onion|Union $propC,
        /* markerCPP3_Property4 */
        public (D&N)|F $propD,
    ) {}
}

/* testTraitConstructorWithParamsAndProperties */
trait ConstructorWithParamsAndProperties {
    /* markerCPP4_Property1 */
    public int $declared;

    /* markerCPP4_Property2 */
    private self $instance;

    /* markerCPP4_Constructor */
    public function __construct(
        /* markerCPP4_Property3 */
        public ?string $propA,
        /* markerCPP4_Property4 */
        protected \MyClass $propB,
        /* markerCPP4_Property5 */
        private namespace\Relative|Partially\Qualified $propC,
        ?int $paramA,
        string $paramB,
    ) {}
}

/* testMultiPropertyDeclarations */
class testMultiProperty {
    /* markerClassMultiProperty1 */
    public private(set) int $propA = 5,
        /* markerClassMultiProperty2 */
        $propB = 10,
        /* markerClassMultiProperty3 */
        $propC,
        /* markerClassMultiProperty4 */
        $propD,
        /* markerClassMultiProperty5 */
        $propE = 10;
}
