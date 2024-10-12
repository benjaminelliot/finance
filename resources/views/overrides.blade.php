<x-app>
    <main class="container">
        <x-nav></x-nav>
        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif
        <h1>Overridden Transactions</h1>
        <table>
            <thead>
                <tr>
                    <th scope="col">Transaction Date</th>
                    <th scope="col">Description</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Override</th>
                    <th scope="col">Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($overrides as $row) : ?>
                    <tr>
                        <th scope="row"><?= $row['transaction_date'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['amount'] ?></td>
                        <td><?= $row['override'] ?></td>
                        <td><?= $row['notes'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</x-app>