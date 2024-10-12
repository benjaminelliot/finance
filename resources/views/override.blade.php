<x-app>
<main class="container">
    <x-nav></x-nav>

    <h1>Override Transaction</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form method="POST">
        @csrf
        <fieldset class="grid">
            <label>
                From Account
                <input
                    name="from_acct"
                    value="{{ $selected_acct }}"
                    readonly />
            </label>
            <label>
                Amount
                <input
                    name="amount"
                    value="{{ $transaction['amount'] }}"
                    readonly />
            </label>
            <label>
                Date
                <input
                    name="transaction_date"
                    value="{{ date('m/d/Y', strtotime($transaction['date'])) }}"
                    readonly />
            </label>
        </fieldset>
        <fieldset>
            <label>
                Description RegEx
                <input
                    name="description"
                    value="{{ $transaction['description'] }}"
                    readonly
                />
            </label>

            <label for="override">Override</label>
            <select id="override" name="override" aria-label="Select the override.">
                <option selected value="">
                    Select override...
                </option>
                <?php foreach ($overrides as $override) : ?>
                    <option
                        value="<?= $override ?>"
                    >
                        <?= $override ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label>
                Note
                <textarea name="notes" id="">{{ old('notes') }}</textarea>
            </label>
        </fieldset>

        <input
            type="submit"
            value="Save" />
    </form>
</main>
</x-app>
