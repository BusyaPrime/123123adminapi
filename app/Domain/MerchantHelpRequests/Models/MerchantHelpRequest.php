<?php


namespace App\Domain\MerchantHelpRequests\Models;


use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserProfile;
use App\Services\FilterService\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * App\Domain\Companies\Models\Company
 *
 * @property int $id
 * @property string $title
 * @property string|null $contract_number
 * @property string|null $address
 * @property string|null $phones
 * @property string|null $emails
 * @property string|null $company_name
 * @property string|null $company_city
 * @property string|null $company_address
 * @property string|null $post_index
 * @property string|null $bank
 * @property string|null $bank_account
 * @property string|null $oked
 * @property string|null $mfo
 * @property string|null $inn
 * @property string|null $okonh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company simplePaginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereCompanyAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereCompanyCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereContractNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereInn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereMfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereOked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereOkonh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company wherePhones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company wherePostIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Companies\Models\Company whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantHelpRequest extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    protected $table = 'merchant_help_requests';
}
