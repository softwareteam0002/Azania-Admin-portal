<table class="table table-striped table-sm table-hover datatable">
    <thead>
    <tr>
        <th width="10"></th>
        <th>Name</th>
        <th>Description</th>
        <th>Institution</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($requests as $request)
        <tr>
            <td widtd="10"></td>
            <td>{{ $request->name }}</td>
            <td>{{ $request->description }}</td>
            <td>{{ $request->institutions->institute_name }}</td>
            <td>@if($request->status==0) <span class="badge badge-danger">INACTIVE</span> @else <span class="badge badge-success">ACTIVE</span> @endif</td>
        </tr>
    @endforeach
    </tbody>
</table>
