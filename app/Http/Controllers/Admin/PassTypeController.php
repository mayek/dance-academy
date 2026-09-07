<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PassType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PassTypeController extends Controller
{
    public function index()
    {
        $passTypes = PassType::withCount('payments')
            ->orderByRaw('name IS NULL')
            ->orderBy('name')
            ->orderBy('type')
            ->orderBy('duration_months')
            ->paginate(10);
        return view('admin.passes.index', compact('passTypes'));
    }

    public function create()
    {
        return view('admin.passes.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        PassType::create($validated);

        return redirect()->route('admin.passes.index')
            ->with('success', __('Pass created successfully.'));
    }

    public function edit(PassType $pass)
    {
        return view('admin.passes.edit', compact('pass'));
    }

    public function update(Request $request, PassType $pass)
    {
        $validated = $this->validateData($request);

        $pass->update($validated);

        return redirect()->route('admin.passes.index')
            ->with('success', __('Pass updated successfully.'));
    }

    public function destroy(PassType $pass)
    {
        $pass->delete();
        return redirect()->route('admin.passes.index')
            ->with('success', __('Pass deleted successfully.'));
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'duration' => ['required', 'in:single,1,2,3,6,12'],
            'price' => ['required', 'numeric', 'min:0'],
            'hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
        ]);

        $validated['name'] = trim($validated['name'] ?? '') ?: null;

        if ($validated['duration'] === 'single') {
            $validated['type'] = 'single';
            $validated['duration_months'] = null;
            $validated['hours'] = null;
        } else {
            $validated['type'] = 'monthly';
            $validated['duration_months'] = (int) $validated['duration'];
        }

        $validated['hours'] = $validated['hours'] === null || $validated['hours'] === ''
            ? null
            : (float) $validated['hours'];

        unset($validated['duration']);

        return $validated;
    }
}
