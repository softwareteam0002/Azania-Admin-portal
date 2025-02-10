<table class="table table-striped table-sm table-hover datatable">
    <thead>
    <tr>
        <th width="10"></th>
        <th>Payer's Name</th>
        <th>Payer's Mobile</th>
        <th>Institution</th>
        <th>Amount</th>
        <th>Service</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($requests as $request)
        <tr>
            <td widtd="10"></td>
            <td>{{ $request->payers->full_name }}</td>
            <td>{{ $request->payers->phone }}</td>
            <td>{{ $request->institutions->institute_name }}</td>
            <td>{{ $request->amount }}</td>
            <td>{{ $request->services->name }}</td>
            <td>@if($request->status==0) <span class="badge badge-danger">INACTIVE</span> @else <span class="badge badge-success">ACTIVE</span> @endif</td>
        </tr>
    @endforeach
    </tbody>
</table>
