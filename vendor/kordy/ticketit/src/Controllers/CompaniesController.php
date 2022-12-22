<?php

namespace Kordy\Ticketit\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kordy\Ticketit\Models\Company;
use Kordy\Ticketit\Helpers\LaravelVersion;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // seconds expected for L5.8<=, minutes before that
        $time = LaravelVersion::min('5.8') ? 60*60 : 60;
        $companies = \Cache::remember('ticketit::companies', $time, function () {
            return Company::all();
        });

        return view('ticketit::admin.company.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('ticketit::admin.company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'kode'      => 'required',
            'name'     => 'required',
        ]);

        $company = new Company();
        $company->create(['kode' => $request->kode, 'color' => $request->name]);

        Session::flash('status', trans('ticketit::lang.company-name-has-been-created', ['name' => $request->name]));

        \Cache::forget('ticketit::companies');

        return redirect()->action('\Kordy\Ticketit\Controllers\CompaniesController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        return trans('ticketit::lang.company-all-tickets-here');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $company = Company::findOrFail($id);

        return view('ticketit::admin.company.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'kode'      => 'required',
            'name'     => 'required',
        ]);

        $company = Company::findOrFail($id);
        $company->update(['kode' => $request->kode, 'name' => $request->name]);

        Session::flash('status', trans('ticketit::lang.company-name-has-been-modified', ['name' => $request->name]));

        \Cache::forget('ticketit::companies');

        return redirect()->action('\Kordy\Ticketit\Controllers\CompaniesController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $name = $company->name;
        $priority->delete();

        Session::flash('status', trans('ticketit::lang.company-name-has-been-deleted', ['name' => $name]));

        \Cache::forget('ticketit::companies');

        return redirect()->action('\Kordy\Ticketit\Controllers\CompaniesController@index');
    }
}
