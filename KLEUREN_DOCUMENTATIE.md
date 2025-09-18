# 🎨 Kleuren Documentatie - AutomotiveApp

## Overzicht
Alle kleuren in dit project zijn nu gecentraliseerd in CSS variabelen in `resources/css/app.css`. Dit maakt het super eenvoudig om kleuren aan te passen zonder door het hele project te zoeken.

## 🎯 Hoofdkleuren

### Primaire Kleuren
```css
--primary-color: #2a2f37        /* Hoofdkleur (sidebar, knoppen) */
--primary-hover: #373d47        /* Hover staat van primaire kleur */
--primary-light: #0d0e11        /* Donkere variant */
--primary-lighter: rgba(42, 47, 55, 0.3)  /* Transparante variant */
```

### Achtergrond Kleuren
```css
--background-main: #f9fafb      /* Hoofdachtergrond (pagina's) */
--background-card: #ffffff      /* Kaarten/widgets achtergrond */
--background-secondary: #f3f4f6 /* Secundaire achtergrond */
--background-hover: rgba(13, 14, 17, 0.5)  /* Hover achtergrond */
```

### Tekst Kleuren
```css
--text-primary: #111827         /* Hoofdtekst (titels, belangrijke tekst) */
--text-secondary: #6b7280       /* Secundaire tekst (beschrijvingen) */
--text-tertiary: #9ca3af        /* Tertiaire tekst (subtekst) */
--text-white: #ffffff           /* Witte tekst */
--text-white-dimmed: rgba(255, 255, 255, 0.5)  /* Gedempte witte tekst */
--text-white-muted: rgba(255, 255, 255, 0.8)   /* Gemute witte tekst */
```

## 🚦 Status Kleuren

### Succes (Groen)
```css
--status-success: #10b981           /* Basis groen */
--status-success-light: #d1fae5     /* Lichte achtergrond */
--status-success-dark: #047857      /* Donkere variant */
--status-success-text: #065f46      /* Tekst kleur */
--status-success-bg: #f0fdf4        /* Badge achtergrond */
--status-success-border: #bbf7d0    /* Border kleur */
```

### Informatie (Blauw)
```css
--status-info: #3b82f6              /* Basis blauw */
--status-info-light: #dbeafe        /* Lichte achtergrond */
--status-info-dark: #1d4ed8         /* Donkere variant */
--status-info-text: #1e40af         /* Tekst kleur */
--status-info-bg: #eff6ff           /* Badge achtergrond */
--status-info-border: #93c5fd       /* Border kleur */
```

### Waarschuwing (Oranje/Geel)
```css
--status-warning: #f59e0b           /* Basis oranje/geel */
--status-warning-light: #fef3c7     /* Lichte achtergrond */
--status-warning-dark: #d97706      /* Donkere variant */
--status-warning-text: #92400e      /* Tekst kleur */
--status-warning-bg: #fffbeb        /* Badge achtergrond */
```

### Gevaar (Rood)
```css
--status-danger: #ef4444            /* Basis rood */
--status-danger-light: #fecaca      /* Lichte achtergrond */
--status-danger-dark: #dc2626       /* Donkere variant */
--status-danger-text: #991b1b       /* Tekst kleur */
--status-danger-bg: #fef2f2         /* Badge achtergrond */
```

### Speciaal (Paars)
```css
--status-special: #8b5cf6           /* Basis paars */
--status-special-light: #ede9fe     /* Lichte achtergrond */
--status-special-dark: #7c3aed      /* Donkere variant */
--status-special-text: #5b21b6      /* Tekst kleur */
```

### Onderhoud (Oranje)
```css
--status-maintenance: #f97316       /* Basis oranje */
--status-maintenance-light: #fed7aa /* Lichte achtergrond */
--status-maintenance-text: #ea580c  /* Tekst kleur */
```

## 🎛️ Hoe Kleuren Aanpassen?

### 1. Centrale Aanpassing
Om kleuren project-breed aan te passen, wijzig je de variabelen in `resources/css/app.css`:

