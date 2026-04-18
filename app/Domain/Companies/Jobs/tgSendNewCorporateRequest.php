<?php


namespace App\Domain\Companies\Jobs;


use App\Domain\Companies\Models\Company;
use App\Domain\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class tgSendNewCorporateRequest
{
    use Dispatchable, Queueable;

    protected $user;
    protected $company;

    public function __construct(User $user, Company $company)
    {
        $this->user = $user;
        $this->company = $company;
    }

    /**
     * @return null
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $userFullName = $this->user->profile->surname." ".$this->user->profile->name." ".$this->user->profile->middle_name."\n";
            $tgMessage = "Новая заявка на подключение юридического лица\n";
            $tgMessage .= "--------------------------\n\n";
            $tgMessage .= "Ф.И.О руководителя: ";
            $tgMessage .= "<b>".$userFullName."</b>";
            $tgMessage .= "Наименование юридического лица: ";
            $tgMessage .= "<b>".$this->company->title."</b>\n";
            $tgMessage .= "Адрес: ";
            $tgMessage .= "<b>".$this->company->company_address."</b>\n";
            $tgMessage .= "ИНН: ";
            $tgMessage .= "<b>" . $this->company->inn . "</b>\n";
            $tgMessage .= "Номер телефона заявителя: ";
            $tgMessage .= "<b><a href='tel:".$this->user->profile->phone_number."'>".$this->user->profile->phone_number."</a></b>\n\n";
            $tgMessage .= "Время заявки: ".date("d.m.Y в H:i", time());
            $tgMessage .= "\n--------------------------\n\n";
            $tgMessage .= "<a href='".route("admin.companies.show", ["company" => $this->company->id])."'>Нажмите для просмотра</a>";

            $botId = "bot5335209292:AAGW9F99CKlnjh2yagMliVtBvDGKdoC2OLY";
            $chatId = "-1001530567966";
            // @file_get_contents("https://api.telegram.org/".$botId."/sendMessage?chat_id=".$chatId."&parse_mode=HTML&text=".urlencode($tgMessage));
        } catch (\Exception $exception) {}
}

}
