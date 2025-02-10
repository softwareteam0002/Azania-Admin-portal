<table class="table table-striped table-sm table-hover datatable">
    <thead>
        <tr>
            <th><b>ID</b></th>
            <th><b>Transaction</b></th>
            <th><b>Type</b></th>
            <th><b>Terminal ID</b></th>
            <th class="text-right"><b>Agent Amount</b></th>
            <th class="text-right"><b>Bank Amount</b></th>
            <th class="text-right"><b>Thirdparty Amount</b></th>
            <th class="text-right"><b>Total Amount</b></th>
            <th><b>Agent</b></th>
            <th><b>Agent Commision Account</b></th>
            <th><b>Paid</b></th>

        </tr>
    </thead>
    <tbody>
        @foreach($commissions as $commision)
        <tr>
            <td>{{ $commision->commission_id}}</td>
            <td>{{ $commision->transactionID}}</td>
            <td>{{ $commision->transaction->trxn_type}}</td>
            <td>{{ $commision->transaction->trans_agent_terminal_id}}</td>
            <td class="text-right">{{ number_format($commision->agent_amount,2) }}</td>
            <td class="text-right">{{ number_format($commision->bank_amount,2)}}</td>
            <td class="text-right">{{ number_format($commision->third_party_amount,2)}}</td>
            <td class="text-right">{{ number_format($commision->commission_amount,2)}}</td>
            <td>{{ $commision->agent['agent_full_name']}}</td>
            <td></td>
            <td>
                @if($commision->is_paid == 1)
                Yes
                @elseif($commision->is_paid == 0)
                No
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>