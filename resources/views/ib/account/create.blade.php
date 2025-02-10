@extends('layouts.admin')
@section('content')
    <div style="margin-bottom: 10px;" class="row">
    </div>
    <div class="card">

        <div class="card-header">
            <div style="margin-bottom: 10px;" class="row">
                <div class="col-lg-12">
                    <a class="btn btn-success btn-sm" href="{{ url('ib/accounts/index') }}">
                        Back to Accounts
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <li class="list-group-item">
                            {{ $error }}
                        </li>
                    @endforeach
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

            <form action="{{ url('ib/account/store') }}" method="POST">
                @csrf
                @method('POST')
                <div class="form-group">
                    <label for="name">Account Name*</label>
                    <input type="text" id="account_name" name="institute_name" class="form-control" >
                </div>

                <div class="form-group">
                    <label for="name">Account Number*</label>
                    <input type="text" id="account_number" name="account_number" class="form-control" >
                </div>



                <div class="form-group">
                    <label for="name">Balance</label>
                    <input type="text" id="balance" name="balance" class="form-control" >
                </div>

                <div class="form-group">
                    <label for="types">Account Type*</label>
                    <select name="account_type" class="form-control">
                        <option value="0" >Select Account Type</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" >{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="types">Branch Name</label>
                    <select name="branch_id" class="form-control">
                        <option value="0" >Select Branch</option>
                        @foreach($branchs as $branch)
                            <option value="{{ $branch->id }}" >{{ $branch->branchName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="types">Institution Name</label>
                    <select name="institution_id" class="form-control">
                        <option value="0" >Select Institution Name</option>
                        @foreach($institutions as $institution)
                            <option value="{{ $institution->id }}" >{{ $institution->institute_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="types">Customer Name*</label>
                    <select name="user_id" class="form-control">
                        <option value="0" >Select Customer Name</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" >{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Min Amount:<span class="text-danger">*</span></label>
                            <input type="number" id="min_amount" name="min_amount" class="form-control form-control-sm" placeholder="Minimum Transaction Amount" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Max Amount:<span class="text-danger">*</span></label>
                            <input type="number" id="max_amount" name="max_amount" class="form-control form-control-sm" min="1" placeholder="Maxmum Transaction Amount" required>
                        </div>
                    </div>


                <div>
                    <input class="btn btn-success btn-sm form-control" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var next = 1;
            $(".add-more").click(function(e){
                e.preventDefault();
                var addto = "#field" + next;
                var addRemove = "#field" + (next);
                next = next + 1;
                var newIn = '<input autocomplete="off" class="input form-control" id="field' + next + '" name="field' + next + '" type="text">';
                var newInput = $(newIn);
                var removeBtn = '<button id="remove' + (next - 1) + '" class="btn btn-danger btn-sm remove-me" >-</button></div><div id="field">';
                var removeButton = $(removeBtn);
                $(addto).after(newInput);
                $(addRemove).after(removeButton);
                $("#field" + next).attr('data-source',$(addto).attr('data-source'));
                $("#count").val(next);

                $('.remove-me').click(function(e){
                    e.preventDefault();
                    var fieldNum = this.id.charAt(this.id.length-1);
                    var fieldID = "#field" + fieldNum;
                    $(this).remove();
                    $(fieldID).remove();
                });
            });



        });
    </script>

@endsection






