<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Doubles\TestCase;

use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\Prophecy\ObjectProphecy;
use Zalas\PHPUnit\Doubles\Extractor\Property;
use Zalas\PHPUnit\Doubles\PhpDocumentor\ReflectionExtractor;

trait TestDoubles
{
    /**
     * @before
     */
    protected function initialiseTestDoubles(): void
    {
        foreach ($this->getTestDoubleProperties() as $property) {
            $this->{$property->getName()} = $this->createTestDouble($property);
        }
    }

    private function createTestDouble(Property $property)
    {
        if ($property->hasType(ObjectProphecy::class)) {
            return $this->createTestDoubleWithProphecy($property->getTypesFiltered(function (string $type) {
                return ObjectProphecy::class !== $type;
            }));
        }
        if ($property->hasType(MockObject::class)) {
            return $this->createTestDoubleWithPhpunit($property->getTypesFiltered(function (string $type) {
                return MockObject::class !== $type;
            }));
        }
    }

    private function createTestDoubleWithProphecy(array $types): ObjectProphecy
    {
        $prophecy = $this->prophesize(\array_shift($types));

        foreach ($types as $type) {
            if (\interface_exists($type)) {
                $prophecy->willImplement($type);
            } else {
                $prophecy->willExtend($type);
            }
        }

        return $prophecy;
    }

    private function createTestDoubleWithPhpunit(array $types): MockObject
    {
        return $this->getMockBuilder(1 === \count($types) ? \array_pop($types) : $types)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disableProxyingToOriginalMethods()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }

    private function getTestDoubleProperties(): array
    {
        return (new ReflectionExtractor())->extract($this, function (Property $property) {
            return $property->hasType(ObjectProphecy::class) || $property->hasType(MockObject::class);
        });
    }
}