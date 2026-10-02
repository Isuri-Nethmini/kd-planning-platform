{{-- Shared by "New client" and "Add project". $project is optional. --}}
<div class="grid sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Project title *</label>
        <input type="text" name="title" value="{{ old('title', $project->title ?? $defaultTitle ?? '') }}" required
            placeholder="e.g. Two-storey home, Ja-Ela"
            class="w-full border border-ink/25 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:border-draft">
    </div>
    <div>
        <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Site location</label>
        <input type="text" name="site_location" value="{{ old('site_location', $project->site_location ?? '') }}"
            class="w-full border border-ink/25 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:border-draft">
    </div>
    <div>
        <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Based on plan</label>
        <select name="house_plan_id" class="w-full border border-ink/25 rounded-sm px-3 py-2.5 text-sm focus:outline-none focus:border-draft">
            <option value="">— Custom design —</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" @selected(old('house_plan_id', $project->house_plan_id ?? $defaultPlan ?? null) == $plan->id)>{{ $plan->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Contract value (Rs.)</label>
        <input type="number" step="0.01" min="0" name="contract_value" value="{{ old('contract_value', $project->contract_value ?? $defaultValue ?? '') }}"
            class="w-full border border-ink/25 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:border-draft">
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Start date</label>
            <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date ?? null)->toDateString()) }}"
                class="w-full border border-ink/25 rounded-sm px-3 py-2.5 text-sm focus:outline-none focus:border-draft">
        </div>
        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Est. completion</label>
            <input type="date" name="estimated_completion" value="{{ old('estimated_completion', optional($project->estimated_completion ?? null)->toDateString()) }}"
                class="w-full border border-ink/25 rounded-sm px-3 py-2.5 text-sm focus:outline-none focus:border-draft">
        </div>
    </div>
</div>
