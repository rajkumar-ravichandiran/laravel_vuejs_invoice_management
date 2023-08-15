<li class="nav-item">
    <a class="nav-link {{ (\Request::routeIs('customers.*')) ? 'active' : '' }}" href="{{ route('customers.index') }}">
        <i class="las la-user"></i> {{ __('Customers') }}
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Request::routeIs('items.*')) ? 'active' : '' }}" href="{{ route('items.index') }}">
        <i class="las la-shapes"></i> {{ __('Items') }}
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Request::routeIs('invoices.*')) ? 'active' : '' }}" href="{{ route('invoices.index') }}">
        <i class="las la-file-invoice"></i> {{ __('Invoices') }}
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Request::routeIs('recurring-invoices.*')) ? 'active' : '' }}" href="{{ route('recurring-invoices.index') }}">
        <i class="las la-sync-alt"></i> {{ __('Recurring Invoices') }}
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Request::routeIs('estimates.*')) ? 'active' : '' }}" href="{{ route('estimates.index') }}">
        <i class="las la-poll-h"></i> {{ __('Estimates') }}
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Request::routeIs('payments.*')) ? 'active' : '' }}" href="{{ route('payments.index') }}">
        <i class="lar la-credit-card"></i> {{ __('Payments') }}
    </a>
</li>
