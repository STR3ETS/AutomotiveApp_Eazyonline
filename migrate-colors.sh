#!/bin/bash

# Script om hardcoded kleuren te vervangen door CSS variabelen
# Dit script updatet alle .blade.php bestanden in de resources/views directory

echo "🎨 Start migratie naar CSS variabelen..."

# Directory waar de blade bestanden staan
VIEWS_DIR="resources/views"

# Zoek alle .blade.php bestanden
find $VIEWS_DIR -name "*.blade.php" -type f | while read file; do
    echo "📝 Bewerk bestand: $file"
    
    # Maak backup
    cp "$file" "$file.backup"
    
    # Vervang achtergrond kleuren
    sed -i 's/bg-gray-50/bg-[var(--background-main)]/g' "$file"
    sed -i 's/bg-white/bg-[var(--background-card)]/g' "$file"
    sed -i 's/bg-gray-100/bg-[var(--background-secondary)]/g' "$file"
    
    # Vervang tekst kleuren
    sed -i 's/text-gray-900/text-[var(--text-primary)]/g' "$file"
    sed -i 's/text-gray-600/text-[var(--text-secondary)]/g' "$file"
    sed -i 's/text-gray-500/text-[var(--text-secondary)]/g' "$file"
    sed -i 's/text-gray-400/text-[var(--text-tertiary)]/g' "$file"
    sed -i 's/text-white/text-[var(--text-white)]/g' "$file"
    
    # Vervang border kleuren
    sed -i 's/border-gray-200/border-[var(--border-light)]/g' "$file"
    sed -i 's/border-gray-300/border-[var(--border-medium)]/g' "$file"
    sed -i 's/divide-gray-200/divide-[var(--border-light)]/g' "$file"
    
    # Vervang status kleuren - Groen (Success)
    sed -i 's/bg-green-100/bg-[var(--status-success-light)]/g' "$file"
    sed -i 's/bg-green-50/bg-[var(--status-success-bg)]/g' "$file"
    sed -i 's/text-green-600/text-[var(--status-success)]/g' "$file"
    sed -i 's/text-green-700/text-[var(--status-success-dark)]/g' "$file"
    sed -i 's/text-green-800/text-[var(--status-success-text)]/g' "$file"
    sed -i 's/text-green-900/text-[var(--status-success-text)]/g' "$file"
    sed -i 's/border-green-200/border-[var(--status-success-border)]/g' "$file"
    sed -i 's/border-green-400/border-[var(--status-success-border)]/g' "$file"
    
    # Vervang status kleuren - Blauw (Info)
    sed -i 's/bg-blue-100/bg-[var(--status-info-light)]/g' "$file"
    sed -i 's/bg-blue-50/bg-[var(--status-info-bg)]/g' "$file"
    sed -i 's/text-blue-600/text-[var(--status-info)]/g' "$file"
    sed -i 's/text-blue-700/text-[var(--status-info-dark)]/g' "$file"
    sed -i 's/text-blue-800/text-[var(--status-info-text)]/g' "$file"
    sed -i 's/text-blue-900/text-[var(--status-info-text)]/g' "$file"
    sed -i 's/border-blue-100/border-[var(--status-info-border)]/g' "$file"
    
    # Vervang status kleuren - Rood (Danger)
    sed -i 's/bg-red-100/bg-[var(--status-danger-light)]/g' "$file"
    sed -i 's/bg-red-50/bg-[var(--status-danger-bg)]/g' "$file"
    sed -i 's/text-red-600/text-[var(--status-danger)]/g' "$file"
    sed -i 's/text-red-700/text-[var(--status-danger-dark)]/g' "$file"
    sed -i 's/text-red-800/text-[var(--status-danger-text)]/g' "$file"
    sed -i 's/text-red-900/text-[var(--status-danger-text)]/g' "$file"
    sed -i 's/border-red-400/border-[var(--status-danger-light)]/g' "$file"
    
    # Vervang status kleuren - Geel/Oranje (Warning)
    sed -i 's/bg-yellow-100/bg-[var(--status-warning-light)]/g' "$file"
    sed -i 's/bg-amber-100/bg-[var(--status-warning-light)]/g' "$file"
    sed -i 's/bg-orange-100/bg-[var(--status-maintenance-light)]/g' "$file"
    sed -i 's/text-yellow-800/text-[var(--status-warning-text)]/g' "$file"
    sed -i 's/text-amber-800/text-[var(--status-warning-text)]/g' "$file"
    sed -i 's/text-orange-600/text-[var(--status-maintenance)]/g' "$file"
    sed -i 's/text-orange-500/text-[var(--status-maintenance)]/g' "$file"
    
    # Vervang status kleuren - Paars (Special)
    sed -i 's/bg-purple-100/bg-[var(--status-special-light)]/g' "$file"
    sed -i 's/bg-violet-100/bg-[var(--status-special-light)]/g' "$file"
    sed -i 's/text-purple-600/text-[var(--status-special)]/g' "$file"
    sed -i 's/text-purple-800/text-[var(--status-special-text)]/g' "$file"
    sed -i 's/text-violet-600/text-[var(--status-special)]/g' "$file"
    
    # Vervang button kleuren
    sed -i 's/bg-green-600 hover:bg-green-700/btn-success/g' "$file"
    sed -i 's/bg-blue-600 hover:bg-blue-700/bg-[var(--status-info)] hover:bg-[var(--status-info-dark)]/g' "$file"
    sed -i 's/bg-red-600 hover:bg-red-700/btn-danger/g' "$file"
    
    # Vervang transition durations
    sed -i 's/duration-200/duration-[var(--transition-normal)]/g' "$file"
    sed -i 's/transition duration-300/transition duration-[var(--transition-normal)]/g' "$file"
    
    # Vervang hover states
    sed -i 's/hover:bg-gray-50/hover:bg-[var(--background-secondary)]/g' "$file"
    sed -i 's/hover:bg-gray-100/hover:bg-[var(--border-medium)]/g' "$file"
    sed -i 's/hover:bg-gray-200/hover:bg-[var(--border-medium)]/g' "$file"
    
    # Vervang focus states
    sed -i 's/focus:ring-blue-500/focus:ring-[var(--status-info)]/g' "$file"
    sed -i 's/focus:border-blue-500/focus:border-[var(--status-info)]/g' "$file"
    
    echo "✅ Bijgewerkt: $file"
done

echo "🎉 Migratie voltooid! Alle hardcoded kleuren zijn vervangen door CSS variabelen."
echo "📝 Backup bestanden zijn gemaakt met .backup extensie"
echo ""
echo "🔍 Controleer de bestanden en verwijder de backup bestanden als alles correct is:"
echo "find resources/views -name '*.backup' -delete"
