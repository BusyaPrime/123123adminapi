<?php


namespace App\Domain\Tcompanies\Models;


use App\Services\FilterService\Filterable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tcompanies\Models\Tcompany
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany simplePaginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereCompanyAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereCompanyCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereContractNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereInn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereMfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereOked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereOkonh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany wherePhones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany wherePostIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcompanies\Models\Tcompany whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tcompany extends Model
{
    use Filterable;
    protected $guarded = ['id'];

    public function getParsedPhones() {
        return json_decode($this->phones, true) ?? [];
    }

    public function getParsedEmails() {
        return json_decode($this->emails, true) ?? [];
    }
}
