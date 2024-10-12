<?php

declare(strict_types=1);

namespace App\Services;

class HledgerService
{
    private string $journal_path;

    public function __construct()
    {
        $this->journal_path = env('HL_JOURNAL_PATH'); 
    }

    public function getAllAccounts(): array
    {
        $cmd = "hledger -f $this->journal_path accounts";
        exec($cmd, $output, $status);
        return $output;
    }

    /**
     * Write a function that takes in an array of associative arrays of transactions. Each transaction has a key "txnidx" which is
     * a unique identifier for the transaction. The function should then return and array of arrays that groups the transactions by the 
     * "txnidx" key. The array should be sorted by the "txnidx" key in ascending order.
     */
    public function getAllTransactions(?string $date = null, ?string $account = null): array
    {
        $acct = $account ? $account : "expenses";
        $date = $date ? "date:$date" : "";
        $cmd = "hledger -f $this->journal_path print $acct -O csv $date";
        exec($cmd, $output, $status);
        $transactions = array_map('str_getcsv', $output);
        $headers = array_shift($transactions);
        return $this->groupedTransactionsByTxnidx($this->transactionsWithHeaders($headers, $transactions));
    }

    public function transactionsWithHeaders(array $headers, array $transactions): array
    {
        $transactionsWithHeaders = [];
        foreach ($transactions as $transaction) {
            $transactionsWithHeaders[] = array_combine($headers, $transaction);
        }
        return $transactionsWithHeaders;
    }

    public function groupedTransactionsByTxnidx(array $transactions): array
    {
        $grouped = [];
        foreach ($transactions as $transaction) {
            $txnidx = $transaction['txnidx'];
            if (!array_key_exists($txnidx, $grouped)) {
                $grouped[$txnidx] = [
                    'txnidx' => $txnidx,
                    'date' => $transaction['date'],
                    'date2' => $transaction['date'],
                    'status' => $transaction['status'],
                    'code' => $transaction['code'],
                    'description' => $transaction['description'],
                    'postings' => []
                ];
            }
            $grouped[$txnidx]['postings'][] = [
                'account' => $transaction['account'],
                'amount' => $transaction['amount'],
                'commodity' => $transaction['commodity'],
                'credit' => $transaction['credit'],
                'debit' => $transaction['debit'],
                'posting-status' => $transaction['posting-status'],
                'posting-comment' => $transaction['posting-comment']
            ];
        }
        ksort($grouped);
        return $grouped;
    }

    public function getTransactionByTxnidx(string $txnidx): array
    {
        $transactions = $this->getAllTransactions();
        return $transactions[$txnidx];
    }
}
