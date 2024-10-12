<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Models\OverrideModel;
use App\Services\HledgerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MainController extends Controller
{
    public function __construct(private HledgerService $hledgerService)
    {
    }

    public function index(Request $request): View
    {
        $date = $request->get('date');
        $account = $request->get('account');

        $transactions = $this->hledgerService->getAllTransactions($date, $account);
        $accounts = $this->hledgerService->getAllAccounts();

        return view('index', [
            'transactions' => $transactions,
            'selected_acct' => $account,
            'accounts' => $accounts,
            'date' => $date
        ]);
    }

    public function overrides()
    {
        /** @var OverrideModel[] */
        $overrides = OverrideModel::all();

        return view('overrides', [
            'overrides' => $overrides
        ]);
    }

    public function override(Request $request)
    {
        $txnidx = $request->get('txnidx');
        $selected_acct = $request->get('acct');
        $transaction = $this->hledgerService->getTransactionByTxnidx($txnidx);
        $overrides = [
            'eating_out',
            'hotel',
            'gifts',
            'film_income',
            'film_cost',
            'phone',
            'entertainment',
            'bagpipes',
        ];

        foreach ($transaction['postings'] as $posting) {
            if (!empty($posting['credit'])) {
                $transaction['amount'] = $posting['amount'];
                break;
            }
        }

        // dd($transaction);

        return view('override', [
            'transaction' => $transaction,
            'overrides' => $overrides,
            'selected_acct' => $selected_acct
        ]);
    }

    public function persistOverride(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'override' => 'required|string',
            'notes' => 'required|string'
        ]);

        $override = OverrideModel::create(
            $validated
        );

        $override->save();


        return redirect('/overrides')->with('success', 'Override saved successfully');
    }
}