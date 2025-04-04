<?php

declare(strict_types=1);

namespace Tests\Feature\API\V1;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Services\Meal\Contracts\ProductServiceInterface;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function testIndex(): void
    {
        Product::factory()->count(3)->create(['user_id' => $this->user->id]);
        Product::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function testSearch(): void
    {
        Product::factory()->create(['name' => 'Apple Pie', 'description' => 'Delicious apple pie']);
        Product::factory()->create(['name' => 'Banana Bread', 'description' => 'Homemade banana bread']);
        Product::factory()->create(['name' => 'Orange Juice', 'description' => 'Freshly squeezed orange juice']);

        $response = $this->getJson('/api/v1/products/search/apple');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Apple Pie');

        $response = $this->getJson('/api/v1/products/search/bread');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Banana Bread');

        $response = $this->getJson('/api/v1/products/search/juice');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Orange Juice');
    }

    public function testCreate(): void
    {
        $payload = [
            'name' => 'New Product',
            'description' => 'Description of the new product',
            'calories' => 100,
            'proteins' => 10,
            'fats' => 5,
            'carbs' => 15,
            'weight_for_features' => 100,
        ];

        $mock = Mockery::mock(ProductServiceInterface::class);
        $this->app->instance(ProductServiceInterface::class, $mock);

        $mock->shouldReceive('getFormattedProductData')
            ->once()
            ->with(\Mockery::on(fn($request): bool => $request->all() === $payload))
            ->andReturn($payload + ['user_id' => $this->user->id]);

        $response = $this->postJson('/api/v1/products/create', $payload);

        $response->assertStatus(200)
            ->assertJson($payload);

        $this->assertDatabaseHas('products', $payload + ['user_id' => $this->user->id]);
    }

    public function testUpdate(): void
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        $payload = [
            'id' => $product->id,
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'calories' => 150,
            'proteins' => 15,
            'fats' => 8,
            'carbs' => 20,
            'weight_for_features' => 120,
        ];

        $mock = Mockery::mock(ProductServiceInterface::class);
        $this->app->instance(ProductServiceInterface::class, $mock);

        $mock->shouldReceive('getFormattedProductData')
            ->once()
            ->with(\Mockery::on(fn($request): bool => $request->all() === $payload))
            ->andReturn($payload + ['user_id' => $this->user->id]);

        $response = $this->postJson("/api/v1/products/update", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Product',
                'description' => 'Updated description',
                'calories' => 150,
                'proteins' => 15,
                'fats' => 8,
                'carbs' => 20,
                'weight_for_features' => 120,
            ]);

        $this->assertDatabaseHas('products', array_merge($payload, ['user_id' => $this->user->id]));
    }

    public function testDelete(): void
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson("/api/v1/products/delete", ['id' => $product->id]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function testShow(): void
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/products/show/{$product->id}");

        $response->assertStatus(200)
            ->assertJson($product->toArray());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
