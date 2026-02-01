# Design Document — BMPOS MVP

## Overview

BMPOS is a Laravel 11 application using Blade templates, Tailwind CSS, and Alpine.js for minimal interactivity. The design prioritizes clarity, consistency, and security while maintaining a modern, professional aesthetic.

---

## Architecture

### Tech Stack
- **Backend:** Laravel 11 (PHP 8.3)
- **Frontend:** Blade templates + Tailwind CSS + Alpine.js
- **Auth:** Laravel Breeze (Blade variant)
- **Database:** PostgreSQL (local + Neon prod)
- **Deployment:** Laravel Cloud + GitHub

### Application Structure
```
BMPOS
├── Public Routes (/)
│   └── Landing page + Login
├── Customer Routes (/dashboard, /orders)
│   └── Read-only portal (auth required)
└── Admin Routes (/admin/*)
    └── Full CRUD (auth + admin middleware)
```

---

## Frontend Design System

### Design Principles

1. **Clarity over Cleverness** — Information should be immediately understandable
2. **Consistency** — Same patterns, same spacing, same interactions everywhere
3. **Responsive First** — Mobile to desktop, not desktop to mobile
4. **Minimal Animation** — Subtle, purposeful, never distracting
5. **Accessible** — Proper contrast, keyboard navigation, screen reader friendly

### Color Palette

**Primary Colors:**
```css
--primary-50:  #eff6ff;  /* Lightest blue */
--primary-100: #dbeafe;
--primary-500: #3b82f6;  /* Main brand blue */
--primary-600: #2563eb;  /* Hover state */
--primary-700: #1d4ed8;  /* Active state */
```

**Semantic Colors:**
```css
--success-50:  #f0fdf4;
--success-500: #22c55e;  /* Green for success, available, closed */
--success-600: #16a34a;

--warning-50:  #fffbeb;
--warning-500: #f59e0b;  /* Yellow for reserved, pending */
--warning-600: #d97706;

--danger-50:   #fef2f2;
--danger-500:  #ef4444;  /* Red for errors, cancelled */
--danger-600:  #dc2626;

--gray-50:     #f9fafb;
--gray-100:    #f3f4f6;
--gray-200:    #e5e7eb;
--gray-300:    #d1d5db;
--gray-500:    #6b7280;  /* Body text */
--gray-700:    #374151;  /* Headings */
--gray-900:    #111827;  /* Dark text */
```

### Typography

**Font Stack:**
```css
font-family: 'Inter', system-ui, -apple-system, sans-serif;
```

**Scale:**
```css
--text-xs:   0.75rem;  /* 12px - Small labels */
--text-sm:   0.875rem; /* 14px - Body text, table cells */
--text-base: 1rem;     /* 16px - Default body */
--text-lg:   1.125rem; /* 18px - Subheadings */
--text-xl:   1.25rem;  /* 20px - Card titles */
--text-2xl:  1.5rem;   /* 24px - Page titles */
--text-3xl:  1.875rem; /* 30px - Hero headlines */
--text-4xl:  2.25rem;  /* 36px - Landing page hero */
```

**Weights:**
- Normal: 400 (body text)
- Medium: 500 (labels, buttons)
- Semibold: 600 (headings, emphasis)
- Bold: 700 (hero text, important CTAs)

### Spacing System

**Consistent spacing scale (Tailwind default):**
```
1  = 0.25rem (4px)
2  = 0.5rem  (8px)
3  = 0.75rem (12px)
4  = 1rem    (16px)
6  = 1.5rem  (24px)
8  = 2rem    (32px)
12 = 3rem    (48px)
16 = 4rem    (64px)
```

**Usage:**
- Card padding: `p-6` (24px)
- Section spacing: `space-y-8` (32px between sections)
- Form field spacing: `space-y-4` (16px between fields)
- Button padding: `px-4 py-2` (16px horizontal, 8px vertical)

### Components

#### Buttons

**Primary Button:**
```html
<button class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg 
               hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 
               transition-colors duration-150">
    Button Text
</button>
```

