<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Werkzaamheden Rapport</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .car-info {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 5px 5px 0;
        }
        
        .car-info h2 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 18px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
        
        .info-value {
            color: #333;
        }
        
        .message {
            font-size: 16px;
            line-height: 1.7;
            color: #333;
            margin-bottom: 25px;
        }
        
        .attachment-notice {
            background-color: #e7f3ff;
            border: 1px solid #b8daff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .attachment-notice h3 {
            margin: 0 0 10px 0;
            color: #004085;
            font-size: 16px;
        }
        
        .attachment-notice p {
            margin: 0;
            color: #004085;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 14px;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .icon {
            display: inline-block;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🚗 Werkzaamheden Rapport</h1>
            <p>{{ $car->company->name ?? 'Automotive App' }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="message">
                <p>Beste,</p>
                
                <p>Hierbij ontvangt u het uitgebreide werkzaamheden rapport voor het voertuig <strong>{{ $car->license_plate }}</strong>. Dit rapport bevat een volledig overzicht van alle uitgevoerde werkzaamheden en controles.</p>
            </div>

            <!-- Auto Informatie -->
            <div class="car-info">
                <h2><span class="icon">🔧</span>Voertuig Details</h2>
                
                <div class="info-row">
                    <span class="info-label">Kenteken:</span>
                    <span class="info-value">{{ $car->license_plate }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Merk & Model:</span>
                    <span class="info-value">{{ $car->brand }} {{ $car->model }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Bouwjaar:</span>
                    <span class="info-value">{{ $car->year }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Kilometerstand:</span>
                    <span class="info-value">{{ number_format($car->mileage) }} km</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Huidige Status:</span>
                    <span class="info-value">{{ $car->stage->name ?? 'Onbekend' }}</span>
                </div>
            </div>

            <!-- Bijlage Informatie -->
            <div class="attachment-notice">
                <h3><span class="icon">📎</span>Bijgevoegd Document</h3>
                <p>In de bijlage vindt u het complete PDF rapport met alle uitgevoerde werkzaamheden, gedetailleerde informatie per fase, en een overzicht van alle reparaties.</p>
            </div>

            <div class="message">
                <p>Het rapport is automatisch gegenereerd op <strong>{{ now()->format('d-m-Y') }}</strong> om <strong>{{ now()->format('H:i') }}</strong>.</p>
                
                <p>Voor vragen over dit rapport kunt u contact opnemen met ons team.</p>
                
                <p>Met vriendelijke groet,<br>
                <strong>{{ $car->company->name ?? 'Automotive App' }}</strong></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $car->company->name ?? 'Automotive App' }}</strong></p>
            <p>Dit is een automatisch gegenereerd rapport • {{ now()->format('d-m-Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
