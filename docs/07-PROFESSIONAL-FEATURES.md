# Professional Features - Teman Bicara

## Overview

Professional users (psychiatrists, psychologists, conversationalists) memiliki akses ke fitur-fitur khusus untuk mengelola profil, jadwal, dan appointment mereka.

## User Roles

### Role Types

```php
// User model
'role' => 'user' | 'professional'
```

**User (Client):**
- Browse professionals
- Book appointments
- Manage cart
- View appointments
- Cancel appointments

**Professional:**
- All user features +
- Manage professional profile
- Create/delete schedules
- View appointments as professional
- Complete appointments

## Professional Model

### Database Schema

```sql
CREATE TABLE professionals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    specialization ENUM('psychiatrist', 'psychologist', 'conversationalist'),
    bio TEXT,
    years_of_experience INT,
    education VARCHAR(255),
    price_30 DECIMAL(10,2),
    price_60 DECIMAL(10,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Model Relationships

```php
// app/Models/Professional.php

class Professional extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'bio',
        'years_of_experience',
        'education',
        'price_30',
        'price_60',
    ];

    // Belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Has many Schedules
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Has many Appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Available schedules only
    public function availableSchedules()
    {
        return $this->schedules()
            ->where('is_available', true)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time');
    }

    // Get specialization label
    public function getSpecializationLabelAttribute()
    {
        return match($this->specialization) {
            'psychiatrist' => 'Psikiater',
            'psychologist' => 'Psikolog',
            'conversationalist' => 'Konselor',
            default => ucfirst($this->specialization),
        };
    }
}
```

### User Helper Methods

```php
// app/Models/User.php

class User extends Authenticatable implements MustVerifyEmail
{
    // Check if user is professional
    public function isProfessional()
    {
        return $this->role === 'professional';
    }

    // Check if user is regular user
    public function isUser()
    {
        return $this->role === 'user';
    }

    // Get professional profile
    public function professional()
    {
        return $this->hasOne(Professional::class);
    }
}
```

## Schedule Management

### Features

Professional dapat:
1. Melihat semua jadwal mereka
2. Menambah jadwal baru
3. Menghapus jadwal yang belum di-booking
4. Melihat status jadwal (available/booked)

### Routes

```php
// routes/web.php

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('professional')->name('professional.')->group(function () {
        Route::get('/schedules', [ScheduleController::class, 'index'])
            ->name('schedules.index');

        Route::get('/schedules/create', [ScheduleController::class, 'create'])
            ->name('schedules.create');

        Route::post('/schedules', [ScheduleController::class, 'store'])
            ->name('schedules.store');

        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])
            ->name('schedules.destroy');
    });
});
```

### Schedule Controller

**File**: `app/Http/Controllers/Professional/ScheduleController.php`

```php
<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display list of schedules
     */
    public function index()
    {
        // Check if user is professional
        if (!auth()->user()->isProfessional()) {
            abort(403, 'Unauthorized');
        }

        $professional = auth()->user()->professional;

        if (!$professional) {
            return redirect()->route('dashboard')
                ->with('error', 'Professional profile not found');
        }

        $schedules = Schedule::where('professional_id', $professional->id)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        return view('professional.schedules.index', compact('schedules'));
    }

    /**
     * Show form to create schedule
     */
    public function create()
    {
        if (!auth()->user()->isProfessional()) {
            abort(403, 'Unauthorized');
        }

        return view('professional.schedules.create');
    }

    /**
     * Store new schedule
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isProfessional()) {
            abort(403, 'Unauthorized');
        }

        $professional = auth()->user()->professional;

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check for duplicate schedule
        $exists = Schedule::where('professional_id', $professional->id)
            ->where('date', $request->date)
            ->where('start_time', $request->start_time)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Jadwal sudah ada untuk tanggal dan waktu tersebut');
        }

        Schedule::create([
            'professional_id' => $professional->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => true,
        ]);

        return redirect()->route('professional.schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    /**
     * Delete schedule
     */
    public function destroy(Schedule $schedule)
    {
        if (!auth()->user()->isProfessional()) {
            abort(403, 'Unauthorized');
        }

        $professional = auth()->user()->professional;

        // Authorization: only owner can delete
        if ($schedule->professional_id !== $professional->id) {
            abort(403, 'Unauthorized');
        }

        // Cannot delete if already booked
        if (!$schedule->is_available) {
            return back()->with('error', 'Tidak dapat menghapus jadwal yang sudah di-booking');
        }

        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus');
    }
}
```

### Schedule Views

**Index** (`resources/views/professional/schedules/index.blade.php`):

```php
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kelola Jadwal
            </h2>
            <a href="{{ route('professional.schedules.create') }}"
               class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                + Tambah Jadwal
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($schedules->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Mulai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Selesai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $schedule->date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $schedule->start_time }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $schedule->end_time }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($schedule->is_available)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Sudah Di-booking
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($schedule->is_available)
                                                <form method="POST" action="{{ route('professional.schedules.destroy', $schedule) }}"
                                                      onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $schedules->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            Belum ada jadwal. <a href="{{ route('professional.schedules.create') }}" class="text-purple-600 hover:underline">Tambah jadwal</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

