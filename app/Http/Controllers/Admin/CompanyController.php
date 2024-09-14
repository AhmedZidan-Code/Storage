<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
        public function getCompanies(Request $request){
        if ($request->ajax()) {

            $companies = DB::table('companies')->select('id','title as text')->simplePaginate(3);

            $morePages=true;
            $pagination_obj= json_encode($companies);
            if (empty($companies->nextPageUrl())){
                $morePages=false;
            }
            $results = array(
                "results" => $companies->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return \Response::json($results);

        }
    }
}
