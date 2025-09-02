<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Car;
use App\Models\CarStage;
use App\Models\Checklist;

class GenerateChecklists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checklist:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate checklist items for all cars and stages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating checklists for all cars and stages...');
        
        $cars = Car::all();
        $stages = CarStage::all();
        
        $created = 0;
        
        foreach ($cars as $car) {
            $this->info("Processing car: {$car->license_plate}");
            
            foreach ($stages as $stage) {
                $existingTasks = $car->checklists()
                    ->where('stage_id', $stage->id)
                    ->pluck('task')
                    ->toArray();

                foreach ($stage->default_tasks as $task) {
                    if (!in_array($task, $existingTasks)) {
                        Checklist::create([
                            'car_id' => $car->id,
                            'stage_id' => $stage->id,
                            'task' => $task,
                            'is_completed' => false,
                        ]);
                        $created++;
                    }
                }
            }
        }
        
        $this->info("✅ Generated {$created} checklist items!");
        $this->info("Total cars: {$cars->count()}");
        $this->info("Total stages: {$stages->count()}");
        
        return 0;
    }
}
