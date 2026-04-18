<?php

namespace App\Http\Controllers\TAdmin;

use App\Domain\Transactions\Filters\TransactionFilter;
use App\Domain\Transactions\Models\Transaction;
use App\Domain\CompanyPriorities\Models\CompanyPriority;
use Illuminate\Http\Request;

class TransactionsController
{
    public function index(Request $request)
    {
        $company = $request->input('_company');
        $request->merge(['company_id' => $company->id]);
        $filter = new TransactionFilter($request);
        $filters = $filter->filters();

        if ($filters['date_start'] != '' && $filters['date_end'] != '') {

            $transactions = Transaction::filter($filter)
                ->whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)), date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0))])
                ->with('user')->with('company')
                ->where('company_id', $company->id)
                ->orderBy('id', 'desc')->paginateFilter();

        } elseif ($filters['date_start'] != '') {
            $transactions = Transaction::filter($filter)
                ->where('created_at', '>=',date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)))
                ->with('user')->with('company')
                ->where('company_id', $company->id)
                ->orderBy('id', 'desc')->paginateFilter();
        } elseif ($filters['date_end'] != '') {
            $transactions = Transaction::filter($filter)
                ->where('created_at', '<=',date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0)))
                ->with('user')->with('company')
                ->where('company_id', $company->id)
                ->orderBy('id', 'desc')->paginateFilter();
        } else {
            $transactions = Transaction::filter($filter)->with('user')->with('company')
                ->where('company_id', $company->id)
                ->orderBy('id', 'desc')->paginateFilter();
        }

        return view('admin2.transactions.index', [
            'transactions' => $transactions,
            'filters' => $filters,
        ]);
    }

    public function show(Transaction $transaction, Request $request)
    {
        $company = $request->input('_company');
        if ($transaction->company_id != $company->id) {
            abort(404);
        }

        return view('admin2.transactions.show', [
            'transaction' => $transaction
        ]);
    }
    
    
    public function companyBalance(Request $request)
    {
        $company = $request->input('_company');
        $companyTransactions = Transaction::where('company_id', $company->id)->where(function($q){
            $q->whereOr('type', 'company_debt')
              ->whereOr('type', 'refill');
        })
        ->orderByDesc('created_at')
        ->get();
        $companyLimit = CompanyPriority::find($company->priority_id);

        return view('admin2.transactions.companyBalance', [
            'company' => $company,
            'transactions' => $companyTransactions,
            'companyLimit' => $companyLimit,
        ]);
    }
}
