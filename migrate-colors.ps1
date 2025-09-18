# PowerShell script om hardcoded kleuren te vervangen door CSS variabelen
# Dit script updatet alle .blade.php bestanden in de resources/views directory

Write-Host "🎨 Start migratie naar CSS variabelen..." -ForegroundColor Green

# Directory waar de blade bestanden staan
$ViewsDir = "resources\views"

# Zoek alle .blade.php bestanden
Get-ChildItem -Path $ViewsDir -Filter "*.blade.php" -Recurse | ForEach-Object {
    $file = $_.FullName
    Write-Host "📝 Bewerk bestand: $file" -ForegroundColor Yellow
    
    # Lees bestand content
    $content = Get-Content -Path $file -Raw
    
    # Maak backup
    Copy-Item -Path $file -Destination "$file.backup"
    
    # Vervang achtergrond kleuren
    $content = $content -replace 'bg-gray-50', 'bg-[var(--background-main)]'
    $content = $content -replace 'bg-white(?!\s*rounded)', 'bg-[var(--background-card)]'
    $content = $content -replace 'bg-gray-100', 'bg-[var(--background-secondary)]'
    
    # Vervang tekst kleuren
    $content = $content -replace 'text-gray-900', 'text-[var(--text-primary)]'
    $content = $content -replace 'text-gray-600', 'text-[var(--text-secondary)]'
    $content = $content -replace 'text-gray-500', 'text-[var(--text-secondary)]'
    $content = $content -replace 'text-gray-400', 'text-[var(--text-tertiary)]'
    $content = $content -replace 'text-white(?!\s*rounded)', 'text-[var(--text-white)]'
    
    # Vervang border kleuren
    $content = $content -replace 'border-gray-200', 'border-[var(--border-light)]'
    $content = $content -replace 'border-gray-300', 'border-[var(--border-medium)]'
    $content = $content -replace 'divide-gray-200', 'divide-[var(--border-light)]'
    
    # Vervang status kleuren - Groen (Success)
    $content = $content -replace 'bg-green-100', 'bg-[var(--status-success-light)]'
    $content = $content -replace 'bg-green-50', 'bg-[var(--status-success-bg)]'
    $content = $content -replace 'text-green-600', 'text-[var(--status-success)]'
    $content = $content -replace 'text-green-700', 'text-[var(--status-success-dark)]'
    $content = $content -replace 'text-green-800', 'text-[var(--status-success-text)]'
    $content = $content -replace 'text-green-900', 'text-[var(--status-success-text)]'
    $content = $content -replace 'border-green-200', 'border-[var(--status-success-border)]'
    $content = $content -replace 'border-green-400', 'border-[var(--status-success-border)]'
    
    # Vervang status kleuren - Blauw (Info)
    $content = $content -replace 'bg-blue-100', 'bg-[var(--status-info-light)]'
    $content = $content -replace 'bg-blue-50', 'bg-[var(--status-info-bg)]'
    $content = $content -replace 'text-blue-600', 'text-[var(--status-info)]'
    $content = $content -replace 'text-blue-700', 'text-[var(--status-info-dark)]'
    $content = $content -replace 'text-blue-800', 'text-[var(--status-info-text)]'
    $content = $content -replace 'text-blue-900', 'text-[var(--status-info-text)]'
    $content = $content -replace 'border-blue-100', 'border-[var(--status-info-border)]'
    
    # Vervang status kleuren - Rood (Danger)
    $content = $content -replace 'bg-red-100', 'bg-[var(--status-danger-light)]'
    $content = $content -replace 'bg-red-50', 'bg-[var(--status-danger-bg)]'
    $content = $content -replace 'text-red-600', 'text-[var(--status-danger)]'
    $content = $content -replace 'text-red-700', 'text-[var(--status-danger-dark)]'
    $content = $content -replace 'text-red-800', 'text-[var(--status-danger-text)]'
    $content = $content -replace 'text-red-900', 'text-[var(--status-danger-text)]'
    $content = $content -replace 'border-red-400', 'border-[var(--status-danger-light)]'
    
    # Vervang status kleuren - Geel/Oranje (Warning)
    $content = $content -replace 'bg-yellow-100', 'bg-[var(--status-warning-light)]'
    $content = $content -replace 'bg-amber-100', 'bg-[var(--status-warning-light)]'
    $content = $content -replace 'bg-orange-100', 'bg-[var(--status-maintenance-light)]'
    $content = $content -replace 'text-yellow-800', 'text-[var(--status-warning-text)]'
    $content = $content -replace 'text-amber-800', 'text-[var(--status-warning-text)]'
    $content = $content -replace 'text-orange-600', 'text-[var(--status-maintenance)]'
    $content = $content -replace 'text-orange-500', 'text-[var(--status-maintenance)]'
    
    # Vervang status kleuren - Paars (Special)
    $content = $content -replace 'bg-purple-100', 'bg-[var(--status-special-light)]'
    $content = $content -replace 'bg-violet-100', 'bg-[var(--status-special-light)]'
    $content = $content -replace 'text-purple-600', 'text-[var(--status-special)]'
    $content = $content -replace 'text-purple-800', 'text-[var(--status-special-text)]'
    $content = $content -replace 'text-violet-600', 'text-[var(--status-special)]'
    
    # Vervang button kleuren
    $content = $content -replace 'bg-green-600 hover:bg-green-700', 'btn-success'
    $content = $content -replace 'bg-blue-600 hover:bg-blue-700', 'bg-[var(--status-info)] hover:bg-[var(--status-info-dark)]'
    $content = $content -replace 'bg-red-600 hover:bg-red-700', 'btn-danger'
    
    # Vervang transition durations
    $content = $content -replace 'duration-200', 'duration-[var(--transition-normal)]'
    $content = $content -replace 'transition duration-300', 'transition duration-[var(--transition-normal)]'
    
    # Vervang hover states
    $content = $content -replace 'hover:bg-gray-50', 'hover:bg-[var(--background-secondary)]'
    $content = $content -replace 'hover:bg-gray-100', 'hover:bg-[var(--border-medium)]'
    $content = $content -replace 'hover:bg-gray-200', 'hover:bg-[var(--border-medium)]'
    
    # Vervang focus states
    $content = $content -replace 'focus:ring-blue-500', 'focus:ring-[var(--status-info)]'
    $content = $content -replace 'focus:border-blue-500', 'focus:border-[var(--status-info)]'
    
    # Schrijf content terug naar bestand
    Set-Content -Path $file -Value $content -NoNewline
    
    Write-Host "✅ Bijgewerkt: $file" -ForegroundColor Green
}

Write-Host "`n🎉 Migratie voltooid! Alle hardcoded kleuren zijn vervangen door CSS variabelen." -ForegroundColor Green
Write-Host "📝 Backup bestanden zijn gemaakt met .backup extensie" -ForegroundColor Cyan
Write-Host "`n🔍 Controleer de bestanden en verwijder de backup bestanden als alles correct is:" -ForegroundColor Yellow
Write-Host "Get-ChildItem -Path 'resources\views' -Filter '*.backup' -Recurse | Remove-Item" -ForegroundColor Gray
