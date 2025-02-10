<div class="sidebar menu-background">
    <nav class="sidebar-nav" style="width:max-content">
        <ul class="nav ">
            <li class="nav-item">
                <a href="{{ url("home") }}" class="nav-link">
                    <i class="nav-icon fa fa-tachometer menu-font">
                    </i>&nbsp;&nbsp;&nbsp;
                    {{ trans('global.dashboard') }}
                </a>
            </li>

            @can('ab_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle">
                        <i class="fa fa-book nav-icon menu-font">
                        </i>&nbsp;&nbsp;&nbsp;
                        Agency Banking
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('ab_settings_access')
                            <li class="nav-item nav-dropdown text-left">
                                <a class="nav-link  nav-dropdown-toggle">&nbsp;&nbsp;&nbsp;
                                    Settings
                                </a>
                                <ul class="nav-dropdown-items text-center">
                                    @can('ab_settings_bank_access')
                                        <li class="nav-item text-left">
                                            <a href="{{ url('agency/view_bank') }}"
                                               class="nav-link {{ request()->is('agency/view_bank') || request()->is('agency/view_bank/*') ? 'active' : '' }}">
                                                Bank
                                            </a>
                                        </li>
                                    @endcan
{{--                                    <li class="nav-item text-left">--}}
{{--                                        <a href="{{ url('agency/view_gepg_institution') }}"--}}
{{--                                           class="nav-link {{ request()->is('agency/view_gepg_institution') || request()->is('agency/view_gepg_institution/*') ? 'active' : '' }}">--}}
{{--                                            GEPG Institution--}}
{{--                                        </a>--}}
{{--                                    </li>--}}
                                    <li class="nav-item text-left">
                                        <a href="{{ url('agency/view_branch') }}"
                                           class="nav-link {{ request()->is('agency/view_branch') || request()->is('agency/view_branch/*') ? 'active' : '' }}">
                                            Branch
                                        </a>
                                    </li>
{{--                                    <li class="nav-item text-left">--}}
{{--                                        <a href="{{ url('agency/view_account_product') }}"--}}
{{--                                           class="nav-link {{ request()->is('agency/view_account_product') || request()->is('agency/view_account_product/*') ? 'active' : '' }}">--}}
{{--                                            Account Product--}}
{{--                                        </a>--}}
{{--                                    </li>--}}
                                    @can('ab_settings_biller_group_access')
                                        <li class="nav-item text-left">
                                            <a href="{{ url('agency/view_biller_group') }}"
                                               class="nav-link {{ request()->is('agency/view_biller_group') || request()->is('agency/view_biller_group/*') ? 'active' : '' }}">
                                                Biller Group
                                            </a>
                                        </li>
                                    @endcan
                                    @can('ab_settings_biller_access')
                                        <li class="nav-item text-left">
                                            <a href="{{ url('agency/view_biller') }}"
                                               class="nav-link {{ request()->is('agency/view_biller') || request()->is('agency/view_biller/*') ? 'active' : '' }}">
                                                Biller
                                            </a>
                                        </li>
                                    @endcan
{{--                                    @can('ab_settings_commission_access')--}}
{{--                                        <li class="nav-item text-left">--}}
{{--                                            <a href="{{ url('agency/commissions') }}"--}}
{{--                                               class="nav-link {{ request()->is('agency/commissions/') || request()->is('agency/commissions/*') ? 'active' : '' }}">--}}
{{--                                                Commission--}}
{{--                                            </a>--}}
{{--                                        </li>--}}
{{--                                    @endcan--}}
                                    @can('ab_settings_security_policy_access')
                                        <li class="nav-item text-left">
                                            <a href="{{ url('agency/securitypolicies') }}"
                                               class="nav-link {{ request()->is('agency/securitypolicies') || request()->is('agency/securitypolicies/*') ? 'active' : '' }}">
                                                Security Policies
                                            </a>
                                        </li>
                                    @endcan
                                    @can('ab_settings_institution_account_access')
                                        <li class="nav-item text-left">
                                            <a href="{{ url('agency/institutionaccounts') }}"
                                               class="nav-link {{ request()->is('agency/institutionaccounts') || request()->is('agency/institutionaccounts/*') ? 'active' : '' }}">
                                                Institution Accounts
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('ab_agents_access')
                            <li class="nav-item">
                                <a href="{{ url('agency/users') }}"
                                   class="nav-link {{ request()->is('agency/users') || request()->is('agency/users/*') ? 'active' : '' }}">
                                    Manage Agent
                                </a>
                            </li>
                        @endcan
                        @can('ab_devices_access')
                            <li class="nav-item">
                                <a href="{{ url('admin/devices') }}"
                                   class="nav-link {{ request()->is('admin/ib/transaction') || request()->is('admin/ib/transaction/*') ? 'active' : '' }}">
                                    Manage Device
                                </a>
                            </li>
                        @endcan
                        @can('ab_transactions_access')
                            <li class="nav-item">
                                <a href="{{ url('agency/transactions') }}"
                                   class="nav-link {{ request()->is('agency/transactions') || request()->is('agency/transactions/*') ? 'active' : '' }}">
                                    Transactions
                                </a>
                            </li>
                        @endcan
                        @can('ab_service_charges_access')
                            <li class="nav-item">
                                <a href="{{ url('agency/account/service') }}"
                                   class="nav-link {{ request()->is('agency/account/service') || request()->is('agency/account/service/*') ? 'active' : '' }}">
                                    Service Accounts
                                </a>
                            </li>
                        @endcan
                        @can('ab_report_access')
                            <li class="nav-item text-left">
                                <a href="{{ url('agency/reports') }}"
                                   class="nav-link {{ request()->is('agency/reports') ? 'active':'' }}">
                                    Reports
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('um_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle">
                        <i class="fa fa-users nav-icon menu-font">
                        </i>&nbsp;&nbsp;&nbsp;
                        Manage Users
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('um_permissions_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.permissions.index") }}"
                                   class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                                    <i class="fas fa-unlock-alt nav-icon"></i>
                                    {{ trans('global.permission.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('um_roles_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.roles.index") }}"
                                   class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                    <i class="fas fa-briefcase nav-icon"></i>
                                    {{ trans('global.role.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('um_users_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.users.index") }}"
                                   class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                    <i class="fas fa-user nav-icon"></i>
                                    {{ trans('global.user.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('um_audit_trail_access')
                            <li class="nav-item">
                                <a href="{{ url('admin/audit_trail') }}"
                                   class="nav-link {{ request()->is('audit_trail') || request()->is('audit_trail/*') ? 'active' : '' }}">
                                    <i class="fas fa-user nav-icon"></i>
                                    Audit Trail
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('um_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle">
                        <i class="fa fa-cog nav-icon menu-font">
                        </i>&nbsp;&nbsp;&nbsp;
                        System Settings
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('um_permissions_access')
                            <li class="nav-item">
                                <a href="{{url('admin/password_policy')}}"
                                   class="nav-link {{ request()->is('admin/password_policy') || request()->is('admin/password_policy/*') ? 'active' : '' }}">
                                    <i class="fas fa-unlock-alt nav-icon"></i>
                                    Password Policy
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            <li class="nav-item">
                <a href="#" class="nav-link"
                   onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fa fa-sign-out menu-font">
                    </i>&nbsp;&nbsp;&nbsp;
                    {{ trans('global.logout') }}
                </a>
            </li>

        </ul>

        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 869px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 415px;"></div>
        </div>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
