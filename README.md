# تعليمات تثبيت وتشغيل المشروع

## المتطلبات الأساسية
- PHP إصدار 8.1 أو أحدث
- Composer (مدير حزم PHP)
- Node.js و npm (لإدارة حزم JavaScript)
- خادم قاعدة بيانات مثل MySQL (مثل WAMP أو XAMPP على Windows)

## خطوات التثبيت

### 1. تحميل المشروع
إذا كان المشروع موجودًا على GitHub أو مستودع Git، قم باستنساخه باستخدام الأمر التالي:
```
git clone [رابط المستودع]
```
أو قم بنسخ الملفات إلى مجلد www في WAMP (مثل c:\wamp64\www\kofta_task_pro).

### 2. تثبيت اعتماديات PHP
انتقل إلى مجلد المشروع وقم بتشغيل:
```
composer install
```

### 3. إعداد ملف البيئة
- انسخ ملف `.env.example` إلى `.env`:
```
cp .env.example .env
```

### 4. توليد مفتاح التطبيق
```
php artisan key:generate
```

### 5. إنشاء قاعدة البيانات وتشغيل الهجرات
- قم بتشغيل الهجرات:
```
php artisan migrate --seeder
```

### 6. تثبيت اعتماديات JavaScript
```
npm install
```

### 7. بناء ملفات الأصول (CSS و JS)
```
npm run build
```
أو للتطوير مع المراقبة:
```
npm run dev
```

### 8. تشغيل الخادم
```
php artisan serve
```
سيتم تشغيل التطبيق على http://localhost:8000