**Secondary Button:**
```html
<button class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300
               hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 
               transition-colors duration-150">
    Button Text
</button>
```

**Danger Button:**
```html
<button class="px-4 py-2 bg-danger-600 text-white font-medium rounded-lg 
               hover:bg-danger-700 focus:ring-4 focus:ring-danger-200 
               transition-colors duration-150">
    Delete
</button>
```

#### Cards

**Standard Card:**
```html
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-xl font-semibold text-gray-900 mb-4">Card Title</h3>
    <div class="space-y-4">
        <!-- Card content -->
    </div>
</div>
```

**Summary Card (Dashboard):**
```html
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Label</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">150,00 kr</p>
        </div>
        <div class="p-3 bg-primary-50 rounded-lg">
            <!-- Icon -->
        </div>
    </div>
</div>
```

#### Status Badges

```html
<!-- Available / Open -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
             bg-success-100 text-success-800">
    Available
</span>

<!-- Reserved / Pending -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
             bg-warning-100 text-warning-800">
    Reserved
</span>

<!-- Sold / Closed -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
             bg-gray-100 text-gray-800">
    Sold
</span>

<!-- Cancelled / Error -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
             bg-danger-100 text-danger-800">
    Cancelled
</span>
```

#### Tables

```html
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Column Header
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    Cell Content
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

#### Forms

```html
<div class="space-y-4">
    <div>
        <label for="field" class="block text-sm font-medium text-gray-700 mb-1">
            Field Label
        </label>
        <input type="text" id="field" name="field"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg 
                      focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                      transition-colors duration-150"
               placeholder="Placeholder text">
        <!-- Error state -->
        <p class="mt-1 text-sm text-danger-600">Error message</p>
    </div>
</div>
```

#### Alerts

```html
<!-- Success -->
<div class="p-4 bg-success-50 border border-success-200 rounded-lg">
    <div class="flex">
        <div class="flex-shrink-0">
            <!-- Success icon -->
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-success-800">Success message</p>
        </div>
    </div>
</div>

<!-- Warning -->
<div class="p-4 bg-warning-50 border border-warning-200 rounded-lg">
    <div class="flex">
        <div class="flex-shrink-0">
            <!-- Warning icon -->
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-warning-800">Warning message</p>
        </div>
    </div>
</div>
```

#### Modals (Confirmation)

```html
<div x-data="{ open: false }" x-show="open" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
         @click="open = false"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6"
             @click.away="open = false">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Confirm Action
            </h3>
            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to proceed?
            </p>
            <div class="flex justify-end space-x-3">
                <button @click="open = false" 
                        class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button class="px-4 py-2 bg-danger-600 text-white rounded-lg hover:bg-danger-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
```

### Animations

**Principles:**
- Use sparingly and purposefully
- Keep duration short (150-300ms)
- Prefer `transition-colors` and `transition-opacity`
- Avoid animations that cause layout shift
- Test on mobile (disable if janky)

**Approved Animations:**
```css
/* Hover states */
.hover\:bg-primary-700 { transition: background-color 150ms ease; }

/* Focus rings */
.focus\:ring-4 { transition: box-shadow 150ms ease; }

/* Fade in/out */
.fade-enter { opacity: 0; }
.fade-enter-active { transition: opacity 200ms ease; }
.fade-enter-to { opacity: 1; }

/* Slide down (for alerts) */
.slide-down-enter { transform: translateY(-10px); opacity: 0; }
.slide-down-enter-active { transition: all 200ms ease; }
.slide-down-enter-to { transform: translateY(0); opacity: 1; }
```

**Avoid:**
- ❌ Complex transforms on mobile
- ❌ Animations longer than 300ms
- ❌ Parallax effects
- ❌ Auto-playing animations

### Responsive Breakpoints

```css
sm:  640px  /* Small tablets */
md:  768px  /* Tablets */
lg:  1024px /* Laptops */
xl:  1280px /* Desktops */
2xl: 1536px /* Large desktops */
```

**Mobile-First Approach:**
```html
<!-- Stack on mobile, grid on desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Cards -->
</div>

