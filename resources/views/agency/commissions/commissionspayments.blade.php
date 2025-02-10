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
    <hr />
</div>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
        <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
        <li class="breadcrumb-item active">Agent Commission Payments</li>
    </ol>
</nav>

<h5>Agent Commission Batches</h5>
<hr />


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h6>Generated Batches</h6>
                <p class="small">Select a commission batch:</p>
                <div class="form-group">
                    <select name="batch_id" class="form-control form-control-sm" id="commissionBatchID">
                        @foreach($batches as $batch)
                        <option value="{{ $batch->batch_id ?? ''}}">
                            Batch # {{ $batch->batch_id }}, From : {{$batch->from_date}} To : {{$batch->to_date}}
                            @if($batch->status == 1)
                            [ Initiated ]
                            @elseif($batch->status == 2)
                            [ Approved - Paid ]
                            @elseif($batch->status == 3)
                            [ Disapproved ]
                            @else
                            [ nil ]
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <hr />
                    <button onclick="attrClick()" class="btn btn-success btn-sm">Get Commissions</button>
                </div>
            </div>

            <script>
                function attrClick() {
                    var v = $("#commissionBatchID").val();
                    window.location.replace("{{ url('agency/agentcommissions/batch/') }}/" + v);;
                }
            </script>


            <div class="col-md-4">
                <h6>Generate Commission Payments</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form action="{{ url('agency/agentcommissions/generate') }}" method="POST">
                    @csrf
                    @method('POST')


                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">From:</span>
                                            </div>
                                            <input type="date" name="from_date" id='from_date' class="form-control form-control-sm" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">To &nbsp;&nbsp;&nbsp;&nbsp;:</span>
                                        </div>
                                        <input type="date" name="to_date" id='to_date' class="form-control form-control-sm" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr />
                            <button type="submit" class="btn btn-success btn-sm">Generate</button>
                            <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                        </div>

                    </div>


                </form>
            </div>
        </div>
    </div>
</div>




@section('scripts')
@parent
<script>
    $(function() {
        $('.datatable').DataTable()
    });
</script>
@endsection
@endsection