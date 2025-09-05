# 🎨 Kleuren Quick Reference

## Meest Gebruikte Variabelen

### 🏠 Layout
```css
bg-[var(--background-main)]       /* Pagina achtergrond */
bg-[var(--background-card)]       /* Kaart/widget achtergrond */
border-[var(--border-light)]      /* Border kleur */
```

### 📝 Tekst
```css
text-[var(--text-primary)]        /* Hoofdtekst (titels) */
text-[var(--text-secondary)]      /* Gewone tekst */
text-[var(--text-tertiary)]       /* Subtekst */
text-[var(--text-white)]          /* Witte tekst */
```

### 🎯 Sidebar/Navigatie
```css
bg-[var(--primary-color)]         /* Sidebar achtergrond */
text-[var(--text-white)]          /* Tekst in sidebar */
hover:bg-[var(--primary-lighter)] /* Hover effect */
```

### ✅ Status Colors
```css
/* Succes/Groen */
bg-[var(--status-success-light)]  /* Lichte achtergrond */
text-[var(--status-success)]      /* Icoon kleur */
text-[var(--status-success-text)] /* Tekst kleur */

/* Info/Blauw */
bg-[var(--status-info-light)]     /* Lichte achtergrond */
text-[var(--status-info)]         /* Icoon kleur */
text-[var(--status-info-text)]    /* Tekst kleur */

/* Waarschuwing/Oranje */
bg-[var(--status-warning-light)]  /* Lichte achtergrond */
text-[var(--status-warning)]      /* Icoon kleur */

/* Gevaar/Rood */
bg-[var(--status-danger-light)]   /* Lichte achtergrond */
text-[var(--status-danger)]       /* Icoon kleur */

/* Speciaal/Paars */
bg-[var(--status-special-light)]  /* Lichte achtergrond */
text-[var(--status-special)]      /* Icoon kleur */

/* Onderhoud/Oranje */
bg-[var(--status-maintenance-light)] /* Lichte achtergrond */
text-[var(--status-maintenance)]     /* Icoon kleur */
```

### 🎨 Gradients
```css
/* Blauwe gradient */
from-[var(--gradient-blue-start)] to-[var(--gradient-blue-end)]

/* Groene gradient */
from-[var(--gradient-green-start)] to-[var(--gradient-green-end)]
```

### ⏱️ Transitions
```css
duration-[var(--transition-fast)]    /* 150ms */
duration-[var(--transition-normal)]  /* 300ms */
```

### 📐 Border Radius
```css
rounded-[var(--border-radius)]       /* 4px - kleine radius */
rounded-[var(--border-radius-large)] /* 12px - grote radius */
```

## 🔧 Utility Classes

### Cards
```html
<div class="card-base card-hover">...</div>
```

### Badges
```html
<span class="badge-success">Voltooid</span>
<span class="badge-info">Info</span>
<span class="badge-warning">Let op</span>
<span class="badge-danger">Fout</span>
<span class="badge-special">Special</span>
<span class="badge-maintenance">Onderhoud</span>
```

### Buttons
```html
<button class="btn-primary">Primair</button>
<button class="btn-success">Opslaan</button>
<button class="btn-danger">Verwijderen</button>
```

### Backgrounds
```html
<div class="bg-main">...</div>     <!-- Hoofdachtergrond -->
<div class="bg-card">...</div>     <!-- Kaart achtergrond -->
<div class="bg-secondary">...</div> <!-- Secundaire achtergrond -->
```

### Text
```html
<h1 class="text-heading">Titel</h1>
<p class="text-body">Gewone tekst</p>
<span class="text-muted">Subtekst</span>
```

## 🚀 Snelle Kleur Aanpassingen

### Wijzig Hoofdthema
Open `resources/css/app.css` en pas aan:
```css
:root {
    --primary-color: #jouw-nieuwe-kleur;
    --primary-hover: #jouw-hover-kleur;
}
```

### Wijzig Status Kleur
```css
:root {
    --status-success: #jouw-groene-kleur;
    --status-info: #jouw-blauwe-kleur;
    /* etc... */
}
```

### Donker Thema Toevoegen
```css
@media (prefers-color-scheme: dark) {
    :root {
        --background-main: #1f2937;
        --background-card: #374151;
        --text-primary: #f9fafb;
        /* etc... */
    }
}
```

---
💡 **Tip**: Gebruik altijd CSS variabelen in plaats van hardcoded kleuren!
