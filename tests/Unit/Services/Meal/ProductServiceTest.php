<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Meal;

use App\Http\DTO\Meal\Product\FormattedProductFeaturesDTO;
use App\Http\DTO\Meal\Product\ProductFeaturesDTO;
use App\Http\Services\Meal\ProductService;
use App\Models\Meal;
use App\Models\MealProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Metadata\CoversClass;

#[CoversClass(ProductService::class)]
class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productService = new ProductService();
    }

    /**
     * Test getFormattedProductData method without weight_for_features.
     */
    public function testGetFormattedProductDataWithoutWeightForFeatures(): void
    {
        // Arrange
        $request = new Request([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10.99,
            'weight' => 200,
            'is_public' => true,
            'is_alcohol' => false,
        ]);
        Auth::shouldReceive('id')->once()->andReturn(1);

        // Act
        $result = $this->productService->getFormattedProductData($request);

        // Assert
        $this->assertSame(1, $result['user_id']);
        $this->assertSame('Test Product', $result['name']);
        $this->assertSame('Test Description', $result['description']);
        $this->assertSame(10.99, $result['price']);
        $this->assertSame(200, $result['weight']);
        $this->assertArrayHasKey('weight_for_features', $result);
        $this->assertNull($result['weight_for_features']);
        $this->assertTrue($result['is_public']);
        $this->assertFalse($result['is_alcohol']);
        $this->assertArrayNotHasKey('calories', $result);
        $this->assertArrayNotHasKey('proteins', $result);
        $this->assertArrayNotHasKey('carbs', $result);
        $this->assertArrayNotHasKey('fats', $result);
    }

    /**
     * Test getFormattedProductData method with weight_for_features.
     */
    public function testGetFormattedProductDataWithWeightForFeatures(): void
    {
        // Arrange
        $request = new Request([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10.99,
            'weight' => 200,
            'weight_for_features' => 100,
            'is_public' => true,
            'is_alcohol' => false,
            'calories' => 500.0,
            'proteins' => 20.0,
            'carbs' => 60.0,
            'fats' => 30.0,
        ]);
        Auth::shouldReceive('id')->once()->andReturn(1);

        // Act
        $result = $this->productService->getFormattedProductData($request);

        // Assert
        $this->assertSame(1, $result['user_id']);
        $this->assertSame('Test Product', $result['name']);
        $this->assertSame('Test Description', $result['description']);
        $this->assertSame(10.99, $result['price']);
        $this->assertSame(200, $result['weight']);
        $this->assertSame(100, $result['weight_for_features']);
        $this->assertTrue($result['is_public']);
        $this->assertFalse($result['is_alcohol']);
        $this->assertSame(1000.0, $result['calories']);
        $this->assertSame(40.0, $result['proteins']);
        $this->assertSame(120.0, $result['carbs']);
        $this->assertSame(60.0, $result['fats']);
    }

    /**
     * Test addProductOrIncreaseCountIntoMeal method for a new product.
     */
    public function testAddProductOrIncreaseCountIntoMealNewProduct(): void
    {
        // Arrange
        $meal = Meal::factory()->create();
        $product = Product::factory()->create(['id' => 5]);
        $weight = 150.0;

        // Act
        $this->productService->addProductOrIncreaseCountIntoMeal($product->id, $weight, $meal);

        // Assert
        $this->assertDatabaseHas('meal_product', [
            'product_id' => $product->id,
            'meal_id' => $meal->id,
            'weight_product' => $weight,
            'count' => 1,
        ]);
    }

    /**
     * Test addProductOrIncreaseCountIntoMeal method for an existing product.
     */
    public function testAddProductOrIncreaseCountIntoMealExistingProduct(): void
    {
        // Arrange
        $product = Product::factory()->create(['id' => 10]);
        $meal = Meal::factory()->hasAttached(
            $product,
            ['weight_product' => 200.0, 'count' => 1]
        )->create();
        $productId = 10;
        $weight = 200.0;

        // Act
        $this->productService->addProductOrIncreaseCountIntoMeal($productId, $weight, $meal);

        // Assert
        $this->assertDatabaseHas('meal_product', [
            'product_id' => $productId,
            'meal_id' => $meal->id,
            'weight_product' => $weight,
            'count' => 2,
        ]);
    }

    /**
     * Test increaseCountProduct method.
     */
    public function testIncreaseCountProduct(): void
    {
        // Arrange
        $meal = Meal::factory()->create();
        $product = Product::factory()->create(['id' => 1]);
        $meal->products()->attach($product, ['weight_product' => 100.0, 'count' => 1]);
        $attachedProduct = $meal->products->first();

        // Act
        $this->productService->increaseCountProduct($attachedProduct);

        // Assert
        $this->assertDatabaseHas('meal_product', [
            'product_id' => $product->id,
            'meal_id' => $meal->id,
            'count' => 2,
        ]);
    }

    /**
     * Test decreaseCountProduct method.
     */
    public function testDecreaseCountProduct(): void
    {
        // Arrange
        $meal = Meal::factory()->create();
        $product = Product::factory()->create(['id' => 2]);
        $meal->products()->attach($product, ['weight_product' => 100.0, 'count' => 2]);
        $attachedProduct = $meal->products->first();

        // Act
        $this->productService->decreaseCountProduct($attachedProduct);

        // Assert
        $this->assertDatabaseHas('meal_product', [
            'product_id' => $product->id,
            'meal_id' => $meal->id,
            'count' => 1,
        ]);
    }

    /**
     * Test formatFeatures method with default weight.
     */
    public function testFormatFeaturesWithDefaultWeight(): void
    {
        // Arrange
        $productFeaturesDTO = new ProductFeaturesDTO();
        $productFeaturesDTO->weight_for_features = 50;
        $productFeaturesDTO->calories = 250.0;
        $productFeaturesDTO->proteins = 10.0;
        $productFeaturesDTO->carbs = 30.0;
        $productFeaturesDTO->fats = 15.0;

        // Act
        $formattedFeaturesDTO = $this->productService->formatFeatures($productFeaturesDTO);

        // Assert
        $this->assertInstanceOf(FormattedProductFeaturesDTO::class, $formattedFeaturesDTO);
        $this->assertSame(500.0, $formattedFeaturesDTO->calories);
        $this->assertSame(20.0, $formattedFeaturesDTO->proteins);
        $this->assertSame(60.0, $formattedFeaturesDTO->carbs);
        $this->assertSame(30.0, $formattedFeaturesDTO->fats);
    }

    /**
     * Test formatFeatures method with a custom weight.
     */
    public function testFormatFeaturesWithCustomWeight(): void
    {
        // Arrange
        $productFeaturesDTO = new ProductFeaturesDTO();
        $productFeaturesDTO->weight = 300;
        $productFeaturesDTO->weight_for_features = 100;
        $productFeaturesDTO->calories = 100.0;
        $productFeaturesDTO->proteins = 5.0;
        $productFeaturesDTO->carbs = 15.0;
        $productFeaturesDTO->fats = 7.0;

        // Act
        $formattedFeaturesDTO = $this->productService->formatFeatures($productFeaturesDTO);

        // Assert
        $this->assertInstanceOf(FormattedProductFeaturesDTO::class, $formattedFeaturesDTO);
        $this->assertSame(300.0, $formattedFeaturesDTO->calories);
        $this->assertSame(15.0, $formattedFeaturesDTO->proteins);
        $this->assertSame(45.0, $formattedFeaturesDTO->carbs);
        $this->assertSame(21.0, $formattedFeaturesDTO->fats);
    }

    /**
     * Test formatFeatures method with null weight.
     */
    public function testFormatFeaturesWithNullWeight(): void
    {
        // Arrange
        $productFeaturesDTO = new ProductFeaturesDTO();
        $productFeaturesDTO->weight = null;
        $productFeaturesDTO->weight_for_features = 50;
        $productFeaturesDTO->calories = 250.0;
        $productFeaturesDTO->proteins = 10.0;
        $productFeaturesDTO->carbs = 30.0;
        $productFeaturesDTO->fats = 15.0;

        // Act
        $formattedFeaturesDTO = $this->productService->formatFeatures($productFeaturesDTO);

        // Assert
        $this->assertInstanceOf(FormattedProductFeaturesDTO::class, $formattedFeaturesDTO);
        $this->assertSame(500.0, $formattedFeaturesDTO->calories);
        $this->assertSame(20.0, $formattedFeaturesDTO->proteins);
        $this->assertSame(60.0, $formattedFeaturesDTO->carbs);
        $this->assertSame(30.0, $formattedFeaturesDTO->fats);
    }
}
