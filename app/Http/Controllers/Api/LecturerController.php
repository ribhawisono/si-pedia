<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\LecturerResource;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function index(Request $request)
    {
        // Not cached: paginators embed Eloquent Collections and are unsafe to
        // Cache::remember() (see unserialize incomplete-object crash fix).
        $lecturers = Lecturer::with('user:id,name,email')
            ->where('status', 'active')
            ->when($request->q, fn ($q, $s) =>
                $q->where('nidn', 'like', "%{$s}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"))
            )
            ->paginate($request->integer('per_page', 15, 1, 50))
            ->withQueryString();
        return LecturerResource::collection($lecturers);
    }
}
