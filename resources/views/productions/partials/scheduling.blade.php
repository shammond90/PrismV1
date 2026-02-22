{{-- Productions > Scheduling tab --}}
<div>
    {{-- ───── Toolbar: View toggle + Add Event ───── --}}
    <div class="flex items-center justify-between mb-4">
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <button id="toggle-calendar" type="button"
                class="view-toggle-btn px-4 py-2 text-sm font-medium border rounded-l-md
                       bg-blue-600 text-white border-blue-600">
                Calendar
            </button>
            <button id="toggle-list" type="button"
                class="view-toggle-btn px-4 py-2 text-sm font-medium border rounded-r-md
                       bg-white text-gray-700 border-gray-300 hover:bg-gray-50
                       dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                List
            </button>
        </div>

        <div class="flex gap-2">
            <button id="print-list-btn" type="button" onclick="window.print()"
                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hidden
                       dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                Print / Export
            </button>

            @can('events.create')
                <button id="open-add-event" type="button" onclick="openAddEventModal()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                    + Add Event
                </button>
            @endcan
        </div>
    </div>

    {{-- ───── Calendar View (default visible) ───── --}}
    <div id="calendar-view">
        <div id="fc"></div>
    </div>

    {{-- ───── List View (hidden by default) ───── --}}
    <div id="list-view" class="hidden">
        @if($events->isEmpty())
            <p class="text-gray-500 text-sm py-6 text-center">No events scheduled yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm" id="events-list-table">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Name</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Type</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Start</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">End</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Space</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Departments</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($events as $evt)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer event-list-row"
                                data-event-id="{{ $evt->id }}">
                                <td class="px-4 py-3 font-medium text-blue-600">{{ $evt->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full inline-block"
                                              style="background:{{ \App\Http\Controllers\EventController::colorForType($evt->event_type) }}"></span>
                                        {{ $evt->event_type ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ optional($evt->start_at)->format('M j, Y g:i A') ?? '—' }}</td>
                                <td class="px-4 py-3">{{ optional($evt->end_at)->format('M j, Y g:i A') ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $evt->space?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ is_array($evt->departments) ? implode(', ', $evt->departments) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ───── Add / Edit Event Modal ───── --}}
    <div id="event-modal" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            {{-- backdrop --}}
            <div class="fixed inset-0 bg-black/50" onclick="closeEventModal()"></div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl relative z-10">
                {{-- header --}}
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                    <h3 id="event-modal-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Add Event</h3>
                    <button type="button" onclick="closeEventModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-xl leading-none">&times;</button>
                </div>

                {{-- form --}}
                <form id="event-form" action="{{ route('productions.events.store', $production) }}" method="POST">
                    @csrf
                    <input type="hidden" id="event-method" name="_method" value="POST" disabled>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ev-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Name <span class="text-red-500">*</span></label>
                                <input id="ev-name" name="name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            </div>
                            <div>
                                <label for="ev-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Type</label>
                                <select id="ev-type" name="event_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                    <option value="">— Select —</option>
                                    @foreach($eventTypes ?? [] as $et)
                                        <option value="{{ $et->name }}" data-color="{{ $et->color }}">{{ $et->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ev-start" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start <span class="text-red-500">*</span></label>
                                <input id="ev-start" type="datetime-local" name="start_at" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            </div>
                            <div>
                                <label for="ev-end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End</label>
                                <input id="ev-end" type="datetime-local" name="end_at"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ev-space" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Space</label>
                                <select id="ev-space" name="space_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                    <option value="">None</option>
                                    @foreach($spaces as $sp)
                                        <option value="{{ $sp->id }}">
                                            @if($sp->building) {{ $sp->building->name }} — @endif{{ $sp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="ev-departments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departments</label>
                                <select id="ev-departments" name="departments[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                    @foreach($departments as $d)
                                        <option value="{{ $d->name }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Select multiple departments (hold Ctrl/Cmd).</p>
                            </div>
                        </div>

                        <div>
                            <label for="ev-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea id="ev-notes" name="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"></textarea>
                        </div>
                    </div>

                    {{-- footer --}}
                    <div class="flex items-center justify-between p-4 border-t dark:border-gray-700">
                        <div>
                            <button type="button" id="event-delete-btn"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 hidden">
                                Delete Event
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="closeEventModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ───── Print styles ───── --}}
    <style>
        @media print {
            body * { visibility: hidden; }
            #events-list-table, #events-list-table * { visibility: visible; }
            #events-list-table { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>

    {{-- ───── FullCalendar v6 (CDN — single bundle includes all standard plugins) ───── --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ——— State ——— */
        let currentView   = 'calendar';   // 'calendar' | 'list'
        let editingEventId = null;

        const calendarViewEl = document.getElementById('calendar-view');
        const listViewEl     = document.getElementById('list-view');
        const toggleCalBtn   = document.getElementById('toggle-calendar');
        const toggleListBtn  = document.getElementById('toggle-list');
        const printBtn       = document.getElementById('print-list-btn');
        const csrfToken      = '{{ csrf_token() }}';

        /* ——— FullCalendar ——— */
        const calendar = new FullCalendar.Calendar(document.getElementById('fc'), {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,timeGridDay'
            },
            selectable: true,
            editable: true,
            eventSources: [{
                url: '{{ route("productions.events.index", $production) }}',
                extraParams: { json: 1 }
            }],

            /* click → open edit modal */
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                openEditEventModal(info.event.id);
            },

            /* drag / resize → PATCH start & end */
            eventDrop:   function (info) { patchEventTimes(info); },
            eventResize: function (info) { patchEventTimes(info); },

            /* click empty slot → open add modal with pre-filled start */
            dateClick: function (info) {
                openAddEventModal(info.dateStr);
            }
        });
        calendar.render();

        /* ——— Fix first-load: calendar may render inside hidden tab ——— */
        document.addEventListener('scheduling-tab-shown', function () {
            calendar.updateSize();
        });
        // Also try an immediate updateSize after a short delay (covers direct #scheduling URL)
        setTimeout(function () { calendar.updateSize(); }, 150);

        /* helper: send start/end after drag or resize */
        function patchEventTimes(info) {
            const payload = {
                start_at: info.event.start.toISOString(),
                end_at:   info.event.end ? info.event.end.toISOString() : info.event.start.toISOString()
            };
            fetch('/events/' + info.event.id, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            }).then(r => {
                if (!r.ok) { info.revert(); console.error('Drag/resize update failed'); }
            });
        }

        /* ——— View Toggle ——— */
        function showView(view) {
            currentView = view;
            if (view === 'calendar') {
                calendarViewEl.classList.remove('hidden');
                listViewEl.classList.add('hidden');
                printBtn && printBtn.classList.add('hidden');
                toggleCalBtn.className  = 'view-toggle-btn px-4 py-2 text-sm font-medium border rounded-l-md bg-blue-600 text-white border-blue-600';
                toggleListBtn.className = 'view-toggle-btn px-4 py-2 text-sm font-medium border rounded-r-md bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600';
                calendar.updateSize();
            } else {
                calendarViewEl.classList.add('hidden');
                listViewEl.classList.remove('hidden');
                printBtn && printBtn.classList.remove('hidden');
                toggleListBtn.className = 'view-toggle-btn px-4 py-2 text-sm font-medium border rounded-r-md bg-blue-600 text-white border-blue-600';
                toggleCalBtn.className  = 'view-toggle-btn px-4 py-2 text-sm font-medium border rounded-l-md bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600';
            }
        }

        toggleCalBtn.addEventListener('click', function () { showView('calendar'); });
        toggleListBtn.addEventListener('click', function () { showView('list'); });

        /* ——— List rows clickable ——— */
        document.querySelectorAll('.event-list-row').forEach(function (row) {
            row.addEventListener('click', function () {
                openEditEventModal(row.dataset.eventId);
            });
        });

        /* ——— Modal helpers ——— */
        const modal      = document.getElementById('event-modal');
        const form       = document.getElementById('event-form');
        const methodEl   = document.getElementById('event-method');
        const titleEl    = document.getElementById('event-modal-title');
        const deleteBtn  = document.getElementById('event-delete-btn');

        const storeUrl   = '{{ route("productions.events.store", $production) }}';

        function openModal()  { modal.classList.remove('hidden'); }
        function closeEventModal() {
            modal.classList.add('hidden');
            resetForm();
        }
        window.closeEventModal = closeEventModal;

        function resetForm() {
            editingEventId = null;
            form.reset();
            form.action       = storeUrl;
            methodEl.disabled = true;
            methodEl.value    = 'POST';
            titleEl.textContent = 'Add Event';
            deleteBtn.classList.add('hidden');
            deleteBtn.onclick = null;
        }

        /* ——— Open Add modal ——— */
        window.openAddEventModal = function (startStr) {
            resetForm();
            if (startStr) {
                var dt = startStr.length <= 10 ? (startStr + 'T09:00') : startStr.slice(0, 16);
                document.getElementById('ev-start').value = dt;
                // default end to +30 minutes when adding
                try {
                    var s = new Date(dt);
                    if (!isNaN(s)) {
                        s = new Date(s.getTime() + 30*60000);
                        var pad = function(n){ return n<10 ? '0'+n : n };
                        var endVal = s.getFullYear() + '-' + pad(s.getMonth()+1) + '-' + pad(s.getDate()) + 'T' + pad(s.getHours()) + ':' + pad(s.getMinutes());
                        document.getElementById('ev-end').value = endVal;
                    }
                } catch (e) { /* ignore */ }
            }
            openModal();
        };

        /* ——— Open Edit modal ——— */
        window.openEditEventModal = function (id) {
            resetForm();
            editingEventId = id;
            titleEl.textContent = 'Edit Event';

            fetch('/events/' + id, {
                headers: { 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json(); })
            .then(function (e) {
                form.action     = '/events/' + e.id;
                methodEl.value  = 'PUT';
                methodEl.disabled = false;

                document.getElementById('ev-name').value        = e.name || '';
                document.getElementById('ev-type').value        = e.event_type || '';
                document.getElementById('ev-start').value       = e.start_at ? e.start_at.replace(' ', 'T').slice(0, 16) : '';
                document.getElementById('ev-end').value         = e.end_at   ? e.end_at.replace(' ', 'T').slice(0, 16) : '';
                document.getElementById('ev-space').value       = e.space_id || '';
                document.getElementById('ev-departments').value = Array.isArray(e.departments) ? e.departments.join(', ') : '';
                document.getElementById('ev-notes').value       = e.notes || '';

                // show delete button
                deleteBtn.classList.remove('hidden');
                deleteBtn.onclick = function () {
                    if (!confirm('Are you sure you want to delete this event?')) return;
                    fetch('/events/' + e.id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    }).then(function (r) {
                        closeEventModal();
                        // reload same page on scheduling tab
                        window.location.href = '{{ route("productions.show", $production) }}#scheduling';
                        window.location.reload();
                    });
                };

                openModal();
            })
            .catch(function (err) {
                console.error('Failed to load event', err);
                alert('Could not load event details.');
            });
        };

        /* ——— End >= Start validation ——— */
        var startInput = document.getElementById('ev-start');
        var endInput   = document.getElementById('ev-end');
        var endError   = document.createElement('p');
        endError.className = 'text-red-500 text-xs mt-1 hidden';
        endError.id = 'ev-end-error';
        endError.textContent = 'End date/time cannot be earlier than start date/time.';
        endInput.parentNode.appendChild(endError);

        function pad(n){ return n < 10 ? '0'+n : n }
        function formatLocal(dt){
            return dt.getFullYear() + '-' + pad(dt.getMonth()+1) + '-' + pad(dt.getDate()) + 'T' + pad(dt.getHours()) + ':' + pad(dt.getMinutes());
        }

        function addMinutesToLocalInput(value, minutes){
            if(!value) return '';
            var d = new Date(value);
            if (isNaN(d)) {
                d = new Date(value.replace('T',' '));
            }
            d = new Date(d.getTime() + minutes*60000);
            return formatLocal(d);
        }

        function validateDateRange() {
            if (startInput.value && !endInput.value) {
                // default end to +30min
                endInput.value = addMinutesToLocalInput(startInput.value, 30);
            }
            if (startInput.value && endInput.value && endInput.value < startInput.value) {
                endInput.classList.add('border-red-500');
                endError.classList.remove('hidden');
                return false;
            }
            endInput.classList.remove('border-red-500');
            endError.classList.add('hidden');
            return true;
        }
        startInput.addEventListener('change', function(){
            if(!endInput.value || endInput.value <= startInput.value){
                endInput.value = addMinutesToLocalInput(startInput.value, 30);
            }
            validateDateRange();
        });
        endInput.addEventListener('change', validateDateRange);

        /* ——— Form submit: validate + transform departments CSV → proper array inputs ——— */
        form.addEventListener('submit', function (e) {
            // block if end < start
            if (!validateDateRange()) {
                e.preventDefault();
                return;
            }

            // remove any previously-injected department inputs
            form.querySelectorAll('input[name="departments[]"]').forEach(function (el) { el.remove(); });

            var raw   = document.getElementById('ev-departments').value || '';
            var parts = raw.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
            parts.forEach(function (dept) {
                var h = document.createElement('input');
                h.type = 'hidden'; h.name = 'departments[]'; h.value = dept;
                form.appendChild(h);
            });
            // if no departments, send empty string so validation passes
            if (parts.length === 0) {
                var h = document.createElement('input');
                h.type = 'hidden'; h.name = 'departments'; h.value = '';
                form.appendChild(h);
            }
        });

        /* ——— Escape key closes modal ——— */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeEventModal();
            }
        });

    }); // end DOMContentLoaded
    </script>
</div>
