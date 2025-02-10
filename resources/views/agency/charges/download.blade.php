<table class="table table-striped table-sm table-hover" width="100%">
    <thead>
        <tr>
            <th><b>Service Name</b></th>
            <th><b>Charge Type</b></th>
            <th><b>Payer</b></th>
            <th class="text-right"><b>From Amount</b></th>
            <th class="text-right"><b>To Amount</b></th>
            <th class="text-right"><b>Charge Amount</b></th>
            <th class="text-right"><b>Chrage Percent</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($charges as $charge)
        <tr>
            <td>{{ $charge->services->agent_serviceName}}</td>
            <td>{{ $charge->chargetypes->charge_type}}</td>
            <td>{{ ucfirst($charge->payee) }}</td>
            <td class="text-right">{{ number_format($charge->from_amount, 2) ?? "--"}}</td>
            <td class="text-right">{{ number_format($charge->to_amount, 2) ?? "--"}}</td>
            <td class="text-right">{{ number_format($charge->amount , 2) ?? "--"}}</td>
            <td class="text-right">{{ $charge->amount_percentage ?? "--"}}</td>
        </tr>
        @endforeach
    </tbody>
</table>