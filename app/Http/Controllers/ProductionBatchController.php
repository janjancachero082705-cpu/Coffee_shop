<?php
namespace App\Http\Controllers;
use App\Models\ProductionBatch;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class ProductionBatchController extends Controller
{
    public function __construct(private InventoryService $inventory) {}

    public function index() {
        $batches = ProductionBatch::with('product')->orderBy('production_date','desc')->paginate(20);
        return view('production.index', compact('batches'));
    }
    public function create() {
        $products = Product::where('is_active',true)->where('is_raw_material',false)->orderBy('name')->get();
        return view('production.create', compact('products'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'production_date' => 'required|date',
            'cherries_input_kg' => 'required|numeric|min:0.01',
            'cherries_cost_per_kg' => 'required|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'processing_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'product_id' => 'required|exists:products,id',
            'output_quantity' => 'required|numeric|min:0.01',
            'output_unit' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        $data['batch_number'] = 'BATCH-' . now()->format('Ymd') . '-' . str_pad(ProductionBatch::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        $data['cherries_total_cost'] = $data['cherries_input_kg'] * $data['cherries_cost_per_kg'];
        $data['total_cost'] = $data['cherries_total_cost'] + ($data['labor_cost'] ?? 0) + ($data['processing_cost'] ?? 0) + ($data['other_cost'] ?? 0);
        $data['cost_per_output_unit'] = $data['output_quantity'] > 0 ? $data['total_cost'] / $data['output_quantity'] : 0;
        $data['yield_percentage'] = $data['cherries_input_kg'] > 0 ? ($data['output_quantity'] / $data['cherries_input_kg']) * 100 : 0;
        $data['user_id'] = auth()->id();

        $batch = ProductionBatch::create($data);

        // Deduct cherries
        $cherry = $this->inventory->getCherryProduct();
        if ($cherry) {
            $this->inventory->removeStock(
                $cherry,
                $data['cherries_input_kg'],
                ['type' => 'production', 'id' => $batch->id],
                "Used for batch {$batch->batch_number}",
                $data['production_date']
            );
        }

        // Add output product stock
        $outputProduct = Product::find($data['product_id']);
        if ($outputProduct) {
            $this->inventory->addStock(
                $outputProduct,
                $data['output_quantity'],
                $data['cost_per_output_unit'],
                ['type' => 'production', 'id' => $batch->id],
                "Output from batch {$batch->batch_number}",
                $data['production_date']
            );
        }

        return redirect()->route('production.index')->with('success','Batch recorded & inventory updated.');
    }
    public function show(ProductionBatch $batch) { return view('production.show', compact('batch')); }
    public function edit(ProductionBatch $batch) { return view('production.edit', compact('batch')); }
    public function update(Request $request, ProductionBatch $batch) { return back(); }
    public function destroy(ProductionBatch $batch) {
        $batch->delete();
        return redirect()->route('production.index')->with('success','Batch deleted.');
    }
}