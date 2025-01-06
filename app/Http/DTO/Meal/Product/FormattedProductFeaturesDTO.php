<?php

namespace App\Http\DTO\Meal\Product;

class FormattedProductFeaturesDTO
{
    public $calories;
    public $proteins;
    public $carbs;
    public $fats;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->calories = $data['calories'];
        $dto->proteins = $data['proteins'];
        $dto->carbs = $data['carbs'];
        $dto->fats = $data['fats'];
        return $dto;
    }

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
