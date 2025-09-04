<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\Sale;
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
        
        return view('reports.index', compact(
            'kpis',
            'pipelineData', 
            'financialData',
            'repairData',
            'customerData',
            'performanceData'
        ));
    }
    
    private function getKPIs()
    {
        $now = Carbon::now();
        $thisMonth = $now->startOfMonth();
        $lastMonth = $now->copy()->subMonth()->startOfMonth();
        
        // Total cars in system
        $totalCars = Car::count();
        
        // Cars sold this month
        $salesThisMonth = Sale::where('status', 'delivered')
            ->where('sold_at', '>=', $thisMonth)
            ->count();
            
        $salesLastMonth = Sale::where('status', 'delivered')
            ->whereBetween('sold_at', [$lastMonth, $thisMonth])
            ->count();
        
        // Revenue this month
        $revenueThisMonth = Sale::where('status', 'delivered')
            ->where('sold_at', '>=', $thisMonth)
            ->sum('sale_price');
            
        $revenueLastMonth = Sale::where('status', 'delivered')
            ->whereBetween('sold_at', [$lastMonth, $thisMonth])
            ->sum('sale_price');
        
        // Active repairs
        $activeRepairs = Repair::whereIn('status', ['gepland', 'bezig', 'wachten_op_onderdeel'])->count();
        
        // Average days in pipeline
        $avgDaysInPipeline = $this->calculateAverageDaysInPipeline();
        
        return [
            'total_cars' => $totalCars,
            'sales_this_month' => $salesThisMonth,
            'sales_growth' => $salesLastMonth > 0 ? round((($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100, 1) : 0,
            'revenue_this_month' => $revenueThisMonth,
            'revenue_growth' => $revenueLastMonth > 0 ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) : 0,
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
        $monthlyRevenue = Sale::where('status', 'delivered')
            ->selectRaw('MONTH(sold_at) as month, YEAR(sold_at) as year, SUM(sale_price) as revenue, COUNT(*) as sales_count')
            ->whereYear('sold_at', Carbon::now()->year)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();
        
        // Top performing car brands
        $brandPerformance = Sale::join('cars', 'sales.car_id', '=', 'cars.id')
            ->where('sales.status', 'delivered')
            ->selectRaw('cars.brand, COUNT(*) as sales_count, AVG(sales.sale_price) as avg_price, SUM(sales.sale_price) as total_revenue')
            ->groupBy('cars.brand')
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
            ->groupBy('description')
            ->orderByDesc('frequency')
            ->limit(5)
            ->get();
        
        // Repair status distribution
        $repairStatus = Repair::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        // Average repair cost per month
        $avgRepairCost = Repair::selectRaw('MONTH(created_at) as month, AVG(cost_estimate) as avg_cost')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
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
        
        // Customer conversion rate (appointments to sales)
        $totalAppointments = Appointment::count();
        $totalSales = Sale::count();
        $conversionRate = $totalAppointments > 0 ? round(($totalSales / $totalAppointments) * 100, 1) : 0;
        
        // Top customers by purchase count
        $topCustomers = Customer::withCount('sales')
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
        // Cars completed this week
        $startOfWeek = Carbon::now()->startOfWeek();
        $carsCompletedThisWeek = Sale::where('status', 'delivered')
            ->where('sold_at', '>=', $startOfWeek)
            ->count();
        
        // Upcoming appointments
        $upcomingAppointments = Appointment::where('date', '>=', Carbon::today())
            ->where('date', '<=', Carbon::today()->addDays(7))
            ->count();
        
        // Cars awaiting action
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
        // Calculate average days from first stage to sale
        $completedSales = Sale::where('status', 'delivered')
            ->with('car')
            ->whereNotNull('sold_at')
            ->get();
        
        if ($completedSales->isEmpty()) {
            return 0;
        }
        
        $totalDays = 0;
        $count = 0;
        
        foreach ($completedSales as $sale) {
            if ($sale->car && $sale->car->created_at && $sale->sold_at) {
                $days = $sale->car->created_at->diffInDays($sale->sold_at);
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
}
