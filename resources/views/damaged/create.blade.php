@extends('layouts.app')

@section('title', 'Tambah Barang Rusak')

@section('content')
<div class="card shadow">
    <div class="card-header bg-white">
        <h5 class="mb-0">Form Barang Rusak</h5>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('damaged.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input
                    type="date"
                    name="tanggal"
                    class="form-control"
                    value="{{ date('Y-m-d') }}"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Supplier</label>

                <select
                    name="supplier_id"
                    id="supplier_id"
                    class="form-select"
                    required>

                    <option value="">-- Pilih Supplier --</option>

                    @foreach($suppliers as $supplier)

                        <option value="{{ $supplier->id }}">
                            {{ $supplier->nama_supplier }}
                        </option>

                    @endforeach

                </select>
            </div>

            <div class="table-responsive">

                <table class="table table-bordered align-middle" id="produkTable">

                    <thead class="table-light">

                    <tr>

                        <th width="40%">Produk</th>

                        <th width="15%">Jumlah</th>

                        <th>Keterangan</th>

                        <th width="10%">Aksi</th>

                    </tr>

                    </thead>

                    <tbody>

                    <tr>

                        <td>

                            <select
                                name="items[0][product_id]"
                                class="form-select product-select"
                                required>

                                <option value="">Pilih Produk</option>

                                @foreach($products as $product)

                                    <option
                                        value="{{ $product->id }}"
                                        data-supplier="{{ $product->supplier_id }}">

                                        {{ $product->nama_produk }}
                                        (Stok : {{ $product->current_stock }})

                                    </option>

                                @endforeach

                            </select>

                        </td>

                        <td>

                            <input
                                type="number"
                                name="items[0][jumlah]"
                                class="form-control"
                                min="1"
                                required>

                        </td>

                        <td>

                            <input
                                type="text"
                                name="items[0][keterangan]"
                                class="form-control"
                                placeholder="Expired / Pecah">

                        </td>

                        <td class="text-center">

                            <button
                                type="button"
                                class="btn btn-danger remove-row">

                                Hapus

                            </button>

                        </td>

                    </tr>

                    </tbody>

                </table>

            </div>

            <button
                type="button"
                class="btn btn-success"
                id="addRow">

                + Tambah Produk

            </button>

            <hr>

            <button
                type="submit"
                class="btn btn-primary">

                <i class="fas fa-save"></i>

                Simpan

            </button>

        </form>

    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    let index = 1;

    const supplier = document.getElementById('supplier_id');

    const tbody = document.querySelector('#produkTable tbody');

    const firstSelect = document.querySelector('.product-select');

    const optionHTML = firstSelect.innerHTML;



    function filterProducts() {

        const supplierId = supplier.value;

        document.querySelectorAll('.product-select').forEach(function(select){

            Array.from(select.options).forEach(function(option){

                if(option.value===""){

                    option.hidden = false;

                    return;

                }

                option.hidden = supplierId !== "" &&
                                option.dataset.supplier != supplierId;

            });

        });

    }



    supplier.addEventListener('change', filterProducts);



    document.getElementById('addRow').addEventListener('click', function(){

        let tr = document.createElement('tr');

        tr.innerHTML = `

            <td>

                <select
                    name="items[${index}][product_id]"
                    class="form-select product-select"
                    required>

                    ${optionHTML}

                </select>

            </td>

            <td>

                <input
                    type="number"
                    class="form-control"
                    name="items[${index}][jumlah]"
                    min="1"
                    required>

            </td>

            <td>

                <input
                    type="text"
                    class="form-control"
                    name="items[${index}][keterangan]"
                    placeholder="Expired / Pecah">

            </td>

            <td class="text-center">

                <button
                    type="button"
                    class="btn btn-danger remove-row">

                    Hapus

                </button>

            </td>

        `;

        tbody.appendChild(tr);

        index++;

        filterProducts();

    });



    tbody.addEventListener('click', function(e){

        if(e.target.classList.contains('remove-row')){

            if(tbody.rows.length > 1){

                e.target.closest('tr').remove();

            }

        }

    });

});
</script>

@endsection