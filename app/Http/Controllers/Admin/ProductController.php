<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products with optional search and type filters.
     */
    public function index(Request $request): View
    {
        $query = Product::query()->latest();

        if ($request->filled('type') && in_array($request->query('type'), ['ready_stock', 'pre_order'], true)) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(12)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $data['slug'] = $slug;

        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured', false);

        // Handle uploaded image file if present
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $data['image'] = '/storage/'.$path;
        }

        $product = Product::create($data);

        ActivityLogger::catalog(
            'product.created',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." menambahkan produk baru: {$product->name} (Rp ".number_format($product->price, 0, ',', '.').')',
            $product,
            ['name' => $product->name, 'price' => $product->price, 'stock' => $product->stock]
        );

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan ke katalog!");
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($data['name'] !== $product->name) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        // Handle uploaded image file if present
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $data['image'] = '/storage/'.$path;
        }

        $product->update($data);

        ActivityLogger::catalog(
            'product.updated',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." memperbarui produk: {$product->name}",
            $product,
            ['name' => $product->name, 'price' => $product->price, 'stock' => $product->stock]
        );

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$product->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();

        ActivityLogger::catalog(
            'product.deleted',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." menghapus produk: {$name}",
            null,
            ['name' => $name]
        );

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$name}' telah dihapus dari katalog.");
    }

    /**
     * Toggle the active visibility status of a product.
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        ActivityLogger::catalog(
            'product.status_toggled',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." mengubah status produk {$product->name} menjadi ".($product->is_active ? 'Aktif' : 'Nonaktif'),
            $product,
            ['is_active' => $product->is_active]
        );

        $stateText = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status produk '{$product->name}' berhasil {$stateText}.");
    }

    /**
     * Toggle the featured highlight status of a product.
     */
    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update([
            'is_featured' => ! $product->is_featured,
        ]);

        ActivityLogger::catalog(
            'product.featured_toggled',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." mengubah status unggulan {$product->name} menjadi ".($product->is_featured ? 'Unggulan' : 'Standar'),
            $product,
            ['is_featured' => $product->is_featured]
        );

        $stateText = $product->is_featured ? 'dijadikan produk unggulan' : 'dihapus dari produk unggulan';

        return back()->with('success', "Produk '{$product->name}' berhasil {$stateText}.");
    }
}
