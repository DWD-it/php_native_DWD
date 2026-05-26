# University Portal System (Web Development II - Project)

## نظرة عامة (Overview)
تم بناء هذا المشروع ليكون بوابة للجامعة، وهو يحقق متطلبات المشروع النهائي لمادة **Web Development II**. 
تم التركيز بشكل أساسي على المفاهيم المتقدمة للبرمجة وكتابة **Clean Code** باتباع المعايير الأكاديمية والمهنية.

---

## 🛠️ التقنيات المستخدمة (Technologies Used)
- **Backend:** Native PHP (No Frameworks).
- **Database:** MySQL.
- **Frontend:** HTML5, Custom CSS3 (Modern Glassmorphism UI - No Bootstrap/Tailwind).
- **Architecture:** Object-Oriented Programming (OOP) & Singleton Design Pattern.

---

## 🔒 الأمان والحماية (Security First)
لضمان أعلى معايير الحماية (Security) للحصول على العلامة الكاملة تم تطبيق التالي:
1. **منع الـ SQL Injection:** تم استخدام **PDO Prepared Statements** في جميع العمليات على قاعدة البيانات (CRUD). لم يتم استخدام دمج النصوص (String Concatenation) في أي استعلام SQL نهائياً.
2. **تشفير كلمات المرور:** تم استخدام `password_hash()` مع خوارزمية (Bcrypt) لتشفير كلمات المرور عند إنشاء المستخدم، واستخدام `password_verify()` للتحقق منها أثناء تسجيل الدخول.
3. **منع الـ XSS (Cross-Site Scripting):** تم استخدام دالة `htmlspecialchars()` عند عرض أي بيانات مدخلة من قبل المستخدمين في صفحات الـ HTML لضمان عدم تنفيذ أي أكواد خبيثة.
4. **التحكم في الصلاحيات (Role-Based Access Control):** تم استخدام جلسات الـ PHP `session_start()` لتحديد ما إذا كان المستخدم (Admin) أو (Student)، وتوجيه أي محاولة وصول غير مصرح بها فوراً (Redirect) لحماية الصفحات الإدارية.

---

## 🧩 مفاهيم الـ OOP المستخدمة (OOP Concepts Applied)
المشروع مبني بالكامل باستخدام البرمجة كائنية التوجه (OOP)، وتم تطبيق المبادئ الأساسية الأربعة:
1. **التغليف (Encapsulation):** تم استخدام الـ Access Modifiers (`private`, `protected`, `public`) في الكلاسات لحماية خصائص الكلاس (Properties) وإتاحة الوصول لها فقط عبر دوال محددة (Getters & Setters).
2. **الوراثة (Inheritance):** تم إنشاء كلاس أساسي يسمى `User` يحتوي على الخصائص المشتركة (الاسم، الإيميل، كلمة المرور)، ثم تم إنشاء كلاسات ترث منه وهي `Admin` و `Student` لتجنب تكرار الكود.
3. **التجريد (Abstraction):** تم استخدام الكلاسات المجردة (Abstract Classes) لضمان أن بعض الكلاسات (مثل `User`) لا يمكن أخذ نسخة (Instance) منها مباشرة، بل يجب استخدام الكلاسات المشتقة.
4. **تعدد الأشكال (Polymorphism):** بعض الدوال (Methods) الموجودة في الـ Base Class تم إعادة كتابتها أو تعديل سلوكها (Overriding) في الكلاسات المشتقة بناءً على احتياج كل دور (Admin vs Student).

---

## 📂 نمط الـ Singleton لربط قاعدة البيانات
تم استخدام **Singleton Design Pattern** في كلاس `Database`.
**السبب:** لمنع إنشاء أكثر من اتصال بقاعدة البيانات (Connection) في نفس الوقت، مما يوفر استهلاك موارد السيرفر ويزيد من كفاءة النظام. الكلاس يقوم بإرجاع نفس الاتصال (Instance) في كل مرة يتم طلبه.

---

## 🌟 مميزات إضافية (Bonus Features)
1. **شريط البحث (Search Bar):** استخدام `LIKE` في استعلامات SQL للبحث الديناميكي عن الكورسات والأقسام.
2. **إحصائيات لوحة التحكم (Dashboard Statistics):** عرض عدد المستخدمين، الكورسات، والأقسام بشكل تفاعلي ومباشر من قاعدة البيانات.
3. **ملف الطالب الشخصي (Student Profile):** إمكانية تحديث الطالب لبياناته (الاسم، كلمة المرور) بأمان تام.

---

## 🚀 كيفية التشغيل (How to Run on Laragon)
1. قم بفتح **Laragon** وتأكد من تشغيل (Apache & MySQL).
2. انسخ مجلد المشروع `university_portal` إلى المسار `C:\laragon\www\`.
3. افتح أداة إدارة قواعد البيانات (مثل phpMyAdmin أو HeidiSQL).
4. قم بإنشاء قاعدة بيانات باسم `university_portal` واستورد الملف `database.sql` أو قم بتشغيل الأكواد الموجودة فيه.
5. افتح المتصفح على الرابط `http://localhost/university_portal`.

**للدخول لأول مرة:**
- **الإيميل:** admin@university.edu
- **كلمة المرور:** admin123
