<x-app>
    <main class="container">
        <x-nav></x-nav>

        <h1>All Transactions</h1>
        <form>
            <fieldset class="grid">
                <input
                    name="date"
                    type="date"
                    aria-label="Date"
                    value="<?= $date ?? '' ?>" />
                <select name="account" aria-label="Select account to filter by...">
                    <option <?= $selected_acct ? '' : 'selected'; ?> value="">
                        Select account...
                    </option>
                    <?php foreach ($accounts as $account) : ?>
                        <option
                            value="<?= $account ?>"
                            <?= $selected_acct === $account ? 'selected' : '' ?>>
                            <?= $account ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input
                    type="submit"
                    name="submit"
                    value="Filter" />
            </fieldset>
        </form>
        <table class="striped">
            <thead>
                <tr>
                    <th scope="col">Transaction Id</th>
                    <th scope="col">Date</th>
                    <th scope="col">Description</th>
                    <th scope="col">Account</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Override</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $txn) : ?>
                    <tr>
                        <th scope="row"><?= $txn['txnidx'] ?></td>
                        <td><?= $txn['date'] ?></td>
                        <td><?= $txn['description'] ?></td>
                        <td colspan="3"></td>

                    </tr>
                    <?php foreach ($txn['postings'] as $posting) : ?>
                        <tr>
                            <td colspan="3"></td>
                            <td><?= $posting['account'] ?></td>
                            <td><?= $posting['amount'] ?></td>
                            <td>
                                <a href="/override?txnidx=<?= $txn['txnidx'] ?>&acct=<?= urlencode($posting['account']) ?>">
                                    Override
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</x-app>