**Create** (`resources/views/professional/schedules/create.blade.php`):

```php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Jadwal
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('professional.schedules.store') }}">
                        @csrf

                        <!-- Date -->
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal
                            </label>
                            <input type="date"
                                   id="date"
                                   name="date"
                                   value="{{ old('date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Time -->
                        <div class="mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Waktu Mulai
                            </label>
                            <input type="time"
                                   id="start_time"
                                   name="start_time"
                                   value="{{ old('start_time') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div class="mb-4">
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Waktu Selesai
                            </label>
                            <input type="time"
                                   id="end_time"
                                   name="end_time"
                                   value="{{ old('end_time') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-700">
                                <strong>Info:</strong> Jadwal yang dibuat akan tersedia untuk di-booking oleh klien.
                                Pastikan Anda tersedia pada waktu yang dipilih.
                            </p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('professional.schedules.index') }}"
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

## Professional Dashboard

### Dashboard Features

Professional melihat:
1. Appointment statistics (upcoming, completed, cancelled)
2. Recent appointments
3. Quick actions (add schedule, view appointments)
4. Earnings summary (if payment tracking implemented)

### Dashboard Controller

```php
// app/Http/Controllers/Professional/DashboardController.php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Schedule;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isProfessional()) {
            return redirect()->route('dashboard');
        }

        $professional = auth()->user()->professional;

        // Statistics
        $stats = [
            'upcoming' => Appointment::where('professional_id', $professional->id)
                ->where('status', 'confirmed')
                ->whereHas('schedule', function ($q) {
                    $q->where('date', '>=', now()->toDateString());
                })
                ->count(),

            'completed' => Appointment::where('professional_id', $professional->id)
                ->where('status', 'completed')
                ->count(),

            'cancelled' => Appointment::where('professional_id', $professional->id)
                ->where('status', 'cancelled')
                ->count(),

            'available_schedules' => Schedule::where('professional_id', $professional->id)
                ->where('is_available', true)
                ->where('date', '>=', now()->toDateString())
                ->count(),
        ];

        // Recent appointments
        $recentAppointments = Appointment::where('professional_id', $professional->id)
            ->with(['user', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('professional.dashboard', compact('stats', 'recentAppointments'));
    }
}
```

## Professional Appointments

### View Appointments

Professional dapat melihat semua appointment mereka:

```php
// app/Http/Controllers/Professional/AppointmentController.php

public function index()
{
    if (!auth()->user()->isProfessional()) {
        abort(403);
    }

    $professional = auth()->user()->professional;

    $appointments = Appointment::where('professional_id', $professional->id)
        ->with(['user', 'schedule'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('professional.appointments.index', compact('appointments'));
}
```

### Complete Appointment

Professional menandai appointment sebagai completed setelah sesi selesai:

```php
// app/Http/Controllers/Professional/AppointmentController.php

public function complete(Appointment $appointment)
{
    if (!auth()->user()->isProfessional()) {
        abort(403);
    }

    $professional = auth()->user()->professional;

    // Authorization
    if ($appointment->professional_id !== $professional->id) {
        abort(403);
    }

    // Update status
    $appointment->update(['status' => 'completed']);

    return back()->with('success', 'Appointment berhasil diselesaikan');
}
```

## Middleware for Professional Routes

### Create Middleware

```bash
php artisan make:middleware EnsureProfessional
```

```php
// app/Http/Middleware/EnsureProfessional.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProfessional
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isProfessional()) {
            abort(403, 'Unauthorized. Professional access only.');
        }

        if (!auth()->user()->professional) {
            abort(403, 'Professional profile not found.');
        }

        return $next($request);
    }
}
```

### Register Middleware

```php
// app/Http/Kernel.php

