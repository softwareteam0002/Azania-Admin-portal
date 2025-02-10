<table class="table table-striped table-sm table-hover datatable">
    <thead>
    <tr>
        <th width="10"></th>
        <th>Full Name</th>
        <th>Mobile Number</th>
        <th>Service Level</th>
        <th>Institution</th>
        <th>Added By</th>
        <th>Control Number</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($requests as $request)
        <tr>
            <td widtd="10"></td>
            <td>{{ $request->full_name }}</td>
            <td>{{ $request->phone }}</td>
            <td>{{ $request->services->name }}</td>
            <td>{{ $request->institutions->institute_name }}</td>
            <td>{{ $request->users->name }}</td>
            <td>{{ $request->control_no }}</td>
            <td>@if($request->status==0) <span class="badge badge-danger">INACTIVE</span> @else <span class="badge badge-success">ACTIVE</span> @endif</td>
        </tr>
    @endforeach
    </tbody>
</table>
