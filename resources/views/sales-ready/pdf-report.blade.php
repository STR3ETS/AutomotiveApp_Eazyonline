<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Werkzaamheden Rapport - {{ $car->license_plate }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .car-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .car-info h2 {
            color: #007bff;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label,
        .info-value {
            display: table-cell;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            width: 30%;
            color: #666;
        }
        
        .tasks-section {
            margin-bottom: 30px;
        }
        
        .stage-header {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            font-size: 14px;
        }
        
        .task-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .task-item {
            padding: 8px 15px;
            border-bottom: 1px solid #eee;
            background-color: #fafafa;
        }
        
        .task-item:last-child {
            border-bottom: none;
        }
        
        .task-description {
            font-weight: 500;
            margin-bottom: 3px;
        }
        
        .task-meta {
            font-size: 10px;
            color: #666;
        }
        
        .repair-badge {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            margin-left: 10px;
        }
        
        .stats-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            width: 33.33%;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $car->company->name ?? 'Automotive App' }}</div>
        <div class="report-title">Werkzaamheden Rapport</div>
        <div style="font-size: 12px; color: #888;">Gegenereerd op {{ now()->format('d-m-Y H:i') }}</div>
    </div>

    <!-- Auto Informatie -->
    <div class="car-info">
        <h2>Voertuig Informatie</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Kenteken:</div>
                <div class="info-value">{{ $car->license_plate }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Merk & Model:</div>
                <div class="info-value">{{ $car->brand }} {{ $car->model }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Bouwjaar:</div>
                <div class="info-value">{{ $car->year }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kilometerstand:</div>
                <div class="info-value">{{ number_format($car->mileage) }} km</div>
            </div>
            <div class="info-row">
                <div class="info-label">Vraagprijs:</div>
                <div class="info-value">€ {{ number_format($car->price, 2, ',', '.') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Huidige Status:</div>
                <div class="info-value">{{ $car->stage->name ?? 'Onbekend' }}</div>
            </div>
        </div>
    </div>

    <!-- Uitgevoerde Werkzaamheden -->
    <div class="tasks-section">
        <h2 style="color: #007bff; margin-bottom: 20px;">Uitgevoerde Werkzaamheden</h2>
        
        @if($car->completed_tasks_by_stage->count() === 0)
            <p style="text-align: center; color: #666; font-style: italic; padding: 20px;">
                Geen voltooide taken gevonden voor dit voertuig.
            </p>
        @else
            @foreach($car->completed_tasks_by_stage as $stageName => $tasks)
                <div class="stage-header">
                    {{ $stageName }} ({{ $tasks->count() }} taken)
                </div>
                
                <ul class="task-list">
                    @foreach($tasks as $task)
                        <li class="task-item">
                            <div class="task-description">
                                {{ $task->task }}
                                @if($task->repair)
                                    <span class="repair-badge">REPARATIE</span>
                                @endif
                            </div>
                            <div class="task-meta">
                                Voltooid op: {{ $task->updated_at->format('d-m-Y H:i') }}
                                @if($task->repair && $task->repair->cost_estimate)
                                    • Geschatte kosten: €{{ number_format($task->repair->cost_estimate, 2, ',', '.') }}
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        @endif
    </div>

    <!-- Statistieken -->
    <div class="stats-section">
        <h3 style="margin: 0 0 15px 0; color: #007bff;">Overzicht</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $car->checklists->count() }}</div>
                <div class="stat-label">Totaal Taken</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $car->checklists->where('repair_id', '!=', null)->count() }}</div>
                <div class="stat-label">Reparaties</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $car->completed_tasks_by_stage->count() }}</div>
                <div class="stat-label">Fases Doorlopen</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        {{ $car->company->name ?? 'Automotive App' }} • Werkzaamheden Rapport • {{ $car->license_plate }}
    </div>
</body>
</html>
