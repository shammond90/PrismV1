<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Position;

class PositionController extends Controller
{
    public function edit(Position $position)
    {
        return view('admin.positions.edit', compact('position'));
    }

    public function store(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $data['department_id'] = $department->id;
        Position::create($data);
        return redirect()->route('admin.departments.edit', $department)->with('success','Position added.');
    }

    public function update(Request $request, Position $position)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $position->update($data);
        return redirect()->route('admin.departments.edit', $position->department)->with('success','Position updated.');
    }

    public function destroy(Position $position)
    {
        $dept = $position->department;
        $position->delete();
        return redirect()->route('admin.departments.edit', $dept)->with('success','Position removed.');
    }
}
