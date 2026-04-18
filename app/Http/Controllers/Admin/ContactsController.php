<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 28.02.2019
 * Time: 12:51
 */

namespace App\Http\Controllers\Admin;


use App\Domain\Contacts\Jobs\UpdateContactJob;
use App\Domain\Contacts\Models\Contact;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function index()
    {
        return view('admin.contacts.index', [
            'contacts' => Contact::all()
        ]);
    }

    public function update(Request $request)
    {
        try {
            $this->dispatchSync(new UpdateContactJob($request));
            return redirect()->route('admin.contacts.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.contacts.index')->with('danger', trans('admin.update_failed'));
        }
    }
}
