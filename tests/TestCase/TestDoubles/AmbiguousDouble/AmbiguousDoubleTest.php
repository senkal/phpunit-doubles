<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Doubles\Tests\TestCase\TestDoubles\AmbiguousDouble;

use PHPUnit\Framework\TestCase;

class AmbiguousDoubleTest extends TestCase
{
    public function test_it_throws_an_exception_if_test_double_framework_is_ambiguous()
    {
        $this->expectException(\LogicException::class);

        (new AmbiguousDoubleRunner())->callInitialiseTestDoubles();
    }
}