<!-- Hide on mobile, show on desktop -->
<div class="hidden md:block">Desktop only</div>

<!-- Show on mobile, hide on desktop -->
<div class="block md:hidden">Mobile only</div>
```

---

## Page Layouts

### Landing Page (`/`)

**Structure:**
```
┌─────────────────────────────────────┐
│ Header (Logo + "Logg inn" button)  │
├─────────────────────────────────────┤
│                                     │
│         Hero Section                │
│   (Headline + Subtext + CTA)        │
│                                     │
├─────────────────────────────────────┤
│                                     │
│    Features Section (3 columns)     │
│   (Icons + Short descriptions)      │
│                                     │
├─────────────────────────────────────┤
│         Footer (Contact info)       │
└─────────────────────────────────────┘
```

**Design Notes:**
- Hero: Large headline (text-4xl), warm and inviting copy
- CTA: Primary button, prominent placement
- Features: Simple icons, 2-3 sentences each
- Tone: Personal, trustworthy, B2C friendly
- Fully responsive (stack on mobile)

### Customer Dashboard (`/dashboard`)

**Structure:**
```
┌─────────────────────────────────────┐
│ Top Nav (Logo + "Mine Ordrer" +    │
│          Profile dropdown)          │
├─────────────────────────────────────┤
│ [Password Change Alert if required] │
├─────────────────────────────────────┤
│  ┌─────┐  ┌─────┐  ┌─────┐         │
│  │Card │  │Card │  │Card │         │
│  │ 1   │  │ 2   │  │ 3   │         │
│  └─────┘  └─────┘  └─────┘         │
│  Total    Total    Utestående       │
│  kjøpt    betalt                    │
├─────────────────────────────────────┤
│                                     │
│    Åpne Ordrer (Table)              │
│                                     │
└─────────────────────────────────────┘
```

**Design Notes:**
- Summary cards: 3 columns on desktop, stack on mobile
- Alert: Yellow warning banner if password change required
- Table: Order number, status badge, outstanding, link to detail
- Empty state: "Ingen åpne ordrer" with friendly message

### Admin Dashboard (`/admin`)

**Structure:**
```
┌─────────────────────────────────────┐
│ Sidebar Nav                         │
│ ├ Dashboard                         │
│ ├ Kunder                            │
│ ├ Varer                             │
│ └ Ordrer                            │
├─────────────────────────────────────┤
│  ┌─────┐  ┌─────┐  ┌─────┐  ┌────┐ │
│  │Card │  │Card │  │Card │  │Card│ │
│  │ 1   │  │ 2   │  │ 3   │  │ 4  │ │
│  └─────┘  └─────┘  └─────┘  └────┘ │
│  Total    Åpne    Antall   Items    │
│  utestå.  ordrer  kunder   i lager  │
├─────────────────────────────────────┤
│                                     │
│  Topp Kunder (Table)                │
│  (Sorted by outstanding desc)       │
│                                     │
└─────────────────────────────────────┘
```

**Design Notes:**
- Sidebar: Fixed on desktop, collapsible on mobile
- Metrics: 4 cards, 2x2 grid on mobile
- Table: Customer name, outstanding, link to detail
- Quick actions: "Ny kunde", "Ny vare", "Ny ordre" buttons

---

## Data Models

### User
```php
class User extends Authenticatable
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'email', 'password', 'role', 'password_change_required'
    ];
    
    protected $casts = [
        'password_change_required' => 'boolean',
    ];
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}
```

### Item
```php
class Item extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'description', 'purchase_price', 'target_price', 'status'
    ];
    
    protected $casts = [
        'purchase_price' => 'integer',
        'target_price' => 'integer',
    ];
    
    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }
    
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
```

### Order
```php
class Order extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'order_number', 'customer_id', 'status', 'total_amount', 'notes'
    ];
    
    protected $casts = [
        'total_amount' => 'integer',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }
    
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    
    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    public function getPaidAmountAttribute(): int
    {
        return $this->payments()->sum('amount');
    }
    
    public function getOutstandingAmountAttribute(): int
    {
        return $this->total_amount - $this->paid_amount;
    }
    
    public function isOverpaid(): bool
    {
        return $this->outstanding_amount < 0;
    }
    
    private static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastOrder ? ((int) substr($lastOrder->order_number, -3)) + 1 : 1;
        
        return $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
