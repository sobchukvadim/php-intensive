<?php

declare(strict_types=1);

/**
 * Value Object is a small object that represents a simple entity whose equality is not based on identity.
 * ID - Value objects don't have identifiers.
 * Mutable - Value objects are also immutable and are side effect free.
 * Logic - Value objects have some logic and behaviors including the validation/
 * @see https://youtu.be/agIL1EUozhQ
 */
class PackageDimension
{
    public function __construct(public readonly int $width, public readonly int $height, public readonly int $length)
    {
        match (true) {
            $this->width <= 0 || $this->width > 80 => throw new \InvalidArgumentException('Invalid package width'),
            $this->height <= 0 || $this->height > 70 => throw new \InvalidArgumentException('Invalid package height'),
            $this->length <= 0 || $this->length > 120 => throw new \InvalidArgumentException('Invalid package length'),
            default => true,
        };
    }

    /**
     * Being immutable, Value Objects cannot be changed once they are created.
     * Modifying one is conceptually the same as discarding the old one and creating a new one.
     * Frequently, the Value Object can define helper methods (or extensions methods) that assist with such operations.
     * The built-in string object in the .NET framework is a good example of an immutable type.
     * Converting a string in some manner, such as making it uppercase via ToUpper(),
     * doesn't actually change the original string but rather creates a new string.
     * Likewise, concatenating two strings doesn't modify either original string, but rather creates a third one.
     *
     * @param int $width
     * @return $this
     */
    public function increaseWidth(int $width): self
    {
        return new self($this->width + $width, $this->height, $this->length);
    }

    /**
     * == loose comparison & for objects it compares property values.
     * === strict comparison & for objects it compares object identity (reference)
     * Because Value Objects lack identity, they can be compared on the basis of their collective state.
     * If all of their component properties are equal to one another,
     * then two Value Objects can be said to be equal. Again, this is the same as with string types.
     *
     * @param PackageDimension $packageDimension
     * @return bool
     */
    public function equalTo(PackageDimension $packageDimension): bool
    {
        return $this->width === $packageDimension->width
            && $this->height === $packageDimension->height
            && $this->length === $packageDimension->length;
    }
}

class Weight
{
    public function __construct(public readonly int $value)
    {
        if ($this->value <= 0 || $this->value > 150) {
            throw new \InvalidArgumentException('Invalid package weight');
        }
    }

    public function equalTo(Weight $weight): bool
    {
        return $this->value === $weight->value;
    }
}

enum DimDivisor: int
{
    case FEDEX = 139;
}

class BillableWeightCalculatorService
{
    public function calculate(
        PackageDimension $packageDimension,
        Weight $weight,
        DimDivisor $dimDivisor
    ): int {
        $dimWeight = (int) round(
            $packageDimension->width * $packageDimension->height * $packageDimension->length / $dimDivisor->value
        );

        return max($weight->value, $dimWeight);
    }
}

$package = [
    'weight' => 6,
    'dimensions' => [
        'width' => 9,
        'length' => 15,
        'height' => 7,
    ],
];

$packageDimension = new PackageDimension(
    $package['dimensions']['width'],
    $package['dimensions']['height'],
    $package['dimensions']['length'],
);

$billableWeight = (new BillableWeightCalculatorService())->calculate(
    $packageDimension,
    new Weight($package['weight']),
    DimDivisor::FEDEX
);

echo $billableWeight . ' lb' . PHP_EOL;