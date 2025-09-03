<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Petition;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


#[Layout('components.layouts.dashboard')]
class Index extends Component
{
    public $chartData;
    public $productosCount;
    public $ordenesCount;
    public $peticionesCount;
    public $ordenesTotal;

    public function mount()
    {
        $this->prepareChartData();
        $this->productosCount = Product::all()->count();
        $this->ordenesCount = Order::where('user_id', Auth::id())->count();
        $this->peticionesCount = Petition::where('user_id', Auth::id())->count();
        $this->ordenesTotal = Order::where('user_id', Auth::id())->sum('total');
    }

    public function prepareChartData()
    {
        $orders = Order::select(
                DB::raw('count(id) as `count`'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereYear('created_at', date('Y'))
            ->where('user_id', Auth::id())
            ->groupBy('year', 'month')
            ->get()
            ->toArray();

        $petitions = Petition::select(
                DB::raw('count(id) as `count`'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereYear('created_at', date('Y'))
            ->where('user_id', Auth::id())
            ->groupBy('year', 'month')
            ->get()
            ->toArray();

        $labels = [];
        $ordersData = [];
        $petitionsData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 10));
            $labels[] = $monthName;
            $ordersData[] = $this->getCountForMonth($orders, $i);
            $petitionsData[] = $this->getCountForMonth($petitions, $i);
        }

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $ordersData,
                    'borderColor' => '#3B82F6', // Blue
                ],
                [
                    'label' => 'Petitions',
                    'data' => $petitionsData,
                    'borderColor' => '#1CC809', // Green
                ]
            ]
        ];
    }

    private function getCountForMonth($data, $month)
    {
        foreach ($data as $item) {
            if ((int)$item['month'] == $month) {
                return $item['count'];
            }
        }
        return 0;
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}