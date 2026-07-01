<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private SearchService $searchService) {}

    public function index(Request $request)
    {
        $q      = trim($request->get('q', ''));
        $result = $this->searchService->search($q);
        return view('pages.search', $result);
    }

    public function suggest(Request $request)
    {
        $q       = trim($request->get('q', ''));
        $results = $this->searchService->suggest($q);
        return response()->json(['results' => $results, 'query' => $q]);
    }
}
