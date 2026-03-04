# Importation Service Refactoring Plan

## Overview
Wrap the entire import process in a single database transaction with French error messages and proper use of `CotisationMensuelle` as a staging table.

## Changes Required

### 1. ImportationController.php

Replace the entire `cotisation()` method to:
- Wrap the entire import process in `DB::transaction()`
- Validate data before creating staging records
- Throw exceptions with French error messages
- Pass staging records to the service
- Handle all errors at controller level

**Key improvements:**
- Singular transaction for all operations
- French error handling: "La cotisation pour cette date existe déjà", "Une erreur s'est produite lors de l'importation"
- Validation before staging
- Staging records collected and passed to service

### 2. ImportationService.php

Update three methods:

#### a) `processImport()` method
- Accept array of staging records instead of raw request data
- Remove nested transactions (now handled at controller level)
- Process each staging record through the workflow
- Call `processCotisation()` to move data to permanent table

#### b) Add `CotisationMensuelle` import
- Add to use statements: `use App\Models\CotisationMensuelle;`

#### c) Add new `processCotisation()` method
- Creates permanent `Cotisation` records from staging data
- Maps fields: `regle`, `restant`, `global`, `retenu`
- Sets `statut` to 'partiel' if `montant_restant > 0`, else 'paye'
- Uses `date_cotisation` as end of month

## Data Flow

```
Controller:
  1. Validate date not exists (throw if exists)
  2. Validate data entries
  3. Create CotisationMensuelle staging records
  4. Pass to service

Service:
  1. For each staging record:
     - Resolve/create Membre
     - Create/update Credit if retenu > 0
     - Create Remboursement
     - Create Cotisation from staging data

Complete or Fail:
  - If any step fails → entire transaction rolls back
  - All errors are French-formatted
```

## Error Messages (French)
- "La cotisation pour cette date existe déjà" (409)
- "Aucune entrée valide trouvée dans les données d'importation" (400)
- "Erreur lors de l'importation: {message}" (500)

## Files to Update
1. `app/Http/Controllers/ImportationController.php`
2. `app/Services/ImportationService.php`
