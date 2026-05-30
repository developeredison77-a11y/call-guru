@extends('layouts.dashboard')

@section('title', 'Profile')
@section('page-title', 'Profile')
@section('eyebrow', 'Account')

@section('content')
    <section class="dashboard-panel profile-form-panel">
        <div class="panel-heading">
            <div>
                <p>Personal information</p>
                <h2>Edit Profile</h2>
            </div>
        </div>

        <form class="settings-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            <div class="form-grid">
                <label class="form-field">
                    <span>Full Name</span>
                    <input type="text" name="name" value="{{ old('name', $user?->name) }}" autocomplete="name" required>
                    @error('name') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Country Code</span>
                    <input type="text" name="country_code" value="{{ old('country_code', $user?->country_code ?? '+91') }}" autocomplete="tel-country-code" required>
                    @error('country_code') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Mobile Number</span>
                    <input type="tel" name="mobile_number" value="{{ old('mobile_number', $user?->mobile_number) }}" autocomplete="tel-national" required>
                    @error('mobile_number') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Date of Birth</span>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user?->date_of_birth)->format('Y-m-d')) }}">
                    @error('date_of_birth') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Sex</span>
                    <select name="sex">
                        <option value="">Not specified</option>
                        @foreach (['Male', 'Female', 'Other'] as $sex)
                            <option value="{{ $sex }}" @selected(old('sex', $user?->sex) === $sex)>{{ $sex }}</option>
                        @endforeach
                    </select>
                    @error('sex') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-button">Update Profile</button>
            </div>
        </form>
    </section>
@endsection