```

### OrderLine
```php
class OrderLine extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'order_id', 'item_id', 'unit_price', 'quantity'
    ];
    
    protected $casts = [
        'unit_price' => 'integer',
        'quantity' => 'integer',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
    public function getTotalAttribute(): int
    {
        return $this->unit_price * $this->quantity;
    }
}
```

### Payment
```php
class Payment extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'order_id', 'amount', 'paid_at', 'payment_method', 'note'
    ];
    
    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'date',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
```

---

## Helper Functions

### Currency Formatting

```php
// app/helpers.php

if (!function_exists('format_nok')) {
    /**
     * Format an integer amount (øre) to Norwegian kroner string
     * 
     * @param int $amount Amount in øre
     * @return string Formatted string (e.g., "150,00 kr")
     */
    function format_nok(int $amount): string
    {
        $kr = $amount / 100;
        return number_format($kr, 2, ',', ' ') . ' kr';
    }
}
```

**Usage:**
```blade
<p>{{ format_nok($order->total_amount) }}</p>
<!-- Output: 1 500,00 kr -->
```

---

## Security Implementation

### Middleware

**EnsureUserIsAdmin:**
```php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized action.');
    }
    
    return $next($request);
}
```

**EnsurePasswordChanged:**
```php
public function handle(Request $request, Closure $next)
{
    if (auth()->check() && auth()->user()->password_change_required) {
        if (!$request->routeIs('profile.edit', 'profile.update')) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Du må endre passordet ditt før du kan fortsette.');
        }
    }
    
    return $next($request);
}
```

### Policies

**OrderPolicy:**
```php
public function view(User $user, Order $order): bool
{
    return $user->isAdmin() || $user->id === $order->customer_id;
}

public function update(User $user, Order $order): bool
{
    return $user->isAdmin();
}

public function delete(User $user, Order $order): bool
{
    return $user->isAdmin();
}
```

### Form Requests

**StoreOrderRequest:**
```php
public function authorize(): bool
{
    return auth()->user()->isAdmin();
}

public function rules(): array
{
    return [
        'customer_id' => 'required|exists:users,id',
        'notes' => 'nullable|string|max:1000',
    ];
}

public function messages(): array
{
    return [
        'customer_id.required' => 'Du må velge en kunde.',
        'customer_id.exists' => 'Valgt kunde finnes ikke.',
    ];
}
```

---

## Testing Strategy

### Feature Tests

**Test Coverage:**
1. Auth: Login redirects, role-based access, password change enforcement
2. Policies: Customer cannot view other customer's orders
3. Admin: Can create customers, items, orders
4. Customer: Can view own orders, cannot modify data
5. Calculations: Outstanding balance, overpayment detection
6. Soft Deletes: Deleted records excluded from queries

**Example Test:**
```php
public function test_customer_cannot_view_another_customers_order()
{
    $customer1 = User::factory()->customer()->create();
    $customer2 = User::factory()->customer()->create();
    $order = Order::factory()->for($customer2, 'customer')->create();
    
    $response = $this->actingAs($customer1)->get(route('orders.show', $order));
    
    $response->assertForbidden();
}
```

---

## Error Handling

### Validation Errors
- Display inline below form fields
- Use red text (`text-danger-600`)
- Keep messages short and actionable

### Empty States
- Always show friendly message when list is empty
- Include relevant action button (e.g., "Opprett første kunde")
- Use muted icon or illustration

### 403 Forbidden
- Redirect to appropriate dashboard with flash message
- Message: "Du har ikke tilgang til denne siden."

### 404 Not Found
- Custom 404 page with link back to dashboard
- Message: "Siden du leter etter finnes ikke."

---

*End of Design Document*
