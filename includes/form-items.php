<?php /* Repeating item rows for material gate passes. $itemsId must be set. */ ?>
<div class="d-flex justify-content-between align-items-center mt-3 mb-2"><b>Items</b><button type="button" class="btn btn-sm btn-outline-ink" data-add-item="<?= e($itemsId) ?>"><i class="bi bi-plus-lg"></i> Add item</button></div>
<div class="table-responsive"><table class="table table-sm align-middle" id="<?= e($itemsId) ?>">
  <thead><tr><th>Description</th><th style="width:90px">Qty</th><th style="width:100px">Unit</th><th>Serial no.</th><th>Condition</th><th style="width:40px"></th></tr></thead>
  <tbody><tr>
    <td><input class="form-control form-control-sm" required></td><td><input type="number" min="1" value="1" class="form-control form-control-sm" required></td>
    <td><select class="form-select form-select-sm"><option>nos</option><option>kg</option><option>m</option><option>set</option><option>box</option></select></td>
    <td><input class="form-control form-control-sm"></td><td><select class="form-select form-select-sm"><option>New</option><option>Good</option><option>Needs repair</option><option>Scrap</option></select></td>
    <td><button type="button" class="btn btn-sm text-danger" data-remove-item aria-label="Remove item"><i class="bi bi-trash"></i></button></td></tr></tbody></table></div>
