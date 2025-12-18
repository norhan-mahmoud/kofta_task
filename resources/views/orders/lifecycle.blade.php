@extends('layout.app')

@section('title', 'حياة المنتج داخل الطلب')
@section('subtitle', 'تتبع كامل من التوريد حتى البيع')

@section('style')
<style>
.lifecycle-container {
    margin-top: 20px;
}
.product-card {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 25px;
    background: #fff;
}
.stage {
    position: relative;
    padding: 15px 20px;
    margin-bottom: 15px;
    border-radius: 8px;
    background: #f9fafb;
    border-right: 5px solid #dee2e6;
}
.stage.supply { border-color: #0dcaf0; }
.stage.manufacturing { border-color: #ffc107; }
.stage.batch { border-color: #198754; }

.stage-title {
    margin-bottom: 5px;
}
.stage small {
    color: #6c757d;
}
.stage-children {
    margin-right: 25px;
    margin-top: 10px;
}
.qty-badge {
    color: #212529;
    font-size: 14px;
}
</style>
@endsection

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-10">

        <form id="search-form" class="mb-4">
            <div class="d-flex gap-2">
                <input type="text" name="order_number" class="form-control"
                       placeholder="رقم الطلب (ORD-001)" required>
                <button class="btn btn-primary">بحث</button>
                <a href="{{ route('home') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </form>

        <div id="result-area"></div>

    </div>
</div>
@endsection

@section('script')
<script>
document.getElementById('search-form').addEventListener('submit', function(e){
    e.preventDefault();

    const params = new URLSearchParams(new FormData(this)).toString();
    document.getElementById('result-area').innerHTML =
        `<div class="alert alert-info">جاري التحميل...</div>`;

    fetch("{{ route('orders.search') }}?" + params)
        .then(res => res.json())
        .then(data => render(data))
        .catch(() => {
            document.getElementById('result-area').innerHTML =
                `<div class="alert alert-danger">لم يتم العثور على الطلب</div>`;
        });
});

function render(data) {

    let html = `
        <div class="card">
            <div class="card-header">
                <strong>طلب:</strong> ${data.order.order_number} |
                العميل: ${data.order.customer}
            </div>
            <div class="card-body lifecycle-container">
    `;

    data.items.forEach(item => {

        html += `
            <div class="product-card">
                <h5>${item.item_name}
                    <span class="qty-badge">${item.ordered_qty}</span>
                </h5>
        `;

        item.flows.forEach(flow => {

            html += `
                <div class="stage batch">
                    <div class="stage-title">Batch #${flow.batch_id}</div>
                    <small>الكمية المسحوبة: ${flow.withdrawn_qty}</small>
                </div>
            `;

            html += renderLifecycle(flow.lifecycle);
        });

        html += `</div>`;
    });

    html += `</div></div>`;

    document.getElementById('result-area').innerHTML = html;
}

function renderLifecycle(node) {

    if (!node) return '';

    let html = `<div class="stage-children">`;

    if (node.type === 'manufacturing') {
        html += `
            <div class="stage manufacturing">
                <div class="stage-title">مرحلة تصنيع</div>
                <small>تاريخ: ${node.factory_date}</small>
            </div>
        `;

        node.inputs.forEach(input => {
            html += `
                <div class="stage-children">
                    <div class="stage batch">
                        ${input.item_name}
                        <span class="qty-badge"> ( ${input.used_amount} )</span>
                    </div>
                    ${renderLifecycle(input.source)}
                </div>
            `;
        });
    }

    if (node.type === 'supply') {
        html += `
            <div class="stage supply">
                <div class="stage-title">توريد</div>
                <small>المورد: ${node.supplier}</small><br>
                <small>تاريخ الاستلام: ${node.received_at}</small>
            </div>
        `;
    }

    html += `</div>`;

    return html;
}
</script>
@endsection
