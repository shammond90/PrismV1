@php
/**
 * Usage:
 * @include('employments.modal', ['contact' => $contact])
 * or
 * @include('employments.modal', ['company' => $company])
 */
@endphp

<div x-data="employmentModal()" x-init="init()">
    <button @click="open = true" class="btn">Add Employment</button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium">Add Employment</h3>
                <button @click="open = false" class="text-gray-600">&times;</button>
            </div>

            <form action="{{ route('employments.store') }}" method="POST" class="p-4">
                @csrf
                <input type="hidden" name="return_to" value="{{ request()->fullUrl() }}">

                <template x-if="contactId">
                    <input type="hidden" name="contact_id" :value="contactId">
                </template>

                @if(isset($company) || isset($venue) || isset($building) || isset($space))
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Contact</label>
                        <select name="contact_id" class="input" required>
                            <option value="">Select contact</option>
                            @foreach(App\Models\Contact::orderBy('last_name')->limit(200)->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->first_name }} {{ $c->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- hidden employable info populated from include context --}}
                    @if(isset($company))
                        <input type="hidden" name="employable_type" value="company">
                        <input type="hidden" name="employable_id" value="{{ $company->id }}">
                    @elseif(isset($venue))
                        <input type="hidden" name="employable_type" value="venue">
                        <input type="hidden" name="employable_id" value="{{ $venue->id }}">
                    @elseif(isset($building))
                        <input type="hidden" name="employable_type" value="building">
                        <input type="hidden" name="employable_id" value="{{ $building->id }}">
                    @elseif(isset($space))
                        <input type="hidden" name="employable_type" value="space">
                        <input type="hidden" name="employable_id" value="{{ $space->id }}">
                    @endif

                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Position</label>
                            <input type="text" name="position" class="input" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Department</label>
                            <input type="text" name="department" class="input" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium mb-1">Start Date</label>
                                <input type="date" name="start_date" class="input" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">End Date</label>
                                <input type="date" name="end_date" class="input" />
                            </div>
                        </div>
                    @elseif(isset($contact))
                    {{-- Included from a Contact: prefill contact_id and show employable type + lookup --}}
                    <input type="hidden" name="contact_id" value="{{ $contact->id }}">
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Type</label>
                            <select name="employable_type" x-model="type" @change="fetchList()" class="input">
                                <option value="company">Company</option>
                                <option value="venue">Venue</option>
                                <option value="building">Building</option>
                                <option value="space">Space</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Select</label>
                            <input type="text" x-model="q" @input.debounce.300="fetchList()" placeholder="Search..." class="input mb-2" />
                            <select name="employable_id" x-model="selectedId" class="input">
                                <option :value="item.id" x-for="item in items" x-text="item.text" :key="item.id"></option>
                            </select>
                        </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Position</label>
                        <input type="text" name="position" class="input" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Department</label>
                        <input type="text" name="department" class="input" />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Start Date</label>
                            <input type="date" name="start_date" class="input" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">End Date</label>
                            <input type="date" name="end_date" class="input" />
                        </div>
                    </div>

                        <div class="flex justify-end gap-2 mt-3">
                            <button type="button" @click="open = false" class="btn-secondary">Cancel</button>
                            <button type="submit" class="btn-primary">Save</button>
                        </div>
                    </div>
                    @else
                    {{-- Fallback: show type + lookup and allow contact selection if not provided --}}
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Contact</label>
                        <select name="contact_id" class="input" required>
                            <option value="">Select contact</option>
                            @foreach(App\Models\Contact::orderBy('last_name')->limit(200)->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->first_name }} {{ $c->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Type</label>
                            <select name="employable_type" x-model="type" @change="fetchList()" class="input">
                                <option value="company">Company</option>
                                <option value="venue">Venue</option>
                                <option value="building">Building</option>
                                <option value="space">Space</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Select</label>
                            <input type="text" x-model="q" @input.debounce.300="fetchList()" placeholder="Search..." class="input mb-2" />
                            <select name="employable_id" x-model="selectedId" class="input">
                                <option :value="item.id" x-for="item in items" x-text="item.text" :key="item.id"></option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 mt-3">
                            <button type="button" @click="open = false" class="btn-secondary">Cancel</button>
                            <button type="submit" class="btn-primary">Save</button>
                        </div>
                    </div>
                    @endif
            </form>
        </div>
    </div>

    <script>
        function employmentModal() {
            return {
                open: false,
                type: '{{ isset($company) ? "company" : "company" }}',
                q: '',
                items: [],
                selectedId: null,
                contactId: {{ isset($contact) ? $contact->id : 'null' }},
                route: '{{ route('employments.store') }}',
                init() {
                    @if(isset($company))
                        this.contactId = null;
                        this.type = 'company';
                        this.selectedId = {{ $company->id }};
                    @elseif(isset($venue))
                        this.contactId = null;
                        this.type = 'venue';
                        this.selectedId = {{ $venue->id }};
                    @elseif(isset($building))
                        this.contactId = null;
                        this.type = 'building';
                        this.selectedId = {{ $building->id }};
                    @elseif(isset($space))
                        this.contactId = null;
                        this.type = 'space';
                        this.selectedId = {{ $space->id }};
                    @endif
                    this.fetchList();
                },
                async fetchList() {
                    const params = new URLSearchParams({ type: this.type, q: this.q });
                    const res = await fetch('{{ route('employables.search') }}?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const json = await res.json();
                    this.items = json.results || [];
                    if (this.items.length && !this.selectedId) this.selectedId = this.items[0].id;
                }
            }
        }
    </script>
</div>