protected $middlewareAliases = [
    // ...
    'professional' => \App\Http\Middleware\EnsureProfessional::class,
];
```

### Use Middleware

```php
// routes/web.php

Route::middleware(['auth', 'verified', 'professional'])->group(function () {
    Route::prefix('professional')->name('professional.')->group(function () {
        Route::get('/dashboard', [ProfessionalDashboardController::class, 'index'])->name('dashboard');
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    });
});
```

## Navigation for Professionals

### Conditional Navigation

```php
<!-- resources/views/layouts/navigation.blade.php -->

@auth
    @if(auth()->user()->isProfessional())
        <!-- Professional Menu -->
        <x-nav-link :href="route('professional.dashboard')" :active="request()->routeIs('professional.dashboard')">
            Dashboard Professional
        </x-nav-link>

        <x-nav-link :href="route('professional.schedules.index')" :active="request()->routeIs('professional.schedules.*')">
            Kelola Jadwal
        </x-nav-link>

        <x-nav-link :href="route('professional.appointments.index')" :active="request()->routeIs('professional.appointments.*')">
            Appointments Saya
        </x-nav-link>
    @else
        <!-- Regular User Menu -->
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            Dashboard
        </x-nav-link>

        <x-nav-link :href="route('professionals.index')" :active="request()->routeIs('professionals.*')">
            Cari Professional
        </x-nav-link>

        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
            Keranjang
        </x-nav-link>

        <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
            My Appointments
        </x-nav-link>
    @endif
@endauth
```

## Seeding Professional Users

### Factory

```php
// database/factories/ProfessionalFactory.php

public function definition()
{
    return [
        'specialization' => $this->faker->randomElement(['psychiatrist', 'psychologist', 'conversationalist']),
        'bio' => $this->faker->paragraph(),
        'years_of_experience' => $this->faker->numberBetween(1, 20),
        'education' => $this->faker->randomElement([
            'S2 Psikologi Klinis - Universitas Indonesia',
            'S1 Kedokteran, Sp.KJ - Universitas Gadjah Mada',
            'S2 Konseling - Universitas Padjajaran',
        ]),
        'price_30' => $this->faker->numberBetween(100, 300) * 1000,
        'price_60' => $this->faker->numberBetween(150, 500) * 1000,
    ];
}
```

### Seeder

```php
// database/seeders/DatabaseSeeder.php

public function run()
{
    // Create professional users
    User::factory()
        ->count(10)
        ->create([
            'role' => 'professional',
            'email_verified_at' => now(),
        ])
        ->each(function ($user) {
            Professional::factory()->create([
                'user_id' => $user->id,
            ]);
        });

    // Create regular users
    User::factory()
        ->count(20)
        ->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
}
```

## Best Practices

1. **Authorization**: Always check professional ownership before allowing actions
2. **Validation**: Validate schedule times don't overlap
3. **Business Rules**: Don't allow deleting booked schedules
4. **User Experience**: Show clear status badges (available/booked)
5. **Navigation**: Separate professional and user menus
6. **Middleware**: Use dedicated middleware for professional routes
7. **Notifications**: Notify professionals when they get booked
8. **Statistics**: Show meaningful metrics on dashboard
9. **Testing**: Test authorization thoroughly
10. **Documentation**: Document professional-specific workflows

## Next Documentation

- [08-ARTICLE-SYSTEM.md](08-ARTICLE-SYSTEM.md) - Article/blog management system
- [09-API-ENDPOINTS.md](09-API-ENDPOINTS.md) - All routes and endpoints reference
