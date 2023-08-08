<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-dashboard nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Users</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('system-user') }}"><i class="nav-icon la la-user-shield"></i> <span>System Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Customers</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('block-user') }}'><i class='nav-icon la la-user-lock'></i> Block Users</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-indent"></i> Subscription</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('subscription-plan') }}"><i class="nav-icon la la-list"></i> <span>Plans</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('subscription-plan-content') }}"><i class="nav-icon la la-list-alt"></i> <span>Plan Contents</span></a></li>
    </ul>
</li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('subscription') }}'><i class="nav-icon la la-universal-access"></i> Memberships</a></li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('transaction') }}'><i class='nav-icon la la-exchange'></i> Transactions</a></li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('testimonial') }}'><i class='nav-icon la la-quote-left'></i> Testimonials</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-gear"></i> Settings</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('home-page-setting') }}'><i class="la la-home nav-icon"></i> Home Page</a></li>
{{--        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('planning') }}"><i class="nav-icon la la-list"></i> <span>Planning</span></a></li>--}}
{{--        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('planning-content') }}"><i class="nav-icon la la-list-ul"></i> <span>Planning Content</span></a></li>--}}
    </ul>
</li>

