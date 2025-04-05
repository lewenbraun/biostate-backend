<?php

declare(strict_types=1);

namespace App\Http\DTO\Meal\Product;

class FormattedProductFeaturesDTO
{
    public float $calories;
    public float $proteins;
    public float $carbs;
    public float $fats;

    /**
     * @param array<string, float> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->calories = $data['calories'];
        $dto->proteins = $data['proteins'];
        $dto->carbs = $data['carbs'];
        $dto->fats = $data['fats'];
        return $dto;
    }

    /**
     * @return array<string, float>
     */
    public function toArray(): array
    {
        return [
            'calories' => $this->calories,
            'proteins' => $this->proteins,
            'carbs' => $this->carbs,
            'fats' => $this->fats,
        ];
    }
}
