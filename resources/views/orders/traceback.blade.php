@extends('layout.app')

@section('title', 'حياة المنتج داخل الطلب')
@section('subtitle', 'تتبع كامل من التوريد حتى البيع')

@section('style')
<style>


.timeline {
    position: relative;
    margin: 20px 0;
    padding-right: 30px;
    border-right: 4px solid #667eea;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-right: 4px solid #667eea;
    transition: transform 0.3s, box-shadow 0.3s;
}

.timeline-item:hover {
    transform: translateX(-5px);
    box-shadow: 0 8px 12px rgba(0,0,0,0.15);
}

.timeline-item::before {
    content: '';
    position: absolute;
    right: -37px;
    top: 25px;
    width: 16px;
    height: 16px;
    background: #667eea;
    border: 3px solid #fff;
    border-radius: 50%;
    box-shadow: 0 0 0 3px #667eea;
}

.timeline-title {
    font-weight: bold;
    font-size: 16px;
    color: #333;
    margin-bottom: 8px;
}

.timeline-date {
    font-size: 13px;
    color: #999;
}

.product-card {
    border: none;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    background: #fff;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    border-left: 5px solid #667eea;
}

.product-card h5 {
    color: #333;
    font-weight: 600;
    margin-bottom: 15px;
}

.order-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.order-info h5 {
    font-size: 24px;
    margin-bottom: 15px;
}

.order-info p {
    margin-bottom: 8px;
    font-size: 15px;
}

.search-form {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.search-form .d-flex {
    gap: 12px;
}

.search-form input {
    border-radius: 8px;
    border: 2px solid #e0e0e0;
    padding: 12px 15px;
}

.search-form button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 8px;
    padding: 12px 25px;
    font-weight: 600;
}

.alert {
    border-radius: 12px;
    border: none;
    padding: 15px 20px;
    font-weight: 500;
}

.alert-info {
    background: #e7f3ff;
    color: #0066cc;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
}
</style>
@endsection

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-10">

        <form id="search-form" class="search-form">
            <div class="d-flex gap-2">
                <input type="text" name="order_number" class="form-control"
                       placeholder="رقم الطلب (ORD-001)" required>
                <button class="btn btn-primary " type="submit">بحث</button>
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

    fetch("{{ route('orders.traceback-search') }}?" + params)
        .then(res => res.json())
        .then(data => render(data))
        .catch(() => {
            document.getElementById('result-area').innerHTML =
                `<div class="alert alert-danger">لم يتم العثور على الطلب</div>`;
        });
});

function render(data) {
    const area = document.getElementById('result-area');

    if (!data.tracebacks || data.tracebacks.length === 0) {
        area.innerHTML = `<div class="alert alert-warning">لا توجد بيانات</div>`;
        return;
    }

    let html = `
        <div class="order-info">
            <h5>📦 ${data.order.order_number}</h5>
            <p>👤 العميل: ${data.order.customer}</p>
            <p>📅 تاريخ الطلب: ${data.order.date}</p>
        </div>
    `;

    data.tracebacks.forEach(trace => {
        html += `
        <div class="product-card">
            <h5>${trace.item_name}</h5>
            <p><strong>الكمية المستخدمة:</strong> ${trace.withdrawn_qty}</p>

            <div class="timeline">
                ${renderSource(trace.source)}
            </div>
        </div>
        `;
    });

    area.innerHTML = html;
}

function renderSource(source) {
    if (!source) return '';

    if (source.type === 'supply') {
        return `
        <div class="timeline-item">
            <div class="timeline-title">📥 توريد من مورد</div>
            <div><strong>المورد:</strong> ${source.supplier ?? '-'}</div>
            <div class="timeline-date">📅 تاريخ الاستلام: ${source.received_at}</div>
        </div>
        `;
    }

    if (source.type === 'manufacturing') {
        let html = `
        <div class="timeline-item">
            <div class="timeline-title">⚙️ عملية تصنيع</div>
            <div class="timeline-date">📅 تاريخ التصنيع: ${source.manufacturing_date}</div>
        </div>
        `;

        if (source.inputs && source.inputs.length) {
            source.inputs.forEach(input => {
                html += `
                <div class="timeline-item ms-4">
                    <div class="timeline-title">
                        📦 مادة مستخدمة: ${input.item_name}
                    </div>
                    <div><strong>الكمية:</strong> ${input.used_amount}</div>
                    ${renderSource(input.source)}
                </div>
                `;
            });
        }

        return html;
    }

    return '';
}
</script>
@endsection
