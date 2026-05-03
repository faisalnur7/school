<?php

namespace App\Http\Controllers;

use App\Enums\RoomType;
use App\Models\Building;
use App\Models\Department;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::query()
            ->with(['building', 'department'])
            ->when($request->filled('building_id'), fn ($query) => $query->where('building_id', $request->building_id))
            ->when($request->filled('department_id'), fn ($query) => $query->where('department_id', $request->department_id))
            ->when($request->filled('room_type'), fn ($query) => $query->where('room_type', $request->room_type))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->withCount('assets')
            ->orderBy('building_id')
            ->orderBy('floor_number')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $buildings = Building::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $roomTypes = RoomType::cases();

        return view('pages.settings.rooms.index', compact('rooms', 'buildings', 'departments', 'roomTypes'));
    }

    public function create()
    {
        $buildings = Building::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $roomTypes = RoomType::cases();

        return view('pages.settings.rooms.create', compact('buildings', 'departments', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        $buildings = Building::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $roomTypes = RoomType::cases();

        return view('pages.settings.rooms.edit', compact('room', 'buildings', 'departments', 'roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $this->validateData($request, $room->id);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        if ($room->assets()->exists()) {
            return back()->with('error', 'Cannot delete this room because assets are linked to it.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }

    private function validateData(Request $request, ?int $roomId = null): array
    {
        return $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:rooms,code,' . $roomId,
            'floor_number' => 'required|integer|min:0',
            'room_type' => 'required|in:' . implode(',', RoomType::values()),
            'seating_capacity' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
