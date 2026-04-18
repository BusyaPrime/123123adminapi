<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Domain\Companies\Models\Company;
use App\Domain\Users\Models\User;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Users\Resources\CompanyUserResource;
use App\Domain\Transactions\Resources\TransactionResource;
use App\Domain\Companies\Resources\CompanyBookingResource;
use App\Domain\Companies\Resources\CompanyResource;
use App\Domain\TruckBookings\Models\TruckBooking;

class CompanyController extends Controller
{
    protected function resolveClientCompany(Request $request): ?Company
    {
        $company = $request->input('clientCompany');

        if ($company instanceof Company) {
            return $company;
        }

        $authUser = $request->input('authUser');

        if ($authUser instanceof User) {
            return $authUser->resolveClientCompany(['user', 'priority']);
        }

        return null;
    }

    protected function companyContextNotFoundResponse()
    {
        return response()->json(['message' => 'company_not_found'], 404);
    }

    protected function userBelongsToCompany(User $user, Company $company): bool
    {
        return optional($user->profile)->company_id === $company->id;
    }

    public function show(Request $r){
        try {
            $companyContext = $this->resolveClientCompany($r);
            if (!$companyContext) {
                return $this->companyContextNotFoundResponse();
            }

            $company_id = $companyContext->id;
            $company = Company::where('id', $company_id)->with('user')->with('users')->with('transactions')->with('priority')->first();
            $users = $company->users->pluck('user_id');
            $users->push($company->user_id);

            $newBookings = TruckBooking::where(function($q) use ($users){
                return $q->where('status', TruckBooking::STATUS_ORDER)->whereIn('user_id', $users);
            })->count();
            $processingBookings = TruckBooking::where(function($q) use ($users){
                return $q->whereNotIn('status', [ TruckBooking::STATUS_ORDER, TruckBooking::STATUS_CANCELED, TruckBooking::STATUS_DONE])->whereIn('user_id', $users);
            })->count();
            $doneBookings = TruckBooking::where(function($q) use ($users){
                return $q->where('status', TruckBooking::STATUS_DONE)->whereIn('user_id', $users);
            })->count();

            $company->order_counts = [
                'new_orders' => $newBookings,
                'processing_orders' => $processingBookings,
                'done_orders' => $doneBookings
            ];

            return CompanyResource::make($company);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function bookings(Request $req){
        try {
            $company = $this->resolveClientCompany($req);
            if (!$company) {
                return $this->companyContextNotFoundResponse();
            }

            $status = $req->status;
            // $users = $company->users->pluck('user_id');
            // $users->push($company->user->id);

            $bookings = TruckBooking::where(function($q) use ($status, $company){
                if($status === 'processing') $q->whereNotIn('status', [ TruckBooking::STATUS_ORDER, TruckBooking::STATUS_CANCELED, TruckBooking::STATUS_DONE]);
                else if($status === 'order') $q->where('status', TruckBooking::STATUS_ORDER);
                else if($status === 'done') $q->where('status', TruckBooking::STATUS_DONE);
                $q->where('client_company_id', $company->id)->get();
            })->with('user')->with('driver')->withCarType()->orderByDesc('id')->paginate(15);

            return CompanyBookingResource::collection($bookings);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error'], 500);
        }
    }
    
    public function users(Request $req){
        try {
            $company = $this->resolveClientCompany($req);
            if (!$company) {
                return $this->companyContextNotFoundResponse();
            }

            $companyID = $company->id;
            $company = Company::with('users')->findOrFail($companyID);
            $companyUsers = $company->users()->orderByDesc('created_at')->paginate(15);
            return CompanyUserResource::collection($companyUsers);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error'], 500);
        }
    }

    
    public function transactions(Request $req){
        try {
            $company = $this->resolveClientCompany($req);
            if (!$company) {
                return $this->companyContextNotFoundResponse();
            }

            $companyID = $company->id;
            $company = Company::with('transactions')->findOrFail($companyID);
            $companyTransactions = $company->transactions()->orderByDesc('created_at')->paginate(15);
            return TransactionResource::collection($companyTransactions);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    
    public function userInfo(Request $req, User $user){
        try {
            $company = $this->resolveClientCompany($req);
            if (!$company) {
                return $this->companyContextNotFoundResponse();
            }
            if (!$this->userBelongsToCompany($user, $company)) {
                return response()->json(['message' => 'user_not_found'], 404);
            }

            return CompanyUserResource::make($user->profile);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'user_not_found'], 500);
        }
    }
    
    public function editUser(Request $request, User $user)
    {
        try {
            $company = $this->resolveClientCompany($request);
            if (!$company) {
                return $this->companyContextNotFoundResponse();
            }
            if (!$this->userBelongsToCompany($user, $company)) {
                return response()->json(['message' => 'user_not_found'], 404);
            }

            $company_id = $company->id;
            $request->merge(['company_id' => $company_id]);
            $updated = $this->dispatchSync(new AdminStoreUserJob($request));
            return CompanyUserResource::make($updated->profile);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th], 500);
        }
    }
    
    public function addUser(Request $request)
    {
        try {
            $company = $this->resolveClientCompany($request);
            if (!$company) {
                return $this->companyContextNotFoundResponse();
            }

            $company_id = $company->id;
            $request->merge(['role' => null]);
            $request->merge(['company_id' => $company_id]);
            $user = $this->dispatchSync(new AdminStoreUserJob($request));
            return CompanyUserResource::make($user->profile);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error_adding_user'], 500);
        }
    }
    
    public function deleteUserFromCompany(Request $request, User $user){
        try {
            $company = $this->resolveClientCompany($request);
            if (!$company) {
                return $this->companyContextNotFoundResponse();
            }
            if (!$this->userBelongsToCompany($user, $company)) {
                return response()->json(['message' => 'user_not_found'], 404);
            }

            $user->profile->company_id = null;
            $user->profile->save();
            return response()->json(['message' => 'deleted'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error_deleting_user'], 500);
        }
    }

    public function agreeWithTerms(Request $request)
    {
        $company = $this->resolveClientCompany($request);
        if (!$company) {
            return $this->companyContextNotFoundResponse();
        }

        if($company->is_agreed_terms == 0) {
            $company->is_agreed_terms = 1;
            $company->save();
        }

        return CompanyResource::make($company);
    }
}
