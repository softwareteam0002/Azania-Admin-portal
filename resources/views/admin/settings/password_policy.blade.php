@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(Session::has('color'))
                <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show" role="alert">
                    {{ Session::get('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="col-md-4 text-right">
            <!-- Date and Time-->
            <p id="todayDate" class="small"><b></b></p>
            <h5 id="todayTime" class=""><b></b></h5>
        </div>
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item active">Security Policies</li>
        </ol>
    </nav>

    <h5>Password Policies</h5>
    <hr/>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <h6>Create Policy</h6>
                        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                        <form action="{{ url('admin/password_policy/store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Min Length:<span class="text-danger">*</span></label>
                                        <input type="text" name="min_length" class="form-control form-control-sm">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Complexity:<span class="text-danger">*</span></label>
                                        <select class="form-control form-control-sm" name="complexity">
                                            <option value="" selected disabled>--Select Complexity--</option>
                                            <option value="strong">Strong <span class="text-sm">(combination of
                                                    Letters, symbols and numbers)</span>
                                            </option>
                                            <option value="medium">Medium <span class="text-sm">(combination of either
                                                    Letters, symbols or numbers)</span>
                                            </option>
                                            <option value="weak">Weak <span class="text-sm">(any password)
                                                </span></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Max Attempts:<span class="text-danger">*</span></label>
                                        <input type="text" name="max_attempts" class="form-control form-control-sm">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">History:<span class="text-danger">*</span></label>
                                        <input type="text" name="pass_history" class="form-control form-control-sm">
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Expiry (Days):<span class="text-danger">*</span></label>
                                        <input type="text" name="expiry_period" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">OTP Length:<span class="text-danger">*</span></label>
                                        <input type="text" name="otp_length" class="form-control form-control-sm">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">OTP Attempts:<span class="text-danger">*</span></label>
                                        <input type="text" name="otp_attempts" class="form-control form-control-sm">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">OTP Expiry (Minutes):<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="otp_expiry" class="form-control
                                        form-control-sm">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <hr/>
                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                    <a href="" data-toggle="collapse"
                                       class="btn btn-sm btn-link">Cancel</a>
                                </div>

                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-sm table-hover datatable">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Min Length</th>
                            <th>Max Attempts</th>
                            <th>Expiry (Days)</th>
                            <th>Complexity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @foreach($policies as $index => $policy)
                                <td>{{$index + 1}}</td>
                                <td>{{$policy->min_length}}</td>
                                <td>{{$policy->max_attempts}}</td>
                                <td>{{$policy->expiry_days}}</td>
                                @if($policy->complexity == 'strong')
                                    <td>
                                        <span class="badge badge-info">Mandatory</span>
                                        <ul class="mb-0 list-unstyled">
                                            <li><span class="badge badge-success">✓</span> Letters</li>
                                            <li><span class="badge badge-success">✓</span> Numbers</li>
                                            <li><span class="badge badge-success">✓</span> Symbols/Characters</li>
                                        </ul>
                                    </td>
                                @elseif($policy->complexity == 'medium')
                                    <td>
                                        <span class="badge badge-info">Optional</span>
                                        <ul class="mb-0 list-unstyled">
                                            <li><span class="badge badge-success">✓</span> Letters</li>
                                            <li><span class="badge badge-success">✓</span> Numbers</li>
                                            <li><span class="badge badge-success">✓</span> Symbols/Characters</li>
                                        </ul>
                                    </td>
                                @else
                                    <td>
                                        <span class="badge badge-info">Optional</span>
                                        <ul class="mb-0 list-unstyled">
                                            <li><span class="badge badge-success">✓</span> Letters</li>
                                            <li><span class="badge badge-success">✓</span> Numbers</li>
                                            <li><span class="badge badge-success">✓</span> Symbols/Characters</li>
                                        </ul>
                                    </td>
                                @endif
                                @if($policy->status == 0)
                                    <td><span class="badge badge-danger">Inactive</span></td>
                                @elseif($policy->status == 1)
                                    <td><span class="badge badge-success">Active</span></td>
                                @else
                                    <td><span class="badge badge-warning">Unknown</span></td>
                                @endif
                                <td></td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        @parent
        <script>
            $(function () {
                $('.datatables').DataTable()
            });
        </script>
    @endsection
@endsection
