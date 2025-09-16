<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\Sale;
use App\Models\SoldCar;
use App\Models\Repair;
use App\Models\CarStage;
use App\Models\Customer;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        // 📊 KPI Dashboard Data
        $kpis = $this->getKPIs();
        
        // 📈 Pipeline Analytics
        $pipelineData = $this->getPipelineAnalytics();
        
        // 💰 Financial Overview
        $financialData = $this->getFinancialData();
        
        // 🔧 Repair Analytics
        $repairData = $this->getRepairAnalytics();
        
        // 👥 Customer Insights
        $customerData = $this->getCustomerInsights();
        
        // 📅 Recent Performance
        $performanceData = $this->getPerformanceData();
        
        // 🚗 Recent Sales Data
        $recentSalesData = $this->getRecentSalesData();
        
        // ⚠️ Pending Deliveries
        $pendingDeliveries = $this->getPendingDeliveries();
        
        return view('reports.index', compact(
            'kpis',
            'pipelineData', 
            'financialData',
            'repairData',
            'customerData',
            'performanceData',
            'recentSalesData',
            'pendingDeliveries'
        ));
    }
    
    private function getKPIs()
    {
        $now = Carbon::now();
        $thisMonth = $now->startOfMonth();
        $lastMonth = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        
        // Total cars in system (active cars + sold cars)
        $totalCars = Car::count() + SoldCar::count();
        
        // Cars sold this month (from sold_cars table)
        $salesThisMonth = SoldCar::where('sold_at', '>=', $thisMonth)->count();
        $salesLastMonth = SoldCar::whereBetween('sold_at', [$lastMonth, $lastMonthEnd])->count();
        
        // Revenue this month (from sold_cars table)
        $revenueThisMonth = SoldCar::where('sold_at', '>=', $thisMonth)->sum('sale_price');
        $revenueLastMonth = SoldCar::whereBetween('sold_at', [$lastMonth, $lastMonthEnd])->sum('sale_price');
        
        // Active repairs
        $activeRepairs = Repair::whereIn('status', ['gepland', 'bezig', 'wachten_op_onderdeel'])->count();
        
        // Average days in pipeline
        $avgDaysInPipeline = $this->calculateAverageDaysInPipeline();
        
        return [
            'total_cars' => $totalCars,
            'sales_this_month' => $salesThisMonth,
            'sales_growth' => $salesLastMonth > 0 ? round((($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100, 1) : ($salesThisMonth > 0 ? 100 : 0),
            'revenue_this_month' => $revenueThisMonth,
            'revenue_growth' => $revenueLastMonth > 0 ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) : ($revenueThisMonth > 0 ? 100 : 0),
            'active_repairs' => $activeRepairs,
            'avg_days_pipeline' => $avgDaysInPipeline
        ];
    }
    
    private function getPipelineAnalytics()
    {
        // Cars per stage
        $stageDistribution = CarStage::with('cars')
            ->orderBy('order')
            ->get()
            ->map(function($stage) {
                return [
                    'name' => $stage->name,
                    'count' => $stage->cars->count(),
                    'percentage' => Car::count() > 0 ? round(($stage->cars->count() / Car::count()) * 100, 1) : 0
                ];
            });
        
        // Bottleneck analysis - stages with longest average time
        $bottlenecks = $this->identifyBottlenecks();
        
        // Completion rates per stage
        $completionRates = $this->getStageCompletionRates();
        
        return [
            'stage_distribution' => $stageDistribution,
            'bottlenecks' => $bottlenecks,
            'completion_rates' => $completionRates
        ];
    }
    
    private function getFinancialData()
    {
        // Monthly revenue from sold cars - ensure we have data for all 12 months
        $monthlyRevenueRaw = SoldCar::selectRaw('MONTH(sold_at) as month, YEAR(sold_at) as year, SUM(sale_price) as revenue, COUNT(*) as sales_count')
            ->whereYear('sold_at', Carbon::now()->year)
            ->groupBy(DB::raw('YEAR(sold_at)'), DB::raw('MONTH(sold_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');
        
        // Fill in missing months with zero values
        $monthlyRevenue = collect();
        for ($i = 1; $i <= 12; $i++) {
            if ($monthlyRevenueRaw->has($i)) {
                $monthlyRevenue->push($monthlyRevenueRaw->get($i));
            } else {
                $monthlyRevenue->push((object)[
                    'month' => $i,
                    'year' => Carbon::now()->year,
                    'revenue' => 0,
                    'sales_count' => 0
                ]);
            }
        }
        
        // Top performing car brands from sold cars
        $brandPerformance = SoldCar::selectRaw('brand, COUNT(*) as sales_count, AVG(sale_price) as avg_price, SUM(sale_price) as total_revenue')
            ->whereNotNull('sold_at')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        
        // Repair costs vs revenue
        $repairCosts = Repair::selectRaw('SUM(cost_estimate) as total_repair_costs')
            ->whereMonth('created_at', Carbon::now()->month)
            ->first();
        
        return [
            'monthly_revenue' => $monthlyRevenue,
            'brand_performance' => $brandPerformance,
            'repair_costs' => $repairCosts->total_repair_costs ?? 0
        ];
    }
    
    private function getRepairAnalytics()
    {
        // Most common repairs
        $commonRepairs = Repair::selectRaw('description, COUNT(*) as frequency, AVG(cost_estimate) as avg_cost')
            ->whereNotNull('description')
            ->groupBy('description')
            ->orderByDesc('frequency')
            ->limit(5)
            ->get();
        
        // Repair status distribution
        $repairStatus = Repair::selectRaw('status, COUNT(*) as count')
            ->whereNotNull('status')
            ->groupBy('status')
            ->get();
        
        // Average repair cost per month
        $avgRepairCost = Repair::selectRaw('MONTH(created_at) as month, AVG(cost_estimate) as avg_cost')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();
        
        return [
            'common_repairs' => $commonRepairs,
            'repair_status' => $repairStatus,
            'avg_repair_cost' => $avgRepairCost
        ];
    }
    
    private function getCustomerInsights()
    {
        // Total customers
        $totalCustomers = Customer::count();
        
        // New customers this month
        $newCustomersThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Customer conversion rate (appointments to actual sales)
        $totalAppointments = Appointment::count();
        $totalActualSales = SoldCar::count(); // Use sold cars for actual completed sales
        $conversionRate = $totalAppointments > 0 ? round(($totalActualSales / $totalAppointments) * 100, 1) : 0;
        
        // Top customers by purchase count (from sold cars)
        $topCustomers = SoldCar::selectRaw('customer_name as name, customer_email as email, COUNT(*) as sales_count')
            ->whereNotNull('customer_name')
            ->groupBy('customer_name', 'customer_email')
            ->having('sales_count', '>', 0)
            ->orderByDesc('sales_count')
            ->limit(5)
            ->get();
        
        return [
            'total_customers' => $totalCustomers,
            'new_customers_month' => $newCustomersThisMonth,
            'conversion_rate' => $conversionRate,
            'top_customers' => $topCustomers
        ];
    }
    
    private function getPerformanceData()
    {
        // Cars completed this week (sold cars)
        $startOfWeek = Carbon::now()->startOfWeek();
        $carsCompletedThisWeek = SoldCar::where('sold_at', '>=', $startOfWeek)->count();
        
        // Upcoming appointments
        $upcomingAppointments = Appointment::where('date', '>=', Carbon::today())
            ->where('date', '<=', Carbon::today()->addDays(7))
            ->count();
        
        // Cars awaiting action (active cars with incomplete checklists)
        $carsAwaitingAction = Car::whereHas('checklists', function($query) {
            $query->where('is_completed', false);
        })->count();
        
        return [
            'cars_completed_week' => $carsCompletedThisWeek,
            'upcoming_appointments' => $upcomingAppointments,
            'cars_awaiting_action' => $carsAwaitingAction
        ];
    }
    
    private function calculateAverageDaysInPipeline()
    {
        // Calculate average days from car creation to sale completion using sold cars
        $completedSales = SoldCar::whereNotNull('sold_at')->get();
        
        if ($completedSales->isEmpty()) {
            return 0;
        }
        
        $totalDays = 0;
        $count = 0;
        
        foreach ($completedSales as $soldCar) {
            // Use the sold car's created_at as proxy for when the car entered the system
            if ($soldCar->created_at && $soldCar->sold_at) {
                $days = $soldCar->created_at->diffInDays($soldCar->sold_at);
                $totalDays += $days;
                $count++;
            }
        }
        
        return $count > 0 ? round($totalDays / $count, 1) : 0;
    }
    
    private function identifyBottlenecks()
    {
        // Identify stages where cars stay the longest
        $stages = CarStage::with(['cars' => function($query) {
            $query->oldest('updated_at');
        }])->get();
        
        $bottlenecks = [];
        
        foreach ($stages as $stage) {
            if ($stage->cars->count() > 0) {
                $avgDaysInStage = $stage->cars->avg(function($car) {
                    return $car->updated_at->diffInDays(Carbon::now());
                });
                
                $bottlenecks[] = [
                    'stage' => $stage->name,
                    'avg_days' => round($avgDaysInStage, 1),
                    'car_count' => $stage->cars->count()
                ];
            }
        }
        
        // Sort by average days (descending)
        usort($bottlenecks, function($a, $b) {
            return $b['avg_days'] <=> $a['avg_days'];
        });
        
        return array_slice($bottlenecks, 0, 3); // Top 3 bottlenecks
    }
    
    private function getStageCompletionRates()
    {
        $stages = CarStage::with(['checklists' => function($query) {
            $query->select('stage_id', 'is_completed');
        }])->get();
        
        $completionRates = [];
        
        foreach ($stages as $stage) {
            $totalTasks = $stage->checklists->count();
            $completedTasks = $stage->checklists->where('is_completed', true)->count();
            
            $completionRates[] = [
                'stage' => $stage->name,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks
            ];
        }
        
        return $completionRates;
    }

    private function getRecentSalesData()
    {
        // Recent sales (last 30 days)
        $recentSales = SoldCar::with([])
            ->where('sold_at', '>=', Carbon::now()->subDays(30))
            ->orderByDesc('sold_at')
            ->limit(10)
            ->get()
            ->map(function($soldCar) {
                return [
                    'license_plate' => $soldCar->license_plate,
                    'brand_model' => $soldCar->brand . ' ' . $soldCar->model,
                    'customer_name' => $soldCar->customer_name,
                    'sale_price' => $soldCar->sale_price,
                    'sold_at' => $soldCar->sold_at,
                    'profit' => $soldCar->sale_price - ($soldCar->purchase_price ?? 0),
                ];
            });

        // Sales summary for the period
        $totalRecentSales = SoldCar::where('sold_at', '>=', Carbon::now()->subDays(30))->count();
        $totalRecentRevenue = SoldCar::where('sold_at', '>=', Carbon::now()->subDays(30))->sum('sale_price');
        $avgSalePrice = $totalRecentSales > 0 ? round($totalRecentRevenue / $totalRecentSales, 0) : 0;

        return [
            'recent_sales' => $recentSales,
            'total_recent_sales' => $totalRecentSales,
            'total_recent_revenue' => $totalRecentRevenue,
            'avg_sale_price' => $avgSalePrice,
        ];
    }

    private function getPendingDeliveries()
    {
        // Sales that are paid but not yet delivered
        $pendingDeliveries = Sale::with(['car', 'customer'])
            ->where('payment_status', 'paid')
            ->whereIn('status', ['contract_signed', 'ready_for_delivery'])
            ->orderBy('delivery_date')
            ->get()
            ->map(function($sale) {
                return [
                    'id' => $sale->id,
                    'license_plate' => $sale->car->license_plate ?? 'Onbekend',
                    'brand_model' => ($sale->car->brand ?? '') . ' ' . ($sale->car->model ?? ''),
                    'customer_name' => $sale->customer->name ?? 'Onbekend',
                    'sale_price' => $sale->sale_price,
                    'delivery_date' => $sale->delivery_date,
                    'status' => $sale->status,
                    'days_overdue' => $sale->delivery_date && $sale->delivery_date < now() ? now()->diffInDays($sale->delivery_date) : 0,
                ];
            });

        return [
            'pending_deliveries' => $pendingDeliveries,
            'total_pending' => $pendingDeliveries->count(),
            'overdue_count' => $pendingDeliveries->where('days_overdue', '>', 0)->count(),
        ];
    }
}
