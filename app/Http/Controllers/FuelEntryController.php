<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuelEntry;
use Carbon\Carbon;
use Auth;

class FuelEntryController extends Controller
{
    // Map fuel types và unit types
    protected $fuelTypes = [
        'RON95_III' => 'Xăng RON 95-III',
        'DO05S_II'  => 'DO 0,05S-II',
        'E5_92_II'  => 'Xăng E5 RON 92-II',
    ];
    protected $unitTypes = [
        'lit'   => 'Lít',
        'other' => 'Đơn vị khác',
    ];

    public function create()
    {
        return view('entries.create', [
            'fuelTypes' => $this->fuelTypes,
            'unitTypes' => $this->unitTypes,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entry_time'    => 'required|date_format:Y-m-d\TH:i',
            'fuel_type'     => 'required|in:'.implode(',', array_keys($this->fuelTypes)),
            'unit_type'     => 'required|in:'.implode(',', array_keys($this->unitTypes)),
            'price'         => 'required|numeric|min:0',
            'vat_percentage'=> 'required|numeric|min:0|max:100',
            'quantity'      => 'required|numeric|min:0',
            'document_code' => 'nullable|string|max:100',
        ]);

        $data['entry_time'] = Carbon::createFromFormat('Y-m-d\TH:i',$data['entry_time'])->toDateTimeString();

        $data['employee_id'] = Auth::user()->employee_id;

        FuelEntry::create($data);

        return redirect()->route('entries.stats.form')
                         ->with('status','Đã lưu nhập hàng thành công.');
    }

    public function statsForm()
    {
        return view('entries.stats', [
            'fuelTypes' => $this->fuelTypes,
            'unitTypes' => $this->unitTypes,   // thêm unitTypes
        ]);
    }

    public function stats(Request $request)
    {
        $data = $request->validate([
            'from_date'    => 'required|date',
            'to_date'      => 'required|date|after_or_equal:from_date',
            'fuel_type'    => 'nullable|in:'.implode(',',array_keys($this->fuelTypes)),
            'has_document' => 'nullable|in:all,with,without',
        ]);

        $query = FuelEntry::whereBetween('entry_time', [
            "{$data['from_date']} 00:00:00",
            "{$data['to_date']}   23:59:59",
        ]);

        if ($data['fuel_type'] ?? false) {
            $query->where('fuel_type',$data['fuel_type']);
        }
        if (($data['has_document'] ?? '')==='with') {
            $query->whereNotNull('document_code');
        }
        if (($data['has_document'] ?? '')==='without') {
            $query->whereNull('document_code');
        }

        $entries  = $query->orderBy('entry_time','desc')->get();
        $totalQty = $entries->sum('quantity');

        return view('entries.stats', [
            'fuelTypes' => $this->fuelTypes,
            'unitTypes' => $this->unitTypes,
            'entries'   => $entries,
            'totalQty'  => $totalQty,
            'data'      => $data,
        ]);
    }

    public function edit($id)
    {
        $entry = FuelEntry::where('employee_id', Auth::user()->employee_id)
                          ->findOrFail($id);

        return view('entries.edit', compact('entry'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'document_code' => 'nullable|string|max:100',
        ]);

        $entry = FuelEntry::where('employee_id', Auth::user()->employee_id)
                          ->findOrFail($id);

        $entry->document_code = $data['document_code'] ?? null;
        $entry->save();

        return redirect()->route('entries.stats.form')
                         ->with('status','Đã cập nhật chứng từ thành công.');
    }

    public function destroy($id)
    {
        $entry = FuelEntry::where('employee_id', Auth::user()->employee_id)
                          ->findOrFail($id);

        $entry->delete();

        return redirect()->route('entries.stats.form')
                         ->with('status','Đã xóa nhập hàng.');
    }
}

