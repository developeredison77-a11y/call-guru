@extends('layouts.dashboard')

@section('title', $module['title'])
@section('page-title', $module['title'])
@section('eyebrow', $module['eyebrow'])

@section('content')
    <section class="dashboard-panel client-listing-panel" data-client-table data-client-label="{{ strtolower($module['title']) }}">
        <div class="panel-heading">
            <div>
                <p>{{ $module['eyebrow'] }}</p>
                <h2>All {{ $module['title'] }}</h2>
            </div>
            <button type="button" class="filter-toggle-button" data-filter-toggle aria-expanded="false">
                <x-dashboard.icon name="filter" />
                <span>Filters</span>
            </button>
        </div>

        <div class="client-toolbar user-toolbar" data-filter-panel hidden>
            <label class="client-search">
                <x-dashboard.icon name="search" />
                <input type="search" data-client-search placeholder="Search {{ strtolower($module['title']) }}, mobile, email">
            </label>

            <select data-client-status aria-label="Filter by status">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <button type="button" class="filter-reset-button" data-filter-reset>
                <x-dashboard.icon name="reset" />
                <span>Reset</span>
            </button>
        </div>

        <div class="responsive-table">
            <table class="advanced-table client-table">
                <thead>
                    <tr>
                        <th><button type="button" data-sort="name">Name</button></th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th><button type="button" data-sort="status">Status</button></th>
                        <th><button type="button" data-sort="joined">Joined</button></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody data-client-body>
                    @forelse ($users as $managedUser)
                        @php
                            $statusLabel = $managedUser->status ? 'Active' : 'Inactive';
                            $initials = collect(explode(' ', $managedUser->name))
                                ->filter()
                                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                ->take(2)
                                ->implode('');
                        @endphp
                        <tr
                            data-name="{{ $managedUser->name }}"
                            data-company="{{ trim(($managedUser->country_code ?? '').' '.$managedUser->mobile_number) }}"
                            data-email="{{ $managedUser->email }}"
                            data-status="{{ $statusLabel }}"
                            data-plan="{{ $module['singular'] }}"
                            data-joined="{{ optional($managedUser->created_at)->format('Y-m-d H:i:s') }}"
                        >
                            <td>
                                <div class="client-person">
                                    <span>{{ $initials ?: 'U' }}</span>
                                    <div>
                                        <strong>{{ $managedUser->name }}</strong>
                                        <small>{{ $module['singular'] }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $managedUser->email ?? '-' }}</td>
                            <td>{{ trim(($managedUser->country_code ?? '').' '.$managedUser->mobile_number) }}</td>
                            <td><span class="status-badge status-{{ strtolower($statusLabel) }}">{{ $statusLabel }}</span></td>
                            <td>{{ optional($managedUser->created_at)->format('M d, Y') ?? '-' }}</td>
                            <td>
                                <div class="table-actions">
                                    <form method="POST" action="{{ route($module['toggleRoute'], $managedUser) }}" data-status-toggle-form>
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="status-toggle {{ $managedUser->status ? 'is-active' : '' }}"
                                            role="switch"
                                            aria-checked="{{ $managedUser->status ? 'true' : 'false' }}"
                                            aria-label="{{ $managedUser->status ? 'Deactivate' : 'Activate' }} {{ $managedUser->name }}"
                                            data-tooltip="{{ $managedUser->status ? 'Deactivate' : 'Activate' }}"
                                        >
                                            <span></span>
                                            <em>{{ $statusLabel }}</em>
                                        </button>
                                    </form>
                                    <a
                                        class="table-action-icon"
                                        href="{{ route($module['editRoute'], $managedUser) }}"
                                        aria-label="Edit {{ $managedUser->name }}"
                                        data-tooltip="Edit"
                                    >
                                        <x-dashboard.icon name="edit" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route($module['deleteRoute'], $managedUser) }}"
                                        data-delete-form
                                        data-delete-name="{{ $managedUser->name }}"
                                        data-delete-type="{{ strtolower($module['singular']) }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="table-action-icon danger-action"
                                            aria-label="Delete {{ $managedUser->name }}"
                                            data-tooltip="Delete"
                                        >
                                            <x-dashboard.icon name="trash" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr data-empty-row>
                            <td colspan="6" class="table-empty-state">No {{ strtolower($module['title']) }} found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer" data-client-footer>
            <span data-client-summary>Showing {{ strtolower($module['title']) }}</span>
            <div class="table-pagination">
                <label class="page-size-control">
                    <span>Rows</span>
                    <select data-client-per-page aria-label="Rows per page">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                    </select>
                </label>
                <div class="pagination-controls">
                    <button type="button" data-page-first aria-label="First page"><x-dashboard.icon name="chevrons-left" /></button>
                    <button type="button" data-page-prev aria-label="Previous page"><x-dashboard.icon name="chevron-left" /></button>
                    <span data-page-current>1</span>
                    <button type="button" data-page-next aria-label="Next page"><x-dashboard.icon name="chevron-right" /></button>
                    <button type="button" data-page-last aria-label="Last page"><x-dashboard.icon name="chevrons-right" /></button>
                </div>
            </div>
        </div>
    </section>

    <div class="confirm-modal" data-delete-confirm-modal hidden>
        <div class="confirm-modal-backdrop" data-delete-cancel></div>
        <section class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-confirm-title" aria-describedby="delete-confirm-message">
            <button type="button" class="confirm-close-button" data-delete-cancel aria-label="Close confirmation">
                <x-dashboard.icon name="close" />
            </button>

            <div class="confirm-dialog-mark">
                <x-dashboard.icon name="trash" />
            </div>

            <div class="confirm-dialog-copy">
                <h2 id="delete-confirm-title">Delete {{ $module['singular'] }}?</h2>
                <span id="delete-confirm-message" data-delete-confirm-message>
                    Are you sure you want to delete this {{ strtolower($module['singular']) }}?
                </span>
            </div>

            <div class="confirm-dialog-actions">
                <button type="button" class="confirm-cancel-button" data-delete-cancel>Cancel</button>
                <button type="button" class="confirm-delete-button" data-delete-confirm>Delete</button>
            </div>
        </section>
    </div>
@endsection
