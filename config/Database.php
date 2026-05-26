<?php
/**
 * كلاس الاتصال بقاعدة البيانات (Database Connection Class)
 * نستخدم هنا نمط تصميم يسمى Singleton Pattern (النسخة الوحيدة).
 * الهدف منه: ضمان إنشاء "اتصال واحد فقط" بقاعدة البيانات وإعادة استخدامه في كل صفحات الموقع، 
 * بدلاً من فتح اتصال جديد في كل مرة، مما يحسن من أداء الموقع ويقلل الضغط على السيرفر.
 */
class Database {
    // 1. الخاصية $instance: من نوع private static 
    // هذه الخاصية ستحتفظ بالنسخة الوحيدة من هذا الكلاس. (static تعني أنها تابعة للكلاس نفسه وليس للكائنات المأخوذة منه)
    private static $instance = null;
    
    // 2. الخاصية $pdo: من نوع private
    // هذه الخاصية ستحتفظ باتصال الـ PDO الفعلي (الكائن الذي يتواصل مع MySQL).
    private $pdo;
    
    // إعدادات الاتصال بالسيرفر (يتم جعلها private لحمايتها من التعديل من خارج الكلاس)
    private $host = 'localhost';         // عنوان السيرفر المحلي (Localhost)
    private $db_name = 'university_portal'; // اسم قاعدة البيانات
    private $username = 'root';          // اسم مستخدم قاعدة البيانات (الافتراضي في Laragon/XAMPP هو root)
    private $password = '';              // كلمة المرور (الافتراضية فارغة)
    
    /**
     * الـ Constructor (الدالة البناءة): يتم جعلها private عمداً!
     * لماذا؟ لمنع أي شخص من إنشاء كائن جديد من هذا الكلاس باستخدام كلمة `new Database()`.
     * هذا أساس فكرة الـ Singleton Pattern.
     */
    private function __construct() {
        try {
            // DSN (Data Source Name): هو النص الذي يحتوي على معلومات الاتصال مثل نوع القاعدة، الهوست، واسم القاعدة.
            // نستخدم charset=utf8mb4 لضمان دعم اللغة العربية والرموز التعبيرية بدون ظهور حروف غريبة (علامات استفهام).
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            
            // إعدادات الـ PDO (هامة جداً للأمان وللمناقشة!)
            $options = [
                // PDO::ERRMODE_EXCEPTION:
                // يخبر الـ PDO بإلقاء استثناء (Exception) عند حدوث أي خطأ في الـ SQL.
                // هذا مفيد جداً في اكتشاف الأخطاء وتصحيحها (Debugging) بدلاً من فشل الكود بصمت.
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
                
                // PDO::FETCH_ASSOC:
                // يخبر الـ PDO بإرجاع البيانات من قاعدة البيانات على شكل مصفوفة ترابطية (Associative Array)
                // أي أن مفاتيح المصفوفة ستكون أسماء الأعمدة (مثل $row['name']). هذا أسهل في التعامل وأفضل للأداء.
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
                
                // PDO::ATTR_EMULATE_PREPARES => false: (مهم جداً للحماية من الـ SQL Injection)
                // نقوم بتعطيل المحاكاة (Emulation).
                // لماذا؟ لكي نجبر الـ PDO على إرسال الـ Query والبيانات بشكل منفصل تماماً إلى سيرفر الـ MySQL.
                // هذا هو خط الدفاع الأول والأساسي ضد ثغرة الحقن (SQL Injection)، لأنه يمنع المخترق من كتابة أوامر SQL داخل مدخلات النصوص.
                PDO::ATTR_EMULATE_PREPARES   => false,                  
            ];
            
            // إنشاء الاتصال الفعلي وإنشاء كائن الـ PDO باستخدام الإعدادات السابقة
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            // في حالة فشل الاتصال، يتم إيقاف السكربت وطباعة رسالة الخطأ (يفضل في التطبيقات الحقيقية تسجيلها في ملف Log بدلاً من عرضها للمستخدم)
            die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    // لمنع عمل استنساخ (Clone) للكائن، لضمان بقاء نسخة واحدة فقط (جزء من الـ Singleton)
    private function __clone() {}

    // لمنع استرجاع الكائن من حالة الـ Serialize (جزء من الـ Singleton)
    public function __wakeup() {
        throw new Exception("لا يمكن عمل unserialize للنسخة الوحيدة.");
    }

    /**
     * الدالة الأهم: getInstance (دالة static)
     * هي البوابة الوحيدة للحصول على كائن الاتصال.
     * طريقة عملها: تسأل "هل هناك نسخة تم إنشاؤها مسبقاً؟"
     * إذا لم تكن موجودة، تنشئها. وإذا كانت موجودة، ترجعها نفسها.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database(); // إنشاء النسخة لأول مرة
        }
        return self::$instance; // إرجاع النسخة
    }

    /**
     * دالة للحصول على كائن الـ PDO الفعلي (المستخدم في عمل الاستعلامات SELECT, INSERT...)
     */
    public function getConnection() {
        return $this->pdo;
    }
}
?>
