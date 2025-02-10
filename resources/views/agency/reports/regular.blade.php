<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
    <tr>
        <?php
        // Assume $transactions is the result set from the query
        if (!empty($transactions)) {
            // Dynamically generate table headings from the first row
            foreach (array_keys((array)$transactions[0]) as $key) {
                echo "<th><b>" . htmlspecialchars(str_replace('_', ' ', $key)) . "</b></th>";
            }
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($transactions as $transaction): ?>
    <tr>
            <?php
            // Dynamically populate each row with data
            foreach ((array)$transaction as $field => $value) {
                if ($field === 'TRANSACTION AMOUNT' || $field === 'TRANSACTION CHARGE') {
                    // Format amount with 2 decimal places
                    echo "<td>" . htmlspecialchars(number_format((float)$value, 2)) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
                }
            }
            ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
