# 🎨 Kleurmigratie Instructies

## Wat is er Gedaan?

Ik heb een volledig kleurensysteem opgezet voor je AutomotiveApp met:

### 1. **Gecentraliseerd Kleurenschema** in `resources/css/app.css`
- Alle kleuren zijn nu variabelen
- Semantische namen (success, danger, info, etc.)
- Consistent door het hele project

### 2. **Migratiescripts Gemaakt**
- `migrate-colors.ps1` (voor Windows PowerShell)
- `migrate-colors.sh` (voor Linux/Mac)

## 🚀 Hoe de Scripts Gebruiken?

### Windows (PowerShell):
```powershell
# Open PowerShell in je project directory
cd "c:\laragon\www\AutomotiveApp_Eazyonline"

# Voer het script uit
.\migrate-colors.ps1
```

### Linux/Mac (Bash):
```bash
# Maak het script uitvoerbaar
chmod +x migrate-colors.sh

# Voer het script uit
./migrate-colors.sh
```

## 📋 Wat Doen de Scripts?

De scripts vervangen automatisch alle hardcoded kleuren door CSS variabelen:

### Achtergronden:
- `bg-gray-50` → `bg-[var(--background-main)]`
- `bg-white` → `bg-[var(--background-card)]`
- `bg-gray-100` → `bg-[var(--background-secondary)]`

### Tekst:
- `text-gray-900` → `text-[var(--text-primary)]`
- `text-gray-600` → `text-[var(--text-secondary)]`
- `text-gray-400` → `text-[var(--text-tertiary)]`

### Status Kleuren:
- `bg-green-100` → `bg-[var(--status-success-light)]`
- `text-blue-600` → `text-[var(--status-info)]`
- `text-red-600` → `text-[var(--status-danger)]`
- etc.

### Buttons:
- `bg-green-600 hover:bg-green-700` → `btn-success`
- `bg-blue-600 hover:bg-blue-700` → `bg-[var(--status-info)] hover:bg-[var(--status-info-dark)]`

## 🔒 Veiligheid

- Scripts maken automatisch backup bestanden (`.backup` extensie)
- Je kunt altijd terugdraaien als iets mis gaat
- Test eerst op een paar bestanden

## ✅ Na het Uitvoeren

1. **Controleer de resultaten:**
   - Open een paar views in je editor
   - Kijk of de kleuren correct zijn vervangen

2. **Test je applicatie:**
   - Start je Laravel server
   - Controleer of alles er nog goed uitziet

3. **Verwijder backups als alles OK is:**
   ```powershell
   # Windows
   Get-ChildItem -Path "resources\views" -Filter "*.backup" -Recurse | Remove-Item
   ```
   ```bash
   # Linux/Mac
   find resources/views -name "*.backup" -delete
   ```

## 🎨 Voordelen na Migratie

### Voor Jou:
- **Één plek** om kleuren aan te passen (`app.css`)
- **Consistent** ontwerp door hele app
- **Makkelijk** nieuwe themes maken

### Voorbeeld Kleur Aanpassing:
```css
/* In resources/css/app.css - verander van grijs naar blauw thema */
:root {
    --primary-color: #1e40af;      /* Van grijs naar blauw */
    --primary-hover: #1d4ed8;
    --status-success: #10b981;     /* Blijft hetzelfde */
}
```

## 🆘 Als er Problemen zijn

1. **Backup terugzetten:**
   ```powershell
   # Windows
   Get-ChildItem -Path "resources\views" -Filter "*.backup" -Recurse | ForEach-Object {
       $original = $_.FullName -replace '\.backup$', ''
       Copy-Item -Path $_.FullName -Destination $original -Force
   }
   ```

2. **Manueel aanpassen:**
   - Open de documentatie: `KLEUREN_DOCUMENTATIE.md`
   - Gebruik de quick reference: `KLEUREN_QUICK_REFERENCE.md`

## 📚 Documentatie

- `KLEUREN_DOCUMENTATIE.md` - Volledige uitleg
- `KLEUREN_QUICK_REFERENCE.md` - Snelle referentie voor developers

## 🎯 Resultaat

Na het uitvoeren van de scripts heb je:
- ✅ Alle hardcoded kleuren vervangen
- ✅ Consistent kleurenschema
- ✅ Makkelijk aanpasbare kleuren
- ✅ Professioneel onderhoudbaar systeem

**Succes met je kleuren migratie! 🎨**
