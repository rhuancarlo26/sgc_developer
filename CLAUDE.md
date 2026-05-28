# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SGC Developer** — Sistema de Gestão de Contratos Ambientais (Environmental Contract Management System) v1.4.0. Built for managing environmental contracts, licenses, fauna/flora monitoring, and geospatial data related to DNIT (Brazilian road infrastructure agency) projects.

## Tech Stack

- **Backend**: PHP 8.1+ / Laravel 10
- **Frontend**: Vue 3 + Inertia.js (server-driven SPA)
- **Build**: Vite + npm
- **Database**: MySQL (`ecosistema` / `ecosistema_2025`)
- **UI**: Bootstrap 5 (Tabler Dashboard theme)
- **Maps**: Leaflet.js + GeoServer integration
- **Auth**: Laravel Sanctum + Spatie Permission (role/permission system)

## Common Commands

```bash
# Development
php artisan serve          # Backend on :8000
npm run dev                # Vite HMR on :5173
npm run dev-docker         # Vite binding 0.0.0.0:5173 (inside Docker)

# Build
composer install
npm install
npm run build

# Database
php artisan migrate
php artisan db:seed
php artisan create:super-admin   # Create initial Super Admin user

# Testing
php artisan test
php artisan test tests/Feature
php artisan test tests/Unit

# Code style (PHP)
./vendor/bin/pint

# Custom generators
php artisan make:model-service {Namespace}    # Create a service class
php artisan make:vue-page-component {Name}   # Create Vue page component
```

## Docker

```bash
docker-compose up -d
# Services: ecossistema (app, ports 8000/5173), mysql (port 3307→3306)
# Network: ecossistema bridge
```

## Architecture

### Backend — Domain-Driven Structure

```
app/
├── Domain/
│   ├── Contrato/        # Contract management
│   ├── Fiscal/          # Fiscal oversight
│   ├── Licenca/         # Environmental licenses
│   ├── Dashboard/       # Analytics
│   └── Servico/         # Environmental services (main domain)
│       ├── AfugentamentoResgateFauna/   # Wildlife rescue
│       ├── PassagemFauna/               # Wildlife crossings
│       ├── MonitoraFauna/               # Fauna monitoring
│       ├── MonAtpFauna/                 # ATP fauna monitoring
│       ├── PMQA/                        # Water quality
│       ├── SupressaoVegetacao/          # Vegetation suppression
│       ├── SupervisaoAmbiental/         # Environmental supervision
│       └── ContOcorrencia/              # Contract occurrences
└── Shared/
    ├── Base/            # User, Auth, Profile, Role models
    ├── Http/            # Controllers, Requests, Middleware
    ├── Providers/       # Service providers
    ├── Console/         # Artisan commands
    ├── Integrations/    # External APIs (Google Routes, DNIT geo)
    └── Utils/, Traits/, Exceptions/
```

Each domain follows the pattern: `Model → Service → Controller → Request → Resource`.

### Frontend — Inertia.js Pages

```
resources/js/
├── Pages/           # Inertia page components (mirrors backend routes)
├── Components/      # Shared UI components
├── Layouts/         # AuthenticatedLayout, GuestLayout
├── Composables/     # Vue composables
└── Utils/           # permissions, axios, datetime, strings helpers
```

Pages are organized by domain matching the backend structure. Inertia handles routing — there is no Vue Router.

### SGC Module (Sgc domain)

Special sub-system at `app/Domain/Sgc/` containing:
- **Espeleologia**: Speleology/cave study documentation
- **Fauna**: Fauna-specific SGC tracking
- **PMQA**: Water quality within SGC
- **Dav**: DAV module

### Routing & Permissions

- `routes/web.php`: Main web routes, grouped by middleware `route-permission` (dynamic permission checking derived from route names)
- `routes/api.php`: Sanctum-protected API (`POST /auth_token` for 7-day tokens, `GET /user`)
- `routes/auth.php`: Standard Laravel auth scaffolding
- Permissions are generated from route names and assigned to Spatie roles
- Email verification required by default

### Geospatial

- `config/api-geo.php`: DNIT geospatial API endpoints
- `config/geoserver.php`: GeoServer WMS/WFS layer configuration
- Frontend: Leaflet.js with geoman (drawing), shapefile import, Turf.js for spatial operations

## Key Configuration

| File | Purpose |
|------|---------|
| `config/permission.php` | Spatie role/permission setup |
| `config/api-geo.php` | DNIT geo API endpoints |
| `config/geoserver.php` | GeoServer layer config |
| `database/migrations/old/` | Archived legacy migrations |

## Environment Variables

Critical `.env` keys beyond standard Laravel:
- `API_GEO_URL` — DNIT geospatial API base URL
- `API_SGPLAN_URL` — SGPLAN API endpoint
- `VITE_*` — Frontend env vars exposed to Vue

## Development Notes

- PHP namespace root is `App\` but domains use `App\Domain\{DomainName}\` and shared code uses `App\Shared\`
- Bootstrap 5 pagination is pre-configured via `AppServiceProvider`
- Portuguese (pt-BR) localization is installed
- Legacy migrations live in `database/migrations/old/` — do not touch these
- HTTPS is enforced in production via `AppServiceProvider`
