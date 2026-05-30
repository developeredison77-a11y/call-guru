@php
    $editing = isset($record);
@endphp

@extends('layouts.dashboard')

@section('title', ($editing ? 'Edit ' : 'Create ').$module['singular'])
@section('page-title', ($editing ? 'Edit ' : 'Create ').$module['singular'])
@section('eyebrow', $module['eyebrow'])

@section('content')
    <section class="dashboard-panel profile-form-panel">
        <div class="panel-heading form-panel-heading">
            <div>
                <p>{{ $module['eyebrow'] }}</p>
                <h2>{{ $editing ? 'Edit' : 'Create' }} {{ $module['singular'] }}</h2>
            </div>
            <a href="{{ route($module['indexRoute']) }}" class="icon-button panel-back-button" aria-label="Back to {{ $module['title'] }}">
                <x-dashboard.icon name="chevron-left" />
            </a>
        </div>

        <form
            class="settings-form"
            method="POST"
            action="{{ $editing ? route($module['updateRoute'], $record) : route($module['storeRoute']) }}"
            novalidate
        >
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div class="form-grid">
                @foreach ($module['fields'] as $field)
                    <label class="form-field {{ ($field['wide'] ?? false) ? 'form-field-wide' : '' }}">
                        <span>{{ $field['label'] }}</span>
                        @if ($field['type'] === 'textarea')
                            <textarea name="{{ $field['name'] }}" rows="{{ $field['rows'] ?? 5 }}" required>{{ old($field['name'], $record->{$field['name']} ?? '') }}</textarea>
                        @else
                            <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ old($field['name'], $record->{$field['name']} ?? '') }}" required>
                        @endif
                        @error($field['name']) <small>{{ $message }}</small> @enderror
                    </label>
                @endforeach

                @if ($module['showStatusField'])
                    <label class="form-field">
                        <span>Status</span>
                        <select name="status" required>
                            <option value="1" @selected((string) old('status', $record->status ?? 1) === '1')>Active</option>
                            <option value="0" @selected((string) old('status', $record->status ?? 1) === '0')>Inactive</option>
                        </select>
                        @error('status') <small>{{ $message }}</small> @enderror
                    </label>
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-button">{{ $editing ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </section>
@endsection
