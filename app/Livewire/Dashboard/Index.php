<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Petition;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.dashboard')]
class Index extends Component
{
    public $chartData;

    public function mount()
    {
        $this->prepareChartData();
    }

    public function prepareChartData()
    {
        $orders = Order::select(
                DB::raw('count(id) as `count`'),
                DB::raw("strftime('%Y', created_at) as year"),
                DB::raw("strftime('%m', created_at) as month")
            )
            ->where(DB::raw("strftime('%Y', created_at)"), date('Y'))
            ->groupBy('year', 'month')
            ->get()
            ->toArray();

        $petitions = Petition::select(
                DB::raw('count(id) as `count`'),
                DB::raw("strftime('%Y', created_at) as year"),
                DB::raw("strftime('%m', created_at) as month")
            )
            ->where(DB::raw("strftime('%Y', created_at)"), date('Y'))
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
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'fill' => false,
                    'tension' => 0.1
                ],
                [
                    'label' => 'Petitions',
                    'data' => $petitionsData,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'fill' => false,
                    'tension' => 0.1
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