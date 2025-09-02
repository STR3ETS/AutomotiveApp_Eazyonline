<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarStage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order', 'description'];

    public function cars()
    {
        return $this->hasMany(Car::class, 'stage_id');
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class, 'stage_id');
    }

    // Geef de standaard taken voor deze fase
    public function getDefaultTasksAttribute()
    {
        $tasks = [
            'Intake' => [
                'Kenteken gecontroleerd',
                'Sleutels ontvangen',
                'Schade geïnspecteerd',
                'Kilometerstand genoteerd'
            ],
            'Technische controle' => [
                'APK-keuring uitgevoerd',
                'Olie vervangen',
                'Remmen gecontroleerd',
                'Banden geïnspecteerd',
                'Technische rapporten opgesteld'
            ],
            'Herstel & Onderhoud' => [
                'Kleine reparaties uitgevoerd',
                'Onderhoud voltooid',
                'Onderdelen vervangen',
                'Kwaliteitscontrole'
            ],
            'Commercieel gereed' => [
                'Auto gewassen en gepoetst',
                'Interieur gereinigd',
                'Marketing foto\'s gemaakt',
                'Advertentie tekst geschreven',
                'Online geplaatst'
            ],
            'Verkoop klaar' => [
                'Prijs bepaald',
                'Verkoopklaar gemaakt',
                'Test drive mogelijk',
                'Financiering voorbereid'
            ]
        ];

        return $tasks[$this->name] ?? [];
    }
}
