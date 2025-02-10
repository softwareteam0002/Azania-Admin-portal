<table width="100%">
    <tr>
        <th><b>#</b></th>
        <th ><b>Date</b></th>
        @if($type == 1)<th><b>Company Name</b></th>@endif
        <th><b>Display Name</b></th>
        <th><b>Username</b></th>
        <th><b>Mobile</b></th>
        <th><b>Accounts</b></th>
        <th><b>Email</b></th>
        <th><b>OTP Option</b></th>
        <th><b>Status</b></th>
       <th><b>Verified</b></th>
       <th><b>Created By</b></th>
       <th><b>Supervised By</b></th>
    </tr>
    <?php
    $total = 0;
    $k = 1;
    foreach ($users as $user) {
        $name = $user->display_name;
        $transaction_channel = "IB";
        $user_name = $user->name;
        $date = $user->created_at;
        $date = date("d-m-Y H:i:s", strtotime($date));


        $mobile = $user->mobile_phone;
        $status = $user->status;
        $email = $user->email;
        $institution = $user->institutions->institute_name ?? '';
        $approver = $user->approvers->name ?? '';
        $initiator = $user->initiators->name ?? '';
    if($user->isVerified == 1)
    {
    $verified = "Verified";
    }
    else
    {
    $verified = "Not Verified";
    }
        $otp_option = $user->otp_option;
        $total = $total + 1;
      ?>
        <tr>
            <td><?php echo $k; ?></td>
            <td><?php echo $date; ?></td>
            @if($type == 1)<td><?php echo $institution; ?></td>@endif
            <td><?php echo $name; ?></td>
            <td><?php echo $user_name; ?></td>
            <td><?php echo $mobile; ?></td>
            <td><?php foreach ($user->accounts as $account) {
                echo $account->accountID.' ';
            } ?></td>
            <td><?php echo $email; ?></td>
            
            <td><?php echo $otp_option; ?></td>
            <td><?php echo $status; ?></td>
            <td><?php echo $verified; ?></td>
            <td><?php echo $initiator; ?></td>
            <td><?php echo $approver; ?></td>
        </tr>
      <?php
        $k++;
    }
    ?>
    <?php

    echo "
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        <td></td>
        <td></td>
        <td></td>
            <td><u><b>TOTAL:</b></u></td>
            <td class='tright'><u><b>" . $total . "</b></u></td>
        </tr>
    ";

    ?>
</table>