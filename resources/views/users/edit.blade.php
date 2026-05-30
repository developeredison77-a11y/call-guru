@extends('layouts.dashboard')

@section('title', 'Edit '.$module['singular'])
@section('page-title', 'Edit '.$module['singular'])
@section('eyebrow', $module['eyebrow'])

@section('content')
    <section class="dashboard-panel profile-form-panel">
        <div class="panel-heading">
            <div>
                <p>{{ $module['eyebrow'] }}</p>
                <h2>Edit {{ $module['singular'] }}</h2>
            </div>
            <a
                href="{{ route($module['indexRoute']) }}"
                class="icon-button panel-back-button"
                aria-label="Back to {{ $module['title'] }}"
            >
                <x-dashboard.icon name="chevron-left" />
            </a>
        </div>

        <form class="settings-form" method="POST" action="{{ route($module['updateRoute'], $managedUser) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="form-grid">
                <label class="form-field">
                    <span>Full Name</span>
                    <input type="text" name="name" value="{{ old('name', $managedUser->name) }}" autocomplete="name" required>
                    @error('name') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', $managedUser->email) }}" autocomplete="email">
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Country Code</span>
                    <input type="text" name="country_code" value="{{ old('country_code', $managedUser->country_code ?? '+91') }}" autocomplete="tel-country-code" required>
                    @error('country_code') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Mobile Number</span>
                    <input type="tel" name="mobile_number" value="{{ old('mobile_number', $managedUser->mobile_number) }}" autocomplete="tel-national" required>
                    @error('mobile_number') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Date of Birth</span>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($managedUser->date_of_birth)->format('Y-m-d')) }}">
                    @error('date_of_birth') <small>{{ $message }}</small> @enderror
                </label>

                <label class="form-field">
                    <span>Sex</span>
                    <select name="sex">
                        <option value="">Not specified</option>
                        @foreach (['Male', 'Female', 'Other'] as $sex)
                            <option value="{{ $sex }}" @selected(old('sex', $managedUser->sex) === $sex)>{{ $sex }}</option>
                        @endforeach
                    </select>
                    @error('sex') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-button">Update {{ $module['singular'] }}</button>
            </div>
        </form>
    </section>
@endsection