```css
:root {
    /* Verander bijvoorbeeld de primaire kleur */
    --primary-color: #1e40af;  /* Van grijs naar blauw */
    
    /* Of pas een status kleur aan */
    --status-success: #059669; /* Nieuwe groen tint */
}
```

### 2. Gebruik in Templates
In je Blade templates gebruik je de variabelen zo:

```html
<!-- Oude manier -->
<div class="bg-blue-500 text-white">...</div>

<!-- Nieuwe manier met variabelen -->
<div class="bg-[var(--status-info)] text-[var(--text-white)]">...</div>

<!-- Of gebruik de utility classes -->
<div class="card-base badge-info">...</div>
```

## 🛠️ Utility Classes

Voor veel gebruikte combinaties zijn er utility classes beschikbaar:

### Cards
```css
.card-base        /* Basis kaart styling */
.card-hover       /* Hover effect voor kaarten */
```

### Badges/Labels
```css
.badge-success    /* Groene badge */
.badge-info       /* Blauwe badge */
.badge-warning    /* Oranje/gele badge */
.badge-danger     /* Rode badge */
.badge-special    /* Paarse badge */
.badge-maintenance /* Oranje badge voor onderhoud */
```

### Buttons
```css
.btn-primary      /* Primaire knop */
.btn-success      /* Groene knop */
.btn-danger       /* Rode knop */
```

### Text
```css
.text-heading     /* Voor titels en kopjes */
.text-body        /* Voor gewone tekst */
.text-muted       /* Voor subtekst */
```

### Backgrounds
```css
.bg-main          /* Hoofdachtergrond */
.bg-card          /* Kaart achtergrond */
.bg-secondary     /* Secundaire achtergrond */
```

## 📋 Voorbeelden van Gebruik

### Dashboard Kaart
```html
<div class="card-base card-hover p-6">
    <h3 class="text-heading">Totaal Auto's</h3>
    <p class="text-body">Beschrijving hier</p>
    <span class="badge-info">Nieuw</span>
</div>
```

### Status Indicator
```html
<!-- Gebruikt automatisch de juiste kleuren -->
<div class="bg-[var(--status-success-light)] text-[var(--status-success-text)] p-2 rounded">
    ✅ Voltooid
</div>
```

### Navigatie Item
```html
<a href="/dashboard" class="text-[var(--text-white)] hover:bg-[var(--primary-lighter)]">
    Dashboard
</a>
```

## 🎨 Kleurenschema Aanpassen

### Voorbeeld: Van Grijs naar Blauw Thema
```css
:root {
    /* Wijzig primaire kleuren */
    --primary-color: #1e40af;      /* blue-700 */
    --primary-hover: #1d4ed8;      /* blue-600 */
    --primary-light: #1e3a8a;      /* blue-800 */
    --primary-lighter: rgba(30, 64, 175, 0.3);
}
```

### Voorbeeld: Van Grijs naar Groen Thema
```css
:root {
    /* Wijzig primaire kleuren */
    --primary-color: #047857;      /* emerald-700 */
    --primary-hover: #059669;      /* emerald-600 */
    --primary-light: #064e3b;      /* emerald-800 */
    --primary-lighter: rgba(4, 120, 87, 0.3);
}
```

## 🔧 Tips voor Developers

1. **Consistentie**: Gebruik altijd de variabelen in plaats van hardcoded kleuren
2. **Semantiek**: Kies de juiste status kleur voor de context (success voor positieve acties, danger voor negatieve, etc.)
3. **Toegankelijkheid**: Zorg dat er voldoende contrast is tussen tekst en achtergrond
4. **Testing**: Test kleurwijzigingen op verschillende schermen en in verschillende browsers

## 📱 Responsive Overwegingen

De kleurvariabelen werken automatisch op alle schermformaten. Voor dark mode ondersteuning kun je media queries toevoegen:

```css
@media (prefers-color-scheme: dark) {
    :root {
        --background-main: #1f2937;
        --background-card: #374151;
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        /* etc... */
    }
}
```

---

**🎯 Resultaat**: Met dit systeem kun je met één wijziging in `app.css` het hele kleurenschema van je applicatie aanpassen!
