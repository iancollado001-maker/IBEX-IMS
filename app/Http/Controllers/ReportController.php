<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function index(Request $request)
    {
        $query = Asset::with(['category', 'brand']);

        // Date range filter
        if ($from = $request->get('date_from')) {
            $query->where('date_added', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->where('date_added', '<=', $to);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($cat = $request->get('category_id')) {
            $query->where('category_id', $cat);
        }
        if ($brand = $request->get('brand_id')) {
            $query->where('brand_id', $brand);
        }

        $assets = $query->orderBy('date_added', 'desc')->get();

        $summary = [
            'total'     => $assets->count(),
            'available' => $assets->where('status', 'Available')->count(),
            'deployed'  => $assets->where('status', 'Deployed')->count(),
            'defective' => $assets->where('status', 'Defective')->count(),
            'spare'     => $assets->where('status', 'Spare')->count(),
        ];

        // Group by category for chart
        $byCategory = $assets->groupBy(fn($a) => $a->category->category_name ?? 'Unknown')
            ->map->count();

        // Group by brand
        $byBrand = $assets->groupBy(fn($a) => $a->brand->brand_name ?? 'Unknown')
            ->map->count();

        $categories = Category::orderBy('category_name')->get();
        $brands     = Brand::orderBy('brand_name')->get();

        return view('reports.index', compact(
            'assets', 'summary', 'byCategory', 'byBrand',
            'categories', 'brands'
        ));
    }

    /**
     * Export CSV
     */
    public function exportCsv(Request $request)
    {
        $query = Asset::with(['category', 'brand']);

        if ($from = $request->get('date_from')) $query->where('date_added', '>=', $from);
        if ($to   = $request->get('date_to'))   $query->where('date_added', '<=', $to);
        if ($s    = $request->get('status'))     $query->where('status', $s);

        $assets = $query->orderBy('date_added', 'desc')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ibex_inventory_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($assets) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['#', 'Serial Number', 'Asset Tag', 'Category', 'Brand', 'Status', 'Date Added']);
            foreach ($assets as $i => $a) {
                fputcsv($handle, [
                    $i + 1,
                    $a->serial_number,
                    $a->asset_tag ?? '',
                    $a->category->category_name ?? '',
                    $a->brand->brand_name ?? '',
                    $a->status,
                    Carbon::parse($a->date_added)->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export PDF (HTML print version)
     */
    public function exportPdf(Request $request)
    {
        $query = Asset::with(['category', 'brand']);

        if ($from = $request->get('date_from')) $query->where('date_added', '>=', $from);
        if ($to   = $request->get('date_to'))   $query->where('date_added', '<=', $to);
        if ($s    = $request->get('status'))     $query->where('status', $s);

        $assets = $query->orderBy('date_added', 'desc')->get();

        $summary = [
            'total'     => $assets->count(),
            'available' => $assets->where('status', 'Available')->count(),
            'deployed'  => $assets->where('status', 'Deployed')->count(),
            'defective' => $assets->where('status', 'Defective')->count(),
            'spare'     => $assets->where('status', 'Spare')->count(),
        ];

        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        return view('reports.pdf', compact('assets', 'summary', 'dateFrom', 'dateTo'));
    }
}
