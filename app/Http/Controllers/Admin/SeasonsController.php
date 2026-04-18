<?php


namespace App\Http\Controllers\Admin;


use App\Domain\Seasons\Models\Season;
use App\Domain\Regions\Models\Region;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeasonsController extends Controller
{
    public function index()
    {
        $seasons = Season::paginate();

        return view('admin.seasons.index', [
            'seasons' => $seasons,
        ]);
    }

    public function create()
    {
        return view('admin.seasons.create');
    }


    public function store(Request $request)
    {
        try {
            $season = new Season();
            $season->title = $request->input('title');
            $season->month_start = $request->input('month_start', 1);
            $season->month_end = $request->input('month_end', 1);
            $season->save();
            return redirect()->route('admin.seasons.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.seasons.index')->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(Season $season)
    {
        return view('admin.seasons.edit', [
            'season' => $season
        ]);
    }

    public function update(Season $season, Request $request)
    {
        try {
            $season->title = $request->input('title');
            $season->month_start = $request->input('month_start', 1);
            $season->month_end = $request->input('month_end', 1);
            $season->save();
            return redirect()->route('admin.seasons.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.seasons.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(Season $season)
    {
        try {
            $season->delete();
            return redirect()->route('admin.seasons.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.seasons.index')->with('danger', trans('admin.destroy_failed'));
        }
    }


}
