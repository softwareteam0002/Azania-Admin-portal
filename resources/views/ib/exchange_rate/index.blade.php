@extends('layouts.admin')
@section('content')

    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(session('notification') && session('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible">
                    <ul>
                        <li>{{Session::get('message')}}</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($notification) && isset($color))
            <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
                {{ $notification }}
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
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Internet Banking</a></li>
            <li class="breadcrumb-item active">Manage currencies</li>
        </ol>
    </nav>


    <h5>Manage currencies</h5>
    <hr/>



    @can('ib_users_create')
        <div class="card">
            <div class="card-body">
                <h6 class="">Create exchange rates form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                 <form action="{{ route('ib.exchange_rate.import') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                  <input type="file" name="file" class="form-control">
                  <br>
                  <button class="btn btn-success">Upload File</button>
                </form>
                <form action="{{ route('ib.exchange_rate.store') }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="row">
            
                        <div class="col-md-4">
                            <div id="name_input_group" class="form-group has-error {{ $errors->has('foreign_currency') ? 'has-error' : '' }}">
                                <label for="foreign_currency">Foreign Currency:<span class="text-danger">*</span></label>
                                <input type="text" id="foreign_currency" name="foreign_currency" class="form-control form-control-sm" value="{{ old('foreign_currency', isset($user) ? $user->foreign_currency : '') }}" placeholder="Enter the full foreign_currency here.">
                                @if($errors->has('foreign_currency'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('foreign_currency') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('global.exchange_rate.fields.foreign_currency_helper') }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('currency_code') ? 'has-error' : '' }}">
                                <label for="currency_code">{{ trans('global.exchange_rate.fields.currency_code') }}:<span class="text-danger">*</span></label>
                                <input type="text" id="currency_code" name="currency_code" class="form-control form-control-sm" value="{{ old('currency_code', isset($user) ? $user->currency_code : '') }}" placeholder="Enter currency code here.">
                                @if($errors->has('currency_code'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('currency_code') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('global.exchange_rate.fields.currency_code_helper') }}
                                </p>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('mean_rate') ? 'has-error' : '' }}">
                                <label for="mean_rate">{{ trans('global.exchange_rate.fields.mean_rate') }}:</label>
                                <input type="text" id="mean_rate" name="mean_rate" class="form-control form-control-sm number" value="{{ old('mean_rate') }}" placeholder="Enter mean rate number here." >
                                @if($errors->has('mean_rate'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('mean_rate') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                <!-- {{ trans('global.phone') }} -->
                                </p>
                            </div>
                        </div>
                             <div class="col-md-3">
                            <div class="form-group {{ $errors->has('buying_price') ? 'has-error' : '' }}">
                                <label for="buying_price">{{ trans('global.exchange_rate.fields.buying_price') }}:</label>
                                <input type="text" id="buying_price" name="buying_price" class="form-control form-control-sm number" value="{{ old('buying_price') }}" placeholder="Enter buying_price number here.">
                                @if($errors->has('buying_price'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('buying_price') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                <!-- {{ trans('global.phone') }} -->
                                </p>
                            </div>
                        </div>

                             <div class="col-md-3">
                            <div class="form-group {{ $errors->has('selling_price') ? 'has-error' : '' }}">
                                <label for="selling_price">{{ trans('global.exchange_rate.fields.selling_price') }}:</label>
                                <input type="text" id="selling_price" name="selling_price" class="form-control form-control-sm number" value="{{ old('selling_price') }}" placeholder="Enter selling_price number here.">
                                @if($errors->has('selling_price'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('selling_price') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                <!-- {{ trans('global.phone') }} -->
                                </p>
                            </div>
                        {{--<div class="col-md-2">
                            <div class="form-group">
                                <label for="types">OTP Options:<span class="text-danger">*</span></label>
                                <select name="option_id" class="form-control  form-control-sm">
                                    <option value="0" >Select OTP Option</option>
                                    @foreach($options as $option)
                                        <option value="{{ strtoupper($option->option_name) }}" >{{ strtoupper($option->option_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>--}}

                        {{-- Since all non cooperate users are customers , so no need to select just bind the value--}}
                        <input type="hidden" name="role_id" value="3">

{{--                        <div class="col-md-4">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="roles">User Role:<span class="text-danger">*</span></label>--}}
{{--                                <select name="role_id" class="form-control form-control-sm">--}}
{{--                                    <option value="0" >Select Role</option>--}}
{{--                                    @foreach ($roles as $role)--}}
{{--                                    <option value="{{ $role->id }}" >--}}
{{--                                        {{$role->display_name}}--}}
{{--                                    </option>--}}

{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}


{{--                        </div>--}}

                     
                    </div>
                       <div class="col-md-12">
                            <hr/>
                            <!-- <input class="btn btn-success form-control" type="submit" value="{{ trans('global.save') }}"> -->
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                        </div>
                </form>
            </div>
        </div>



    @endcan              


    <h5>Registered currencies.</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <h6 class="mb-4">All registered currencies.</h6>
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr><th width="10"> </th>
                        <th>Foreign Currency</th>
                        <th>Currency Code</th>
                        <th>Mean Rate</th>
                        <th>Buying Price</th>
                        <th>Selling Price</th>
                        <th>Added By</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($currency_rates as $key => $currency)
                    <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$currency->id}}" id="loadingModel" data="{{$currency->id}}">
                        <td></td>
                        <td>{{$currency->foreign_currency}}</td>
                        <td>{{$currency->currency_code}}</td>
                        <td>{{number_format($currency->mean_rate, 2, '.', ',')}}</td>
                        <td>{{number_format($currency->buying_price, 2, '.', ',')}}</td>
                        <td>{{number_format($currency->selling_price, 2, '.', ',')}}</td>
                        <td>{{ $currency->initiators->name ?? '--' }}</td>
                    <td>
					 @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                        <form action="{{ url('ib/requests/actions') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $currency->id }}">
                            <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="request_type" value="ib_users">
                            <a class="btn btn-primary btn-sm" title="Edit Exchange Rate" href="{{ route('ib.exchange_rate.edit', $currency->id ) }}">
                                <i class="fas fa-edit text-dark-pastel-green"></i>
                            </a>
                       

                        </form>
						@endif


                    </td>

                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $currency_rates->links() }}
            </div>
        </div>
    </div>


    @include('ib.dialogs.user_block_dialog')



@section('scripts')
    @parent
    <script>
      
        //figure out how to validate the full name input
        $("#name").change(function(){
            //get the value of the field
            var v = $(this).val();
            if((v.split(" ").length-1) < 1){
                //there is a single field
                $("#name").removeClass("is-invalid");
                $("#name").removeClass("is-valid");
                $("#name").addClass("is-invalid");
            }else{
                //the field has multiple names
                $("#name").removeClass("is-invalid");
                $("#name").removeClass("is-valid");
                $("#name").addClass("is-valid");
            }
        });

    </script>

    <script>
        function handleActivateBlock(url,username,mobile,email,status_value,id)
        {
            var form = document.getElementById('blockUser');
            var action_plan="";
            if(status_value=="Blocked")
            {
                action_plan="BLOCKING";
            }
            else if(status_value=="Active")
            {
                action_plan="ACTIVATION";
            }
            else{
                action_plan="";
            }
            var htmlBody = "<h5>CONFIRM "+action_plan+"</h5>\n" +
                "    <hr/>\n" +
                "\n" +
                "    <div class=\"card\">\n" +
                "        <div class=\"card-body\">\n" +
                "            <h6 class=\"mb-3\">USER DETAILS</h6>\n" +
                "            <div class=\"row\">\n" +
                "                <div class=\"col-md-6\">\n" +
                "                    <table class=\"table table-sm\">\n" +
                "                        <tbody>\n" +
                "                            <tr><td>NAME:</td><td>"+ username +"</td></tr>\n" +
                "                            <tr><td>EMAIL:</td><td>"+ email +"</td></tr>\n" +
                "                            <tr><td>PHONE:</td><td>"+ mobile +"</td></tr>\n" +
                "                        </tbody>\n" +
                "                    </table>\n" +
                "                    <hr/>\n" +
                "                </div>\n" +
                "            </div>\n" +
                "        </div>\n" +
                "    </div>"
            var text = document.getElementById('blockingText').innerHTML = htmlBody;
            var status = document.getElementById('status').value = status_value;
            var id = document.getElementById('user_id').value=id;

            form.action = url;
        }

    </script>

@endsection




@endsection
