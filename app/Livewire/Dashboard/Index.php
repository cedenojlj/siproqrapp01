<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


#[Layout('components.layouts.dashboard')]
class Index extends Component
{
    public $chartData;
    public $monthlyOrdersCount;
    public $monthlyPayments;
    public $monthlySales;
    public $yearlySales;

    public function mount()
    {
        $this->prepareChartData();
        
        $now = now();
        
        $this->monthlyOrdersCount = Order::whereYear('created_at', $now->year)
                                         ->whereMonth('created_at', $now->month)
                                         ->count();
                                         
        $this->monthlyPayments = Payment::whereYear('fecha_pago', $now->year)
                                        ->whereMonth('fecha_pago', $now->month)
                                        ->sum('monto');
                                        
        $this->monthlySales = Order::whereYear('created_at', $now->year)
                                   ->whereMonth('created_at', $now->month)
                                   ->sum('total');
                                   
        $this->yearlySales = Order::whereYear('created_at', $now->year)
                                  ->sum('total');
    }

    public function prepareChartData()
    {
        $sales = Order::select(
                DB::raw('sum(total) as `amount`'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('year', 'month')
            ->get()
            ->toArray();

        $payments = Payment::select(
                DB::raw('sum(monto) as `amount`'),
                DB::raw('YEAR(fecha_pago) as year'),
                DB::raw('MONTH(fecha_pago) as month')
            )
            ->whereYear('fecha_pago', date('Y'))
            ->groupBy('year', 'month')
            ->get()
            ->toArray();

        $labels = [];
        $salesData = [];
        $paymentsData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 10));
            $labels[] = $monthName;
            $salesData[] = $this->getAmountForMonth($sales, $i);
            $paymentsData[] = $this->getAmountForMonth($payments, $i);
        }

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ventas',
                    'data' => $salesData,
                    'borderColor' => '#3B82F6', // Blue
                ],
                [
                    'label' => 'Abonos',
                    'data' => $paymentsData,
                    'borderColor' => '#1CC809', // Green
                ]
            ]
        ];
    }

    private function getAmountForMonth($data, $month)
    {
        foreach ($data as $item) {
            if ((int)$item['month'] == $month) {
                return $item['amount'];
            }
        }
        return 0;
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}