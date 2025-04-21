<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find category and brand by name or create default
        $category   = Category::where('name', $row['category'])->first() ?? Category::first();
        $brand      = Brand::where('name', $row['brand'])->first() ?? Brand::first();

        // Generate slug
        $slug           = Str::slug($row['name']);
        $count          = 1;
        $originalSlug   = $slug;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Generate SKU if not provided
        $sku = isset($row['sku']) && !empty($row['sku'])
            ? $row['sku']
            : strtoupper(substr(str_replace(['-', ' '], '', $slug), 0, 8) . '-' . uniqid());

        // Create product
        $product = Product::create([
            'name'          => $row['name'],
            'slug'          => $slug,
            'sku'           => $sku,
            'category_id'   => $category->id,
            'brand_id'      => $brand->id,
            'price'         => $row['price'],
            'sale_price'    => isset($row['sale_price']) && $row['sale_price'] > 0 ? $row['sale_price'] : null,
            'stock'         => $row['stock'] ?? 0,
            'description'   => $row['description'] ?? null,
            'status'        => strtolower($row['status'] ?? '') === 'published' || ($row['status'] ?? 0) == 1,
            'featured'      => strtolower($row['featured'] ?? '') === 'yes' || ($row['featured'] ?? 0) == 1,
        ]);

        // Create default image
        ProductImage::create([
            'product_id'    => $product->id,
            'image'         => 'products/default.jpg',
            'is_primary'    => true,
        ]);

        return $product;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
        ];
    }
}
