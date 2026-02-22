@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">Edit Company</h2>

    <form action="{{ route('companies.update', $company) }}" method="POST">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc ms-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4">
            <div>
                <input name="name" placeholder="Name" value="{{ old('name', $company->name) }}" required class="input" />
                @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <input name="industry" placeholder="Industry" value="{{ old('industry', $company->industry) }}" class="input" />
                @error('industry') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <input name="website" placeholder="Website" value="{{ old('website', $company->website) }}" class="input" />
                @error('website') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <textarea name="notes" placeholder="Notes" class="input">{{ old('notes', $company->notes) }}</textarea>
                @error('notes') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-4">
            <button class="btn">Update</button>
            <a href="{{ route('companies.index') }}" class="ml-4">Cancel</a>
        </div>
    </form>
</div>
@endsection
