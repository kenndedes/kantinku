<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    private function standId(): ?int
    {
        return optional(Auth::user()->sellerProfile)->stand_id;
    }

    public function index(): View|RedirectResponse
    {
        $standId = $this->standId();

        if (! $standId) {
            return redirect()->route('seller.pending');
        }

        $menuItems = MenuItem::query()
            ->with('category')
            ->where('stand_id', $standId)
            ->orderBy('name')
            ->get();

        return view('seller.menu.index', compact('menuItems', 'standId'));
    }

    public function create(): View|RedirectResponse
    {
        $standId = $this->standId();

        if (! $standId) {
            return redirect()->route('seller.pending');
        }

        $categories = Category::orderBy('name')->get();

        return view('seller.menu.create', compact('standId', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $standId = $this->standId();

        if (! $standId) {
            return redirect()->route('seller.pending');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'numeric', 'min:1000', 'max:999999'],
            'stock'       => ['required', 'integer', 'min:0'],
            'photo'       => ['nullable', 'image', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('menu_photos', 'public');
        }

        MenuItem::create([
            ...$validated,
            'stand_id'    => $standId,
            'photo'       => $photoPath,
            'is_available' => $validated['is_available'] ?? true,
        ]);

        return redirect()->route('seller.menu.index')->with('status', 'Menu berhasil ditambahkan.');
    }

    public function edit(MenuItem $menu): View
    {
        $this->authorizeMenu($menu);

        $categories = Category::orderBy('name')->get();

        return view('seller.menu.edit', [
            'menuItem'   => $menu,
            'standId'    => $this->standId(),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MenuItem $menu): RedirectResponse
    {
        $this->authorizeMenu($menu);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'numeric', 'min:1000', 'max:999999'],
            'stock'       => ['required', 'integer', 'min:0'],
            'photo'       => ['nullable', 'image', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $photoPath = $menu->photo;
        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('menu_photos', 'public');
        }

        $menu->update([
            ...$validated,
            'photo' => $photoPath,
            'is_available' => $validated['is_available'] ?? true,
        ]);

        return redirect()->route('seller.menu.index')->with('status', 'Menu berhasil diperbarui.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        $this->authorizeMenu($menu);
        $menu->delete();

        return redirect()->route('seller.menu.index')->with('status', 'Menu berhasil dihapus.');
    }

    private function authorizeMenu(MenuItem $menu): void
    {
        if ($menu->stand_id !== $this->standId()) {
            abort(403);
        }
    }
}
