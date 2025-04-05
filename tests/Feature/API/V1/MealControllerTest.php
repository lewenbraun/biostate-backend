<?php

declare(strict_types=1);

namespace Tests\Feature\API\V1;

use Mockery;
use Tests\TestCase;
use App\Models\Meal;
use App\Models\User;
use App\Models\Product;
use App\Models\MealProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Services\Meal\Contracts\ProductServiceInterface;

class MealControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testCreateMeal(): void
    {
        $payload = [
            'date'       => '2025-03-24',
            'meal_order' => 1,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/v1/daily-meal/meal/create', $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'date'       => '2025-03-24T00:00:00.000000Z',
                     'meal_order' => 1,
                     'user_id'    => $this->user->id,
                 ]);

        $this->assertDatabaseHas('meals', [
            'date'       => '2025-03-24',
            'meal_order' => 1,
            'user_id'    => $this->user->id,
        ]);
    }

    public function testDeleteMeal(): void
    {
        $meal = Meal::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/v1/daily-meal/meal/delete', ['id' => $meal->id]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
    }

    public function testAddProductIntoMeal(): void
    {
        $meal = Meal::factory()->create([
            'date'       => '2025-03-24',
            'meal_order' => 1,
            'user_id'    => $this->user->id,
        ]);

        $product = Product::factory()->create();

        $mock = Mockery::mock(ProductServiceInterface::class);
        $this->app->instance(ProductServiceInterface::class, $mock);

        $mock->shouldReceive('addProductOrIncreaseCountIntoMeal')
             ->once()
             ->with($product->id, 200, \Mockery::on(fn($arg): bool => $arg->id === $meal->id));

        $payload = [
            'date'       => '2025-03-24',
            'meal_order' => 1,
            'product_id' => $product->id,
            'weight'     => 200,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/v1/daily-meal/product/add', $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Product added successfully.']);
    }

    public function testShowMeals(): void
    {
        Meal::factory()->create([
            'date'       => '2025-03-24',
            'meal_order' => 1,
            'user_id'    => $this->user->id,
        ]);

        Meal::factory()->create([
            'date'       => '2025-03-24',
            'meal_order' => 2,
            'user_id'    => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/v1/daily-meal?date=2025-03-24');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => ['*' => ['id', 'date', 'meal_order']],
                 ]);
    }

    public function testDeleteProduct(): void
    {
        $meal = Meal::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        $meal->products()->attach($product->id, ['count' => 2, 'weight_product' => 150]);

        $mealProduct = MealProduct::where('meal_id', $meal->id)
                                  ->where('product_id', $product->id)
                                  ->first();

        $payload = [
            'meal_id'       => $meal->id,
            'product_id'    => $product->id,
            'weight_product' => 150,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/v1/daily-meal/product/delete', $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Product deleted successfully']);

        $this->assertDatabaseMissing('meal_product', [
            'meal_id'    => $meal->id,
            'product_id' => $product->id,
        ]);
    }

    public function testIncreaseCountProduct(): void
    {
        $meal = Meal::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        $meal->products()->attach($product->id, ['count' => 1, 'weight_product' => 100]);

        $attachedProduct = $meal->products()->where('product_id', $product->id)->first();

        $mock = Mockery::mock(ProductServiceInterface::class);
        $this->app->instance(ProductServiceInterface::class, $mock);

        $mock->shouldReceive('increaseCountProduct')
             ->once()
             ->with(\Mockery::on(fn($arg): bool => $arg->id === $attachedProduct->id));

        $payload = [
            'meal_id'    => $meal->id,
            'product_id' => $product->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/v1/daily-meal/product/increase-count', $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Product count increased successfully.']);
    }

    public function testDecreaseCountProduct_Detach(): void
    {
        $meal = Meal::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        $meal->products()->attach($product->id, ['count' => 1, 'weight_product' => 100]);

        $payload = [
            'meal_id'    => $meal->id,
            'product_id' => $product->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/v1/daily-meal/product/decrease-count', $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Product count decreased successfully.']);

        $meal->load('products');
        $this->assertFalse($meal->products()->where('product_id', $product->id)->exists());
    }

    public function testDecreaseCountProduct_Decrease(): void
    {
        $meal = Meal::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        $meal->products()->attach($product->id, ['count' => 2, 'weight_product' => 100]);

        $attachedProduct = $meal->products()->where('product_id', $product->id)->first();

        $mock = Mockery::mock(ProductServiceInterface::class);
        $this->app->instance(ProductServiceInterface::class, $mock);

        $mock->shouldReceive('decreaseCountProduct')
             ->once()
             ->with(\Mockery::on(fn($arg): bool => $arg->id === $attachedProduct->id));

        $payload = [
            'meal_id'    => $meal->id,
            'product_id' => $product->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/v1/daily-meal/product/decrease-count', $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Product count decreased successfully.']);
    }

    public function testUpdateWeightProduct(): void
    {
        $meal = Meal::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        $meal->products()->attach($product->id, ['count' => 1, 'weight_product' => 150]);

        $mock = Mockery::mock(ProductServiceInterface::class);
        $this->app->instance(ProductServiceInterface::class, $mock);

        $payload = [
            'meal_id'        => $meal->id,
            'product_id'     => $product->id,
            'weight_product' => 150,
            'changed_weight' => 200,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/v1/daily-meal/product/update-weight', $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Product weight updated successfully.']);

        $this->assertDatabaseHas('meal_product', [
            'meal_id'        => $meal->id,
            'product_id'     => $product->id,
            'weight_product' => 200,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
