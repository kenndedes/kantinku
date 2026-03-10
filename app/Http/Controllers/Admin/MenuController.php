<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Stand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search    = trim($request->input('q', ''));
        $type      = $request->input('type', '');
        $standId   = $request->input('stand_id', '');
        $available = $request->input('available', '');

        $menuItems = MenuItem::with('stand')
            ->when($search !== '', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->when($type !== '', fn($q) => $q->where('type', $type))
            ->when($standId !== '', fn($q) => $q->where('stand_id', $standId))
            ->when($available !== '', fn($q) => $q->where('is_available', $available === '1'))
            ->orderBy('type')->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $stands = Stand::orderBy('name')->get();

        return view('admin.menu.index', compact('menuItems', 'stands', 'search', 'type', 'standId', 'available'));
    }

    public function create(): View
    {
        $stands = Stand::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.menu.create', [
            'stands' => $stands,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stand_id' => ['required', 'exists:stands,id'],
            'name' => ['required', 'string', 'max:100', 'unique:menu_items,name'],
            'type' => ['required', 'in:makanan,minuman'],
            'price' => ['required', 'numeric', 'min:1000', 'max:999999'],
            'stock' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('menu_photos', 'public');
        }

        MenuItem::create([
            ...$validated,
            'stand_id' => $validated['stand_id'],
            'photo' => $photoPath,
            'is_available' => $validated['is_available'] ?? true,
        ]);

        return redirect()
            ->route('admin.menu.index')
            ->with('status', 'Menu berhasil ditambahkan.');
    }

    public function edit(MenuItem $menu): View
    {
        $stands = Stand::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.menu.edit', [
            'menuItem' => $menu,
            'stands' => $stands,
        ]);
    }

    public function update(Request $request, MenuItem $menu): RedirectResponse
    {
        $validated = $request->validate([
            'stand_id' => ['required', 'exists:stands,id'],
            'name' => ['required', 'string', 'max:100', 'unique:menu_items,name,' . $menu->id],
            'type' => ['required', 'in:makanan,minuman'],
            'price' => ['required', 'numeric', 'min:1000', 'max:999999'],
            'stock' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $photoPath = $menu->photo;

        if ($request->hasFile('photo')) {
            if ($menu->photo && Storage::disk('public')->exists($menu->photo)) {
                Storage::disk('public')->delete($menu->photo);
            }

            $photoPath = $request->file('photo')->store('menu_photos', 'public');
        }

        $menu->update([
            ...$validated,
            'stand_id' => $validated['stand_id'],
            'photo' => $photoPath,
            'is_available' => $validated['is_available'] ?? true,
        ]);

        return redirect()
            ->route('admin.menu.index')
            ->with('status', 'Menu berhasil diperbarui.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()
            ->route('admin.menu.index')
            ->with('status', 'Menu berhasil dihapus.');
    }
}
