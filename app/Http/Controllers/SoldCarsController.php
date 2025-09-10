<?php

namespace App\Http\Controllers;

use App\Models\SoldCar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SoldCarsController extends Controller
{
    public function index(Request $request)
    {
        $query = SoldCar::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(brand, ' ', model) like ?", ["%{$search}%"]);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('sold_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('sold_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        $soldCars = $query->orderBy('sold_at', 'desc')->paginate(20);

        // Get stats
        $stats = $this->getStats($request);

        // Get unique brands for filter
        $brands = SoldCar::select('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('sold-cars.index', compact('soldCars', 'stats', 'brands'));
    }

    public function show(SoldCar $soldCar)
    {
        return view('sold-cars.show', compact('soldCar'));
    }

    private function getStats(Request $request)
    {
        $query = SoldCar::query();
        
        // Apply same filters as main query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(brand, ' ', model) like ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('sold_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('sold_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        $totalCars = $query->count();
        $totalRevenue = $query->sum('sale_price');
        $totalProfit = $query->whereNotNull('purchase_price')->sum(DB::raw('sale_price - purchase_price'));
        $averagePrice = $query->avg('sale_price');

        return [
            'total_cars' => $totalCars,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'average_price' => $averagePrice,
        ];
    }
}
