@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">Edit Email</h2>

    <form action="{{ route('emails.update', $email) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="return_to" value="{{ request('return_to', url()->previous()) }}" />

        <div class="grid grid-cols-1 gap-4">
            <label class="block">
                <span class="text-sm text-gray-700">Type</span>
                <input type="text" name="type" value="{{ old('type', $email->type) }}" class="input mt-1 w-full" placeholder="e.g. Work, Personal" />
            </label>

            <label class="block">
                <span class="text-sm text-gray-700">Company (optional)</span>
                <select name="company_id" class="input mt-1 w-full">
                    <option value="">-- None --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $email->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm text-gray-700">Email address</span>
                <input type="email" name="address" value="{{ old('address', $email->address) }}" required class="input mt-1 w-full" />
            </label>

            <label class="flex items-center gap-2">
                <input type="hidden" name="primary" value="0" />
                <input type="checkbox" name="primary" value="1" {{ old('primary', $email->primary) ? 'checked' : '' }} />
                <span class="text-sm text-gray-700">Primary</span>
            </label>

            <label class="block">
                <span class="text-sm text-gray-700">Notes</span>
                <textarea name="notes" class="input mt-1 w-full" rows="3">{{ old('notes', $email->notes) }}</textarea>
            </label>

            <div class="flex items-center gap-2">
                <button class="btn">Save</button>
                <a href="{{ request('return_to', route('contacts.show', $email->contact_id)) }}" class="ml-2 text-gray-600">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
