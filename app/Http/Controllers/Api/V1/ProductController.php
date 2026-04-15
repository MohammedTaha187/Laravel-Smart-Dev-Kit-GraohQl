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
    protected $ProductService;
    protected $ProductRepository;

    public function __construct(ProductServiceInterface $ProductService, ProductRepositoryInterface $ProductRepository)
    {
        $this->ProductService = $ProductService;
        $this->ProductRepository = $ProductRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(ProductResource::collection(Product::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $data = $this->ProductService->create($request->validated());
        return $this->successResponse(new ProductResource($data), 'Product created.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        // Logic
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        // Logic
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        // Logic
    }
}
