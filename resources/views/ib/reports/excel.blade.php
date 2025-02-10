<table>
    <tr>
        <th><b>DATE</b></th>
        <th><b>TRANSACTION ID</b></th>
        <th><b>CHANNEL</b></th>
        <th><b>TRANSACTION TYPE</b></th>
        <th><b>SERVICE</b></th>
        <th><b>SENDER NUMBER</b></th>
        <th><b>DESTINATION ACCOUNT</b></th>
        <th><b>REFERENCE NUMBER</b></th>
        <th><b>STATUS</b></th>
        <th><b>AMOUNT</b></th>
        <th><b>SENDER NAME</b></th>
        <th><b>RECEIVER NAME</b></th>
        <th><b>RECEIVER BANK</b></th>
        <th><b>FAILURE REASON</b></th>
        <th><b>RECEIVER MOBILE</b></th>
        <th><b>AUTH CODE</b></th>
    </tr>
    <?php
    // Function to sanitize output
    function sanitize($data): string
    {
        return htmlspecialchars(preg_replace('/[^A-Za-z0-9\s\-]/', '', $data));
    }

    foreach ($transactions as $transcation) {
        // Sanitize transaction data
        $date = date("Y-m-d H:i:s", strtotime($transcation->{'CREATED AT'}));
        $amount = number_format(intval($transcation->{'AMOUNT'}), 2);

        echo "
        <tr>
            <td>{$date}</td>
            <td>" . sanitize($transcation->{'TRANSACTION ID'}) . "</td>
            <td>IB</td>
            <td>" . sanitize($transcation->{'TRANSACTION NAME'}) . "</td>
            <td>" . sanitize($transcation->{'SERVICE'}) . "</td>
            <td>" . sanitize($transcation->{'SENDER NUMBER'}) . "</td>
            <td>" . sanitize($transcation->{'DEST ACCOUNT'} ?? "") . "</td>
            <td>" . sanitize($transcation->{'REFERENCE NUMBER'} ?? "") . "</td>
            <td>" . sanitize($transcation->{'STATUS'}) . "</td>
            <td>{$amount}</td>
            <td>" . sanitize($transcation->{'SENDER NAME'}) . "</td>
            <td>" . sanitize($transcation->{'RECEIVER NAME'}) . "</td>
            <td>" . sanitize($transcation->{'BANK NAME'}) . "</td>
            <td>" . sanitize($transcation->{'TRANSACTION RESPONSE MESSAGE'} ?? "") . "</td>
            <td>" . sanitize($transcation->{'RECEIVER NUMBER'}) . "</td>
            <td>" . sanitize($transcation->{'AUTH CODE'} ?? "") . "</td>
        </tr>
        ";
    }
    ?>
</table>
