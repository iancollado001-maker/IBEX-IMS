<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AssetController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Dashboard / List Assets
     */
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'brand']);

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(25);

        // Stats
        $stats = $this->getStats();

        // Monthly (default: this month)
        $monthly = $this->getMonthlyCounts('this_month');

        $categories = Category::orderBy('category_name')->get();
        $brands     = Brand::orderBy('brand_name')->get();

        return view('inventory.index', compact('assets', 'stats', 'monthly', 'categories', 'brands'));
    }

    /**
     * Store new asset
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => 'required|string|max:100|unique:assets,serial_number',
            'asset_tag'     => 'nullable|string|max:100',
            'category_id'   => 'required|exists:categories,id',
            'brand_id'      => 'required|exists:brands,id',
            'status'        => 'required|in:Available,Deployed,Spare,Defective',
            'date_added'    => 'required|date',
        ]);

        $asset = Asset::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Asset added successfully.',
            'asset'   => $asset,
        ]);
    }

    /**
     * Update existing asset
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'serial_number' => 'required|string|max:100|unique:assets,serial_number,' . $asset->id,
            'asset_tag'     => 'nullable|string|max:100',
            'category_id'   => 'required|exists:categories,id',
            'brand_id'      => 'required|exists:brands,id',
            'status'        => 'required|in:Available,Deployed,Spare,Defective',
            'date_added'    => 'required|date',
        ]);

        $asset->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Asset updated successfully.',
            'asset'   => $asset,
        ]);
    }

    /**
     * Delete asset
     */
    public function destroy(Asset $asset)
    {
        try {
            // Stamp removed_at before soft-deleting so monthly stats can count it
            $asset->update(['removed_at' => now()]);
            $asset->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove asset: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * JSON stats endpoint
     */
    public function stats()
    {
        return response()->json($this->getStats());
    }

    /**
     * Monthly summary endpoint
     */
    public function monthly(Request $request)
    {
        $period = $request->get('period', 'this_month');
        return response()->json($this->getMonthlyCounts($period));
    }

    /**
     * Search endpoint for AJAX
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $assets = Asset::with(['category', 'brand'])
            ->where(function ($query) use ($q) {
                $query->where('serial_number', 'like', "%{$q}%")
                      ->orWhere('asset_tag', 'like', "%{$q}%");
            })
            ->limit(50)->get();

        return response()->json($assets);
    }

    // ─── Private Helpers ─────────────────────────────────────

    private function getStats(): array
    {
        return [
            'total'     => Asset::count(),
            'defective' => Asset::where('status', 'Defective')->count(),
            'deployed'  => Asset::where('status', 'Deployed')->count(),
            'available' => Asset::where('status', 'Available')->count(),
            'spare'     => Asset::where('status', 'Spare')->count(),
        ];
    }

    private function getMonthlyCounts(string $period): array
    {
        [$from, $to] = match($period) {
            'last_month' => [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ],
            'this_year' => [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ],
            default => [  // this_month
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
        };

        return [
            'added'   => Asset::whereBetween('date_added', [$from, $to])->count(),
            'removed' => Asset::withTrashed()
                ->whereBetween('deleted_at', [$from, $to])
                ->whereNotNull('deleted_at')->count(),
        ];
    }
}
