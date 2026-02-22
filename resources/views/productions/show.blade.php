<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $production->title }}</h2>
            <div>
                @can('productions.update')
                    <a href="{{ route('productions.edit', $production) }}" class="text-blue-600">Edit</a>
                @endcan
                <a href="{{ route('productions.index') }}" class="ml-4 text-gray-600">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div>
                        <div class="mb-4 border-b">
                            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                                <button data-tab="overview" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Overview</button>
                                <button data-tab="companies" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Companies</button>
                                <button data-tab="contacts" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Contacts</button>
                                <button data-tab="scheduling" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Scheduling</button>
                                <button data-tab="notes" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Notes</button>
                                <button data-tab="files" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Files</button>
                                <button data-tab="finance" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Finance</button>
                            </nav>
                        </div>

                        <div id="tab-overview" class="tab-panel">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-2"><strong>Show:</strong> <a href="{{ route('shows.show', $production->show) }}" class="text-blue-600">{{ $production->show->title }}</a></div>
                                    <div class="mb-2"><strong>Title:</strong> {{ $production->title }}</div>
                                    <div class="mb-2"><strong>Status:</strong> {{ $production->status }}</div>
                                    <div class="mb-2"><strong>Dates:</strong> {{ optional($production->start_date)->toDateString() ?? '—' }} — {{ optional($production->end_date)->toDateString() ?? '—' }}</div>
                                    <div class="mb-2"><strong>Initial Contact Date:</strong> {{ optional($production->initial_contact_date)->toDateString() ?? '—' }}</div>
                                    <div class="mb-2"><strong>Space:</strong>
                                        @if($production->space)
                                            <a href="{{ route('spaces.show', $production->space) }}" class="text-blue-600">{{ $production->space->name }}</a>
                                            @if($production->space->building)
                                                — <a href="{{ route('buildings.show', $production->space->building) }}" class="text-blue-600">{{ $production->space->building->name }}</a>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </div>
                                    <div class="mb-2"><strong>Notes:</strong>
                                        <div class="mt-1 text-gray-700">{{ $production->notes ?? '—' }}</div>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-2"><strong>Primary Company:</strong>
                                        @if($production->primaryCompany)
                                            <a href="{{ route('companies.show', $production->primaryCompany) }}" class="text-blue-600">{{ $production->primaryCompany->name }}</a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                    <div class="mb-2"><strong>Primary Contact:</strong>
                                        @if($production->primaryContact)
                                            <a href="{{ route('contacts.show', $production->primaryContact) }}" class="text-blue-600">{{ trim(($production->primaryContact->first_name ?? '') . ' ' . ($production->primaryContact->last_name ?? '')) }}</a>
                                            @php
                                                $primaryPhone = $production->primaryContact->phones->firstWhere('primary', true);
                                                $primaryEmail = $production->primaryContact->emails->firstWhere('primary', true);
                                            @endphp
                                            <div class="text-sm text-gray-600 mt-1">
                                                <div>Phone: @if($primaryPhone)<a href="tel:{{ $primaryPhone->number }}" class="text-blue-600">{{ $primaryPhone->number }}</a>@else — @endif</div>
                                                <div>Email: @if($primaryEmail)<a href="mailto:{{ $primaryEmail->address }}" class="text-blue-600">{{ $primaryEmail->address }}</a>@else — @endif</div>
                                            </div>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-sm text-gray-500">
                                <div>Created: {{ $production->created_at->toDateTimeString() }}</div>
                                <div>Updated: {{ $production->updated_at->toDateTimeString() }}</div>
                            </div>
                        </div>

                        <div id="tab-companies" class="tab-panel hidden">
                            <div class="mb-4">
                                <strong>Companies (all)</strong>
                                <ul class="mt-2 ml-4 list-disc">
                                    @foreach($production->companies as $c)
                                        <li class="flex items-center justify-between">
                                            <div>
                                                <a href="{{ route('companies.show', $c) }}" class="text-blue-600">{{ $c->name }}</a>
                                                @if($production->primary_company_id == $c->id)
                                                    <span class="ml-2 text-xs text-green-600">(Primary)</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @can('productions.update')
                                                    <form action="{{ route('productions.companies.attach', $production) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="company_id" value="{{ $c->id }}" />
                                                        <input type="hidden" name="primary" value="1" />
                                                        <button class="text-sm text-blue-600">Make Primary</button>
                                                    </form>
                                                    <form action="{{ route('productions.companies.detach', [$production, $c->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="text-sm text-red-600">Remove</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @can('productions.update')
                                <div class="mt-3">
                                    <form action="{{ route('productions.companies.attach', $production) }}" method="POST" class="flex gap-2 items-center">
                                        @csrf
                                        <label class="flex-1">
                                            <select name="company_id" class="input block w-full">
                                                <option value="">Select company to add</option>
                                                @foreach($companies as $company)
                                                    @unless($production->companies->pluck('id')->contains($company->id))
                                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                    @endunless
                                                @endforeach
                                            </select>
                                        </label>
                                        <button class="btn">Add</button>
                                    </form>
                                </div>
                            @endcan
                        </div>

                        <div id="tab-contacts" class="tab-panel hidden">
                            <div class="mb-4">
                                <strong>Contacts (all)</strong>
                                <div class="mt-3 space-y-3">
                                    @foreach($production->contacts as $ct)
                                        @php
                                            $primaryPhone = $ct->phones->firstWhere('primary', true);
                                            $primaryEmail = $ct->emails->firstWhere('primary', true);
                                        @endphp
                                        <div class="border rounded-lg p-4">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <div class="flex items-center">
                                                        <a href="{{ route('contacts.show', $ct) }}" class="text-lg font-medium text-blue-600">{{ trim(($ct->first_name ?? '') . ' ' . ($ct->last_name ?? '')) }}</a>
                                                        @if($production->primary_contact_id == $ct->id)
                                                            <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Primary</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="mt-2 text-sm text-gray-600">
                                                        <div>Phone: @if($primaryPhone)<a href="tel:{{ $primaryPhone->number }}" class="text-blue-600">{{ $primaryPhone->number }}</a>@else — @endif</div>
                                                        <div>Email: @if($primaryEmail)<a href="mailto:{{ $primaryEmail->address }}" class="text-blue-600">{{ $primaryEmail->address }}</a>@else — @endif</div>
                                                    </div>

                                                        @php
                                                            $pivotDepartments = [];
                                                            if (!empty($ct->pivot->departments)) {
                                                                try { $pivotDepartments = is_array($ct->pivot->departments) ? $ct->pivot->departments : json_decode($ct->pivot->departments, true) ?? []; } catch (\Exception $e) { $pivotDepartments = []; }
                                                            } elseif (!empty($ct->pivot->department)) {
                                                                $pivotDepartments = [$ct->pivot->department];
                                                            }
                                                            $pivotPositions = [];
                                                            if (!empty($ct->pivot->positions)) {
                                                                try { $pivotPositions = is_array($ct->pivot->positions) ? $ct->pivot->positions : json_decode($ct->pivot->positions, true) ?? []; } catch (\Exception $e) { $pivotPositions = []; }
                                                            }
                                                        @endphp
                                                        @if($ct->pivot->role || !empty($pivotDepartments) || !empty($pivotPositions) || $ct->pivot->notes)
                                                        <div class="mt-2">
                                                            @if($ct->pivot->role)
                                                                <div class="text-sm"><span class="font-medium">Role:</span> {{ $ct->pivot->role }}</div>
                                                            @endif
                                                            @if(!empty($pivotDepartments))
                                                                <div class="text-sm"><span class="font-medium">Department:</span> {{ implode(', ', $pivotDepartments) }}</div>
                                                            @endif
                                                            @if(!empty($pivotPositions))
                                                                <div class="text-sm"><span class="font-medium">Position:</span> {{ implode(', ', $pivotPositions) }}</div>
                                                            @endif
                                                            @if($ct->pivot->notes)
                                                                <div class="text-sm"><span class="font-medium">Notes:</span> {{ $ct->pivot->notes }}</div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex items-center space-x-2">
                                                    @can('productions.update')
                                                        <button onclick="editContact({{ $ct->id }})" class="text-sm text-blue-600 hover:text-blue-800">Edit</button>
                                                        <form action="{{ route('productions.contacts.attach', $production) }}" method="POST" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="contact_id" value="{{ $ct->id }}" />
                                                            <input type="hidden" name="primary" value="1" />
                                                            <button class="text-sm text-green-600 hover:text-green-800">Make Primary</button>
                                                        </form>
                                                        <form action="{{ route('productions.contacts.detach', [$production, $ct->id]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="text-sm text-red-600 hover:text-red-800">Remove</button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </div>

                                            <!-- Edit Form (hidden by default) -->
                                                        <div id="edit-contact-{{ $ct->id }}" class="mt-4 border-t pt-4 hidden">
                                                <form action="{{ route('productions.contacts.update-pivot', [$production, $ct->id]) }}" method="POST" onsubmit="return submitEditContact(event, {{ $ct->id }})">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Role</label>
                                                            <input type="text" name="role" value="{{ $ct->pivot->role }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Department</label>
                                                            <select name="departments[]" id="edit-departments-{{ $ct->id }}" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                                @foreach($departments as $d)
                                                                    @php
                                                                        $selDeps = [];
                                                                        if (!empty($ct->pivot->departments)) { try { $selDeps = is_array($ct->pivot->departments) ? $ct->pivot->departments : json_decode($ct->pivot->departments, true) ?? []; } catch (\Exception $e) { $selDeps = []; } }
                                                                    @endphp
                                                                    <option value="{{ $d->name }}" {{ in_array($d->name, $selDeps) ? 'selected' : '' }}>{{ $d->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium text-gray-700">Position</label>
                                                        <select name="positions[]" id="edit-positions-{{ $ct->id }}" data-existing='@json($pivotPositions)' multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                            {{-- populated by JS --}}
                                                        </select>
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                                                        <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $ct->pivot->notes }}</textarea>
                                                    </div>
                                                    <div class="mt-4 flex justify-end space-x-3">
                                                        <button type="button" onclick="cancelEditContact({{ $ct->id }})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                                                        <button type="submit" class="px-3 py-2 text-sm text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @can('productions.update')
                                <div class="mt-3">
                                    <button type="button" onclick="openAddContactModal()" class="btn">Add Contact</button>
                                </div>

                                <!-- Add Contact Modal -->
                                <div id="add-contact-modal" class="fixed inset-0 z-50 hidden">
                                    <div class="flex items-center justify-center min-h-screen px-4">
                                        <div class="fixed inset-0 bg-black opacity-50" onclick="closeAddContactModal()"></div>
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md relative">
                                            <div class="flex items-center justify-between p-4 border-b">
                                                <h3 class="text-lg font-medium">Add Contact to Production</h3>
                                                <button type="button" onclick="closeAddContactModal()" class="text-gray-400 hover:text-gray-600">
                                                    <span class="sr-only">Close</span>
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            
                                            <form action="{{ route('productions.contacts.attach', $production) }}" method="POST" onsubmit="return submitAddContact(event)">
                                                @csrf
                                                <div class="p-6 space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Contact</label>
                                                        <select name="contact_id" id="contact-select" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required onchange="updateContactInfo()">
                                                            <option value="">Select a contact</option>
                                                            @foreach($contacts as $contact)
                                                                @unless($production->contacts->pluck('id')->contains($contact->id))
                                                                    @php
                                                                        $pEmail = $contact->emails->firstWhere('primary', true);
                                                                        $pPhone = $contact->phones->firstWhere('primary', true);
                                                                    @endphp
                                                                    <option value="{{ $contact->id }}" data-email="{{ $pEmail?->address ?? '' }}" data-phone="{{ $pPhone?->number ?? '' }}">
                                                                        {{ trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? '')) ?: 'Contact #' . $contact->id }}
                                                                    </option>
                                                                @endunless
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Role</label>
                                                            <input type="text" name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Department</label>
                                                            <select name="departments[]" id="add-departments" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                                @foreach($departments as $d)
                                                                    <option value="{{ $d->name }}">{{ $d->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium text-gray-700">Position</label>
                                                        <select name="positions[]" id="add-positions" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                            {{-- options populated by JS based on selected departments --}}
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                                                        <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                                    </div>

                                                    <div id="contact-info" class="text-sm text-gray-600 bg-gray-50 p-3 rounded">
                                                        <div>Phone: —</div>
                                                        <div>Email: —</div>
                                                    </div>
                                                </div>

                                                <div class="flex justify-end space-x-3 p-4 border-t">
                                                    <button type="button" onclick="closeAddContactModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">Add</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    function openAddContactModal() {
                                        document.getElementById('add-contact-modal').classList.remove('hidden');
                                        document.getElementById('contact-select').focus();
                                    }

                                    function closeAddContactModal() {
                                        document.getElementById('add-contact-modal').classList.add('hidden');
                                        document.getElementById('contact-select').value = '';
                                        document.querySelector('input[name="role"]').value = '';
                                        document.querySelector('input[name="department"]').value = '';
                                        document.querySelector('textarea[name="notes"]').value = '';
                                        updateContactInfo();
                                    }

                                    function updateContactInfo() {
                                        const select = document.getElementById('contact-select');
                                        const info = document.getElementById('contact-info');
                                        const option = select.selectedOptions[0];
                                        
                                        if (!option || !option.value) {
                                            info.innerHTML = '<div>Phone: —</div><div>Email: —</div>';
                                            return;
                                        }
                                        
                                        const email = option.dataset.email || '—';
                                        const phone = option.dataset.phone || '—';
                                        
                                        const phoneHtml = phone !== '—' ? `<a href="tel:${phone}" class="text-blue-600">${phone}</a>` : '—';
                                        const emailHtml = email !== '—' ? `<a href="mailto:${email}" class="text-blue-600">${email}</a>` : '—';
                                        
                                        info.innerHTML = `<div>Phone: ${phoneHtml}</div><div>Email: ${emailHtml}</div>`;
                                    }

                                    // Departments -> Positions mapping (from server-provided data)
                                    @php
                                        $departmentsForJs = $departments->map(function($d){
                                            return [
                                                'id' => $d->id,
                                                'name' => $d->name,
                                                'positions' => $d->positions->map(function($p){ return ['id' => $p->id, 'name' => $p->name]; })->values()->toArray(),
                                            ];
                                        })->values()->toArray();
                                    @endphp
                                    const departmentsData = @json($departmentsForJs);

                                    function buildPositionsFor(deptNames) {
                                        const seen = {};
                                        const opts = [];
                                        deptNames.forEach(function(dn){
                                            departmentsData.forEach(function(dd){
                                                if (dd.name === dn) {
                                                    dd.positions.forEach(function(p){
                                                        if (!seen[p.id]) { seen[p.id]=true; opts.push(p); }
                                                    });
                                                }
                                            });
                                        });
                                        return opts;
                                    }

                                    // Update add modal positions when departments change
                                    document.getElementById('add-departments').addEventListener('change', function(){
                                        const sel = Array.from(this.selectedOptions).map(o=>o.value);
                                        const options = buildPositionsFor(sel);
                                        const posEl = document.getElementById('add-positions');
                                        posEl.innerHTML = '';
                                        options.forEach(function(p){
                                            const o = document.createElement('option'); o.value = p.id; o.textContent = p.name; posEl.appendChild(o);
                                        });
                                    });

                                    // When opening add modal, clear positions
                                    function openAddContactModal() {
                                        document.getElementById('add-contact-modal').classList.remove('hidden');
                                        document.getElementById('contact-select').focus();
                                        document.getElementById('add-departments').value = null;
                                        document.getElementById('add-positions').innerHTML = '';
                                    }

                                    function submitAddContact(event) {
                                        // Let the form submit naturally, which will redirect and close the modal
                                        return true;
                                    }

                                    // Edit contact: populate positions based on selected departments and prepare arrays
                                    function submitEditContact(e, contactId) {
                                        // nothing special needed; browser will send departments[] and positions[]
                                        return true;
                                    }

                                    // When opening an edit form, populate positions
                                    function openEditPositions(contactId) {
                                        const depEl = document.getElementById('edit-departments-' + contactId);
                                        const posEl = document.getElementById('edit-positions-' + contactId);
                                        if (!depEl || !posEl) return;
                                        const sel = Array.from(depEl.selectedOptions).map(o=>o.value);
                                        const options = buildPositionsFor(sel);
                                        posEl.innerHTML = '';
                                        options.forEach(function(p){
                                            const o = document.createElement('option'); o.value = p.id; o.textContent = p.name; posEl.appendChild(o);
                                        });
                                        // pre-select any existing positions from pivot (stored as names)
                                        try {
                                            const existingNames = JSON.parse(posEl.getAttribute('data-existing') || '[]');
                                            if (Array.isArray(existingNames) && existingNames.length) {
                                                Array.from(posEl.options).forEach(function(opt){
                                                    if (existingNames.indexOf(opt.textContent) !== -1) opt.selected = true;
                                                });
                                            }
                                        } catch (ee) { /* ignore */ }
                                    }

                                    // Hook edit buttons to populate positions when the edit form is shown
                                    window.editContact = function(contactId) {
                                        document.getElementById('edit-contact-' + contactId).classList.remove('hidden');
                                        // small delay to ensure select exists
                                        setTimeout(function(){ openEditPositions(contactId); }, 20);
                                        // attach change listener to update positions when departments selection changes
                                        var depEl = document.getElementById('edit-departments-' + contactId);
                                        if (depEl && !depEl._positionsListenerAttached) {
                                            depEl.addEventListener('change', function(){ openEditPositions(contactId); });
                                            depEl._positionsListenerAttached = true;
                                        }
                                    };

                                    // Attach change listeners for any existing edit-departments selects on page load
                                    document.querySelectorAll('select[id^="edit-departments-"]').forEach(function(el){
                                        var cid = el.id.replace('edit-departments-','');
                                        if (!el._positionsListenerAttached) {
                                            el.addEventListener('change', function(){ openEditPositions(cid); });
                                            el._positionsListenerAttached = true;
                                        }
                                    });

                                    // Close modal on Escape key
                                    document.addEventListener('keydown', function(e) {
                                        if (e.key === 'Escape') {
                                            closeAddContactModal();
                                        }
                                    });
                                </script>
                            @endcan
                        </div>

                        <div id="tab-scheduling" class="tab-panel hidden">
                            @include('productions.partials.scheduling')
                        </div>

                        <div id="tab-notes" class="tab-panel hidden">
                            <div class="text-sm text-gray-600">Notes are visible in the Overview tab.</div>
                        </div>

                        <div id="tab-files" class="tab-panel hidden">
                            <div class="text-sm text-gray-600">Files and attachments can be managed here.</div>
                        </div>

                        <div id="tab-finance" class="tab-panel hidden">
                            <div class="text-sm text-gray-600">Finance and budgets will appear here.</div>
                        </div>
                    </div>
                    <script>
                        (function(){
                            const buttons = document.querySelectorAll('.tab-btn');
                            const panels = document.querySelectorAll('.tab-panel');

                            function activate(tab){
                                panels.forEach(p => p.classList.add('hidden'));
                                document.getElementById('tab-' + tab).classList.remove('hidden');
                                buttons.forEach(b => b.classList.remove('border-blue-600', 'text-blue-600'));
                                const active = Array.from(buttons).find(b => b.dataset.tab === tab);
                                if(active){ active.classList.add('border-blue-600','text-blue-600'); }
                                location.hash = tab;
                                // notify scheduling partial so calendar can resize
                                if (tab === 'scheduling') {
                                    document.dispatchEvent(new CustomEvent('scheduling-tab-shown'));
                                }
                            }

                            buttons.forEach(b => b.addEventListener('click', function(){ activate(this.dataset.tab); }));

                            const initial = (location.hash || '').replace('#','') || 'overview';
                            activate(initial);

                            // Contact edit functions
                            window.editContact = function(contactId) {
                                document.getElementById('edit-contact-' + contactId).classList.remove('hidden');
                            };

                            window.cancelEditContact = function(contactId) {
                                document.getElementById('edit-contact-' + contactId).classList.add('hidden');
                            };
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
