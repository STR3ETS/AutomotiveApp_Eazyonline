# Rol-gebaseerd Authenticatie Systeem

## Overzicht
Het AutomotiveApp systeem heeft nu een volledig rol-gebaseerd authenticatie systeem dat verschillende toegangsniveaus biedt voor verschillende gebruikerstypen.

## Rollen

### 1. Owner (Eigenaar)
- **Volledige toegang** tot alle functies
- Kan bedrijfsinstellingen wijzigen
- Kan medewerkers beheren (aanmaken, bewerken, verwijderen)
- Kan auto's beheren en toewijzen
- Kan rapporten bekijken
- Kan klanten beheren

### 2. Manager (Voorman)
- **Bijna volledige toegang** behalve bedrijfsinstellingen
- Kan medewerkers beheren
- Kan auto's beheren en toewijzen
- Kan rapporten bekijken
- Kan klanten beheren
- Kan NIET bedrijfsinstellingen wijzigen

### 3. Employee (Medewerker)
- **Werk-gerelateerde toegang** - kunnen hun werk uitvoeren
- Toegang tot: Overzicht, Agenda, Voorraad Pipeline, Auto Beheer, Reparaties, Verkoop Klaar, Klaar voor Oplevering
- Toegang tot "Mijn Werk" (eigen profiel)
- Kan eigen werk afronden en status bijwerken
- Kan GEEN klanten beheren
- Kan GEEN andere medewerkers beheren  
- Kan GEEN auto's toewijzen aan anderen
- Kan GEEN verkoop/leveringen beheren
- Heeft GEEN toegang tot gevoelige rapportages

## Inloggegevens (voor testing)

### Eigenaren
- **AutoGarage Piet**: piet@autogaragepiet.nl / password123
- **De Snelle Garage**: mark@desnellegarage.nl / password123  
- **Premium Motors**: lisa@premiummotors.nl / password123
- **Buurgarage Jan**: jan@buurgaragejan.nl / password123

### Voormannen
- **Voorman**: voorman@[bedrijf].nl / password123

### Medewerkers
- **Medewerkers**: [naam]@[bedrijf].nl / password123

## Toegangstabel per Rol

| Functie | Owner | Manager | Employee |
|---------|-------|---------|----------|
| **Dashboard/Overzicht** | ✅ | ✅ | ✅ |
| **Agenda** | ✅ | ✅ | ✅ |
| **Voorraad Pipeline** | ✅ | ✅ | ✅ |
| **Auto Beheer** | ✅ | ✅ | ✅ |
| **Reparaties** | ✅ | ✅ | ✅ |
| **Verkoop Klaar** | ✅ | ✅ | ✅ |
| **Klaar voor Oplevering** | ✅ | ✅ | ✅ |
| **Mijn Werk** | ✅ | ✅ | ✅ |
| | | | |
| **Klanten Beheer** | ✅ | ✅ | ❌ |
| **Medewerkers Beheer** | ✅ | ✅ | ❌ |
| **Verkoop Management** | ✅ | ✅ | ❌ |
| **Auto's Toewijzen** | ✅ | ✅ | ❌ |
| **Bedrijfsinstellingen** | ✅ | ❌ | ❌ |
| **Financiële Rapporten** | ✅ | ✅ | ❌ |

## Nieuwe Features

### 1. Gebruikersaccounts voor Medewerkers
- Bij het aanmaken van een medewerker kun je optioneel een gebruikersaccount aanmaken
- Kies tussen "Medewerker" of "Voorman" rol
- Automatisch gegenereerde email adressen
- Standaard wachtwoord: password123

### 2. Dynamische Navigatie
- **Eigenaar/Voorman**: Ziet volledige navigatie
- **Medewerker**: Ziet alleen "Mijn Werk" menu-item

### 3. Permission Systeem
- Laravel Gates voor fine-grained toegangscontrole
- Middleware bescherming op routes
- View-level permission checks

### 4. Automatische Redirects
- Medewerkers worden automatisch doorgestuurd naar hun eigen profiel
- Toegang tot andermans gegevens wordt geblokkeerd

## Technische Implementatie

### Database Changes
```sql
-- Nieuwe velden in users tabel
ALTER TABLE users ADD COLUMN role ENUM('owner','manager','employee') DEFAULT 'employee';
ALTER TABLE users ADD COLUMN active BOOLEAN DEFAULT true;
ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;

-- Nieuwe velden in employees tabel  
ALTER TABLE employees ADD COLUMN user_id BIGINT UNSIGNED NULL;
ALTER TABLE employees ADD COLUMN role ENUM('owner','manager','employee') DEFAULT 'employee';
ALTER TABLE employees ADD COLUMN last_login_at TIMESTAMP NULL;
```

### Middleware
- `AuthMiddleware`: Controleert Laravel authenticatie
- `PermissionMiddleware`: Controleert specifieke permissions
- `TenantMiddleware`: Handhaaft company isolation

### Permissions
- `manage.company`: Alleen eigenaar
- `manage.employees`: Eigenaar + Voorman
- `manage.cars`: Eigenaar + Voorman  
- `view.reports`: Eigenaar + Voorman
- `assign.cars`: Eigenaar + Voorman

## Gebruik

### Een Medewerker Toevoegen met Login
1. Ga naar Medewerkers → Nieuwe Medewerker
2. Vul alle vereiste gegevens in
3. Vink "Maak een login account aan" aan
4. Kies de juiste rol (Medewerker/Voorman)
5. Opslaan

### Inloggen als Medewerker
1. Gebruik email: [naam]@[bedrijf].nl
2. Wachtwoord: password123
3. Je wordt automatisch doorgestuurd naar je eigen werkpagina

### Rechten Controleren
```php
// In een controller
if (Auth::user()->can('manage.employees')) {
    // Gebruiker mag medewerkers beheren
}

// In een Blade view  
@can('manage.cars')
    <a href="{{ route('autos.create') }}">Nieuwe Auto</a>
@endcan
```

## Voordelen van dit Systeem

✅ **Veiligheid**: Echte wachtwoorden ipv hardcoded credentials  
✅ **Schaalbaarheid**: Makkelijk nieuwe rollen toevoegen  
✅ **Audit Trail**: Tracking van wie wat doet  
✅ **Gebruiksvriendelijk**: Intuïtieve interface per rol  
✅ **Laravel Best Practices**: Gebruikt Laravel's ingebouwde auth systeem  

## Toekomstige Uitbreidingen

- **Password Reset Functionaliteit**
- **Email Verificatie**  
- **Two-Factor Authentication**
- **Meer Granulaire Permissions**
- **Audit Logging**
- **API Tokens voor Mobile Apps**
