BMPOS – Arbeidsplan (Master Plan)
Mål

Bygge en enkel, trygg og ryddig app for:

lager / “ting jeg eier”

salg/avtaler med kunder

avdrag/innbetalinger

utestående per kunde

kundeportal (read-only)

Teknologi (låst)

Backend: Laravel 11

Frontend: Blade

Auth: Laravel Breeze (Blade)

DB lokalt: PostgreSQL (installert lokalt + pgAdmin)

DB prod: Neon Postgres (SSL/TLS)

Hosting senere: Laravel Cloud (anbefalt)

PHP: 8.3 (stabilt og enkelt)

Ingen Docker

Fase 0 – Prosjektsetup (foundation)

Leveranse: App kjører lokalt med Postgres og kan migrere.

Tasks

Opprett Laravel-prosjekt

Sett .env til pgsql (lokal Postgres)

Kjør php artisan migrate (baseline)

Sett opp git repo + push til GitHub

Definisjon av ferdig

App åpner

DB-tilkobling fungerer

Migrations kjører uten feil

Fase 1 – Auth og roller (Fase 2 hos deg nå)

Leveranse: Admin og customer kan logge inn, riktig redirect og tilgang.

Tasks

Installer Breeze (Blade)

Legg til role i users:

admin

customer (default)

Lag AdminUserSeeder (oppretter deg som admin)

Lag admin-middleware:

blokkerer alle ikke-admin fra /admin/\*

Routing:

/admin (kun admin)

/dashboard (kunde)

Definisjon av ferdig

Du kan logge inn som admin og se admin dashboard

Kunde kan logge inn og blir sendt til kunde-dashboard

Kunde får 403/redirect hvis de prøver /admin

Fase 2 – Datamodell (MVP kjerne)

Leveranse: Tabeller + relasjoner + beregning av utestående.

Domene (låst)

Kunde = users med role customer

Admin = users med role admin

Varer er unike (typisk bruktmarked)

Tabeller (MVP)
items (lager)

id

name (string)

description (text, nullable)

purchase_price (integer, nullable) (i øre)

target_price (integer, nullable) (i øre)

status (string/enum): available, reserved, sold, archived

timestamps

orders

id

customer_id (FK → users.id)

status: open, closed, cancelled

total_amount (integer) (i øre; kan overstyres)

notes (text nullable)

timestamps

order_lines

id

order_id (FK)

item_id (FK)

unit_price (integer) (i øre)

quantity (int default 1)

payments

id

order_id (FK)

amount (integer) (i øre)

paid_at (date)

note (string nullable)

Regler (låst)

Utestående per ordre:

outstanding = total_amount - sum(payments.amount)

Kunde skal kun kunne se egne ordre/data

Admin kan endre alt

Definisjon av ferdig

migrations kjører

seed/testdata kan opprettes

order outstanding kan beregnes

Fase 3 – Autorisasjon (Policies + sikker dataflyt)

Leveranse: “Ingen data lekker”.

Tasks

OrderPolicy:

admin: alltid true

customer: order.customer_id === user.id

Henting av data skjer via relasjoner:

auth()->user()->orders()...

Sikre at order_lines og payments hentes via order, ikke “fri query”

Definisjon av ferdig

Kunde kan ikke åpne /orders/{id} for en annen kunde (403)

Admin kan åpne alt

Fase 4 – Admin panel (CRUD + arbeidsflyt)

Leveranse: Du kan faktisk bruke systemet.

Admin-sider (minimum)
Customers

Liste kunder (users role=customer)

Opprett kunde (admin oppretter)

Kundedetalj:

total kjøpt

total betalt

total utestående

liste med ordre

Items

Liste items

Opprett item

Edit item

Endre status (available/reserved/sold)

Orders

Liste ordre (filter: open/closed)

Opprett ordre (velg kunde)

Ordredetalj:

legg til order lines (velg item + pris)

registrer betaling (amount + dato)

vis outstanding

lukk ordre

Business rule (praktisk)

Når item legges til i order:

status settes til reserved

Når ordre lukkes:

items settes sold (hvis du vil, eller behold reserved/sold logikk enkel)

Definisjon av ferdig

Du kan opprette kunde → item → ordre → betaling

Dashboard viser korrekt outstanding

Fase 5 – Kundeportal (read-only)

Leveranse: Kunden ser egen oversikt.

Kunde-sider

/dashboard:

total utestående

open orders

/orders:

liste med ordre

/orders/{order}:

varer (lines)

betalinger

total / paid / outstanding

Definisjon av ferdig

Kunde kan logge inn og forstå alt

Ingen endring mulig

Fase 6 – Landing page + navigasjon

Leveranse: En enkel landing som peker riktig.

/ landing (public)

“Logg inn” (for kunder)

“Admin login” (samme login, men admin får admin dashboard etterpå)

Etter login:

admin → /admin

customer → /dashboard

Fase 7 – Kvalitet (MVP-sikkerhet og stabilitet)

Leveranse: Ikke fancy, men solid.

Minimum

Validering på all input (Form Requests)

Flash-messages for success/error

Beskyttelse mot:

mass assignment (fillable/guarded)

uautoriserte routes (middleware + policies)

Enkle feature-tests:

kunde kan ikke se andres ordre

admin kan

Fase 8 – Prod: Neon + Laravel Cloud (når MVP er ferdig lokalt)

Leveranse: Live app med kundeprofiler.

Neon (prod DB)

Lag DB i Neon

Kopier connection string

SSL/TLS aktivt (Neon krever det)

Laravel Cloud

Koble repo fra GitHub

Sett env vars (DB + APP_KEY + APP_URL)

Kjør migrations i prod

Opprett admin i prod (seeder eller tinker)

Definisjon av ferdig

Kunden kan logge inn via live URL

Data ligger i Neon

Ingen Docker nødvendig
