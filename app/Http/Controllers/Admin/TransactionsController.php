<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Companies\Models\Company;
use App\Domain\Transactions\Exports\TransactionsExport;
use App\Domain\Transactions\Filters\TransactionFilter;
use App\Domain\Transactions\Imports\TransactionsImport;
use App\Domain\Transactions\Models\Transaction;
use App\Domain\Transactions\Requests\TransactionImportRequest;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $filter = new TransactionFilter($request);
        $filters = $filter->filters();

        if ($filters['date_start'] != '' && $filters['date_end'] != '') {

            $transactions = Transaction::filter($filter)
                ->whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)), date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0))])
                ->with('user')->with('company')
                ->orderBy('id', 'desc')->paginateFilter();

        } elseif ($filters['date_start'] != '') {
            $transactions = Transaction::filter($filter)
                ->where('created_at', '>=',date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)))
                ->with('user')->with('company')
                ->orderBy('id', 'desc')->paginateFilter();
        } elseif ($filters['date_end'] != '') {
            $transactions = Transaction::filter($filter)
                ->where('created_at', '<=',date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0)))
                ->with('user')->with('company')
                ->orderBy('id', 'desc')->paginateFilter();
        } else {
            $transactions = Transaction::filter($filter)->with('user')->with('company')
                ->orderBy('id', 'desc')->paginateFilter();
        }

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'filters' => $filters,
        ]);
    }

    public function export(Request $request)
    {
        $filter = new TransactionFilter($request);
        $filters = $filter->filters();

        if ($filters['date_start'] != '' && $filters['date_end'] != '') {

            $transactions = Transaction::filter($filter)
                ->whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)), date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0))])
                ->with('user')->with('company')
                ->orderBy('id', 'desc')->get();

        } elseif ($filters['date_start'] != '') {
            $transactions = Transaction::filter($filter)
                ->where('created_at', '>=',date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)))
                ->with('user')->with('company')
                ->orderBy('id', 'desc')->get();
        } elseif ($filters['date_end'] != '') {
            $transactions = Transaction::filter($filter)
                ->where('created_at', '<=',date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0)))
                ->with('user')->with('company')
                ->orderBy('id', 'desc')->get();
        } else {
            $transactions = Transaction::filter($filter)->with('user')->with('company')
                ->orderBy('id', 'desc')->get();
        }

        $exportData = collect();
        $exportData->push([
            'ID',
            'Водитель',
            'Компания',
            'Тип транзакции',
            'Основание',
            'Дата',
            'Сумма',
        ]);

        foreach ($transactions as $transaction) {
            $transactionData = [
                $transaction->id,
                ($transaction->user->profile->surname ?? '').' '.($transaction->user->profile->name ?? '').' '.($transaction->user->profile->middle_name ?? ''),
                $transaction->company_id ? ($transaction->company->title ?? ''): 'Самозанятый',
                $transaction->type == 'refill' ? 'Пополнение': 'Списание',
                $transaction->description ?? '',
                $transaction->created_at->format('d.m.Y H:i'),
                $transaction->type == 'refill'? '+'.$transaction->amount : '-'.$transaction->amount
            ];
            $exportData->push($transactionData);
        }

        $export = new TransactionsExport($exportData);

        return Excel::download($export, config('app.name').' - transactions '.(date('Y-m-d')).'.xlsx');
    }

    public function storeCompany(Request $request)
    {
        $companyInn = $request->input('inn');
        $company = Company::where('inn', $companyInn)->first();
        if (!$company) {
            return redirect()->route('admin.transactions.index')->with('danger', 'Компания с указанным ИНН не найдена');
        }
        $transaction = new Transaction();
        $transaction->user_id = null;
        $transaction->company_id = $company->id;
        $transaction->type = $request->input('type', 'refill');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount', 0);
        if ($transaction->save()) {
            if ($transaction->type == 'refill') {
                $company->balance = (int)$company->balance + $transaction->amount;
            } else {
                $company->balance = (int)$company->balance - $transaction->amount;
            }
            $company->save();
            return redirect()->route('admin.transactions.index')->with('success', 'Транзакция проведена успешно');
        } else {
            return redirect()->route('admin.transactions.index')->with('danger', 'Не удалось провести транзакцию');
        }
    }

    public function storeUser(Request $request)
    {
        $userId = $request->input('transaction_user_id');
        $user = User::whereHas('car')->find($userId);
        if (!$user) {
            return redirect()->route('admin.transactions.index')->with('danger', 'Водитель с указанным ID не найден');
        }
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->company_id = null;
        $transaction->type = $request->input('type', 'refill');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount', 0);
        if ($transaction->save()) {
            if ($transaction->type == 'refill') {
                $user->profile->balance = (int)$user->profile->balance + $transaction->amount;
            } else {
                $user->profile->balance = (int)$user->profile->balance - $transaction->amount;
            }
            $user->profile->save();
            return redirect()->route('admin.transactions.index')->with('success', 'Транзакция проведена успешно');
        } else {
            return redirect()->route('admin.transactions.index')->with('danger', 'Не удалось провести транзакцию');
        }
    }

    public function show(Transaction $transaction)
    {
        return view('admin.transactions.show', [
            'transaction' => $transaction
        ]);
    }

    public function destroy(Transaction $transaction)
    {
        try {
            if ($transaction->company_id) {
                $company = Company::find($transaction->company_id);
                if ($company) {
                    $company->balance = (int)$company->balance - $transaction->amount;
                    $company->save();
                }
            } elseif ($transaction->user_id) {
                $user = User::find($transaction->user_id);
                if($user && $user->profile) {
                    $user->profile->balance = (int)$user->profile->balance - $transaction->amount;
                    $user->profile->save();
                }
            }
            $transaction->delete();
            return redirect()->route('admin.transactions.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.transactions.index')->with('danger', trans('admin.destroy_failed'));
        }
    }

    public function importForm()
    {
        return view('admin.transactions.import.form');
    }

    public function importShow(TransactionImportRequest $request)
    {
        $import = new TransactionsImport();
        $rows = $import->toCollection($request->file('file'));
        $transactions = collect([]);
        foreach ($rows as $sheets) {
            foreach ($sheets as $row) {
                if (isset($row[2]) && $row[2] != '' && $row[2] != 'ИНН') {
                    $inn = explode('/', $row[1])[1]??0;
                    $transactions->add(['date' => $row[0] ?? date('d.m.Y'), 'inn' =>  $inn, 'sum' => $row[6] ?? $row[8], 'description' => $row[7]]);
                }
            }
        }

        return view('admin.transactions.import.form', [
            'transactions' => $transactions
        ]);
    }

    public function importExcel(Request $request)
    {
        $requestTransactions = $request->input('transactions', []);
        $errors = [];
        foreach ($requestTransactions as $requestTransaction) {
            if(isset($requestTransaction['include']) && $requestTransaction['include'] == 1) {
                $companyInn = $requestTransaction['inn'] ?? 0;
                $company = Company::where('inn', $companyInn)->first();
                if (!$company) {
                    $errors[] = 'Компания с ИНН '.$companyInn.' не найдена';
                    continue;
                }
                $transaction = new Transaction();
                $transaction->user_id = null;
                $transaction->company_id = $company->id;
                $transaction->type = 'refill';
                $transaction->description = $requestTransaction['description'] ?? 'Импорт с Excel';
                $transaction->amount = $requestTransaction['amount'] ?? 0;
                if ($transaction->save()) {
                    if ($transaction->type == 'refill') {
                        $company->balance = (int)$company->balance + $transaction->amount;
                    } else {
                        $company->balance = (int)$company->balance - $transaction->amount;
                    }
                    $company->save();
                } else {
                    $errors[] = 'Не удалось провести транзакцию с ИНН '.$companyInn;
                    return redirect()->route('admin.transactions.index')->with('danger', 'Не удалось провести транзакцию');
                }

            }
        }

        if (count($errors) > 0) {
            $message = 'Импорт проведен со следующими ошибками: ';
            foreach ($errors as $error) {
                $message .= '<br/>'.$error;
            }
            return redirect()->route('admin.transactions.index')->with('warning', $message);
        } else {
            return redirect()->route('admin.transactions.index')->with('success', 'Импорт проведен успешно');
        }
    }
}
