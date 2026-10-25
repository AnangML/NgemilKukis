<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGreetingCardRequest;
use App\Http\Requests\Admin\UpdateGreetingCardRequest;
use App\Models\GreetingCardTemplate;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GreetingCardController extends Controller
{
    /**
     * Display a listing of greeting card templates.
     */
    public function index(Request $request): View
    {
        $query = GreetingCardTemplate::query()->latest();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('default_message', 'like', "%{$search}%");
            });
        }

        $cards = $query->paginate(12)->withQueryString();

        return view('admin.greeting_cards.index', compact('cards'));
    }

    /**
     * Show the form for creating a new greeting card template.
     */
    public function create(): View
    {
        return view('admin.greeting_cards.create');
    }

    /**
     * Store a newly created greeting card template in storage.
     */
    public function store(StoreGreetingCardRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (GreetingCardTemplate::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active');

        $card = GreetingCardTemplate::create($data);

        ActivityLogger::catalog(
            'card.created',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." menambahkan kartu ucapan baru: \"{$data['name']}\"",
            $card
        );

        return redirect()->route('admin.greeting-cards.index')
            ->with('success', "Template kartu ucapan \"{$data['name']}\" berhasil ditambahkan!");
    }

    /**
     * Show the form for editing the specified greeting card template.
     */
    public function edit(GreetingCardTemplate $greetingCard): View
    {
        return view('admin.greeting_cards.edit', compact('greetingCard'));
    }

    /**
     * Update the specified greeting card template in storage.
     */
    public function update(UpdateGreetingCardRequest $request, GreetingCardTemplate $greetingCard): RedirectResponse
    {
        $data = $request->validated();

        if ($greetingCard->name !== $data['name']) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (GreetingCardTemplate::where('slug', $slug)->where('id', '!=', $greetingCard->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        }

        $data['is_active'] = $request->boolean('is_active');

        $greetingCard->update($data);

        ActivityLogger::catalog(
            'card.updated',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." memperbarui kartu ucapan: \"{$greetingCard->name}\"",
            $greetingCard
        );

        return redirect()->route('admin.greeting-cards.index')
            ->with('success', "Template kartu ucapan \"{$greetingCard->name}\" berhasil diperbarui!");
    }

    /**
     * Toggle the active status of the specified greeting card template.
     */
    public function toggleStatus(GreetingCardTemplate $greetingCard): RedirectResponse
    {
        $greetingCard->update([
            'is_active' => ! $greetingCard->is_active,
        ]);

        ActivityLogger::catalog(
            'card.status_toggled',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." mengubah status kartu ucapan \"{$greetingCard->name}\" menjadi ".($greetingCard->is_active ? 'Aktif' : 'Nonaktif'),
            $greetingCard
        );

        $statusLabel = $greetingCard->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status kartu \"{$greetingCard->name}\" berhasil {$statusLabel}!");
    }

    /**
     * Remove the specified greeting card template from storage.
     */
    public function destroy(GreetingCardTemplate $greetingCard): RedirectResponse
    {
        $name = $greetingCard->name;
        $greetingCard->delete();

        ActivityLogger::catalog(
            'card.deleted',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." menghapus kartu ucapan: \"{$name}\"",
            null
        );

        return redirect()->route('admin.greeting-cards.index')
            ->with('success', "Template kartu ucapan \"{$name}\" berhasil dihapus.");
    }
}
