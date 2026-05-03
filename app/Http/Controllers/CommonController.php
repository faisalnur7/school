<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use App\Models\Division;
use App\Models\District;
use App\Models\PoliceStation;
use App\Models\PostOffice;

class CommonController extends Controller
{
    public function load_section_groups(Request $request)
    {
        return [
            'sections' => Section::where('school_class_id', $request->school_class_id)->get(),
        ];
    }

    public function load_groups(Request $request)
    {
        return ['groups' => Group::all()];
    }

    public function loadSections(Request $request)
    {
        $sections = Section::where('school_class_id', $request->school_class_id)->get();

        return response()->json([
            'sections' => $sections
        ]);
    }

    public function loadGroups(Request $request)
    {
        $groups = Group::where('section_id', $request->section_id)->get();

        return response()->json([
            'groups' => $groups
        ]);
    }

    public function load_districts(Request $request)
    {
        $data['districts'] = District::query()->where('division_id',$request->division_id)->get();
        return $data;
    }

    public function load_police_stations(Request $request)
    {
        $data['police_stations'] = PoliceStation::query()->where('district_id',$request->district_id)->get();
        return $data;
    }

    public function load_post_offices(Request $request)
    {
        $data['post_offices'] = PostOffice::query()->where('police_station_id',$request->police_station_id)->get();
        return $data;
    }
}
