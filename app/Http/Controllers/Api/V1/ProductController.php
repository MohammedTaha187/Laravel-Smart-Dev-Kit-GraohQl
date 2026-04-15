<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Contracts\ProductServiceInterface;
use App\Contracts\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    protected $productService;
    protected $productRepository;

    public function __construct(ProductServiceInterface $productService, ProductRepositoryInterface $productRepository)
    {
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(ProductResource::collection(Product::all()));
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $data = $this->productService->create($request->validated());
        return $this->successResponse(new ProductResource($data), 'Product created.', 201);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->successResponse(new ProductResource($product));
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        // update logic
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return $this->successResponse(null, 'Product deleted.');
    }
}
