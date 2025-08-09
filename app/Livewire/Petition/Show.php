<?php

namespace App\Livewire\Petition;

use Livewire\Component;
use App\Models\Petition;
use Barryvdh\DomPDF\Facade\Pdf;

class Show extends Component
{
    public $petition;

    public function mount(Petition $petition)
    {
        $this->petition = $petition;
    }

    public function generatePdf()
    {
        $pdf = Pdf::loadView('pdf.petition-details', ['petition' => $this->petition]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'petition_' . $this->petition->id . '.pdf');
    }

    public function render()
    {
        return view('livewire.petition.show');
    }
}