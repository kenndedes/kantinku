<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Stand;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $standId = request()->input('stand_id');
        $search  = trim(request()->input('q', ''));

        $stands = Stand::query()
            ->where('is_active', true)
            ->withCount(['menuItems as available_menu_count' => function ($q) {
                $q->where('is_available', true);
            }])
            ->having('available_menu_count', '>', 0)
            ->orderBy('name')
            ->get();

        $menuItems = collect();
        $selectedStand = null;

        if ($standId) {
            $selectedStand = $stands->firstWhere('id', $standId);
            $menuItems = MenuItem::query()
                ->with('category')
                ->where('stand_id', $standId)
                ->where('is_available', true)
                ->when($search !== '', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orderBy('name')
                ->get();
        }

        return view('menu.index', [
            'menuItems'       => $menuItems,
            'stands'          => $stands,
            'selectedStand'   => $selectedStand,
            'selectedStandId' => $standId,
            'search'          => $search,
        ]);
    }
}
