<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
    <tr>
        <?php
        // Assume $agents is the result set from the query
        if (!empty($agents)) {
            // Dynamically generate table headings from the first row
            foreach (array_keys((array)$agents[0]) as $key) {
                echo "<th><b>" . htmlspecialchars(str_replace('_', ' ', $key)) . "</b></th>";
            }
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($agents as $agent): ?>
    <tr>
            <?php
            // Dynamically populate each row with data
            foreach ((array)$agent as $field => $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
