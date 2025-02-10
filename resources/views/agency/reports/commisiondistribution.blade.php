<table class="table table-striped table-sm table-hover datatable">
            <!--

        {
            "commission_id": 1,
            "commission_amount": "200",
            "agent_amount": "60.0",
            "bank_amount": "60.0",
            "third_party_amount": "80.0",
            "agent_id": null,
            "is_reversed": null,
            "is_paid": "0",
            "transactionID": "AB.59296274.15945573",
            "initiator_id": null,
            "approver_id": null
        }


            -->
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
                    <th><b>Transaction ID</b></th>
                    <th><b>Paid</b></th>
                    <th><b>Initiator</b></th>
                    <th><b>Approver</b></th>

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
                    <td>{{ $accounts[0]->bank_account}}</td>
                    <td>{{ $commision->transactionID}}</td>
                    <td>
                        @if($commision->is_paid == 1)
                        Yes
                        @elseif($commision->is_paid == 0)
                        No
                        @endif
                    </td>
                    <td>{{ $commision->initiator->name ?? '--'}}</td>
                    <td>{{ $commision->approver->name ?? '--'}}</td>
                </tr>

                @endforeach
            </tbody>