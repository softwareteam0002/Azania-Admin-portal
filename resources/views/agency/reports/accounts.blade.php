<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <?php if (!empty($accounts)): ?>
            <?php
            // Dynamically generate table headings from the first row
        foreach (array_keys((array)$accounts[0]) as $key): ?>
        <th>
            <b><?= htmlspecialchars(str_replace('_', ' ', $key)) ?></b>
        </th>
        <?php endforeach; ?>
        <?php endif; ?>
    </tr>

    <?php foreach ($accounts as $account): ?>
    <tr>
            <?php
            // Dynamically populate each row with data
        foreach ((array)$account as $field => $value): ?>
        <td><?= htmlspecialchars($value ?? '') ?></td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
</table>
