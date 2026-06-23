<?php
// ۱. بارگذاری فایل‌های کتابخانه PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// ۲. تنظیمات اتصال به دیتابیس
$host = "localhost";
$db_user = "root";       
$db_pass = "";           
$db_name = "your_db";    

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("اتصال به دیتابیس برقرار نشد: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// ۳. دریافت و ایمن‌سازی اطلاعات فرم
$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$message = mysqli_regular = mysqli_real_escape_string($conn, $_POST['message']);

if (!empty($name) && !empty($email) && !empty($message)) {
    
    // الف) ذخیره در دیتابیس (همان کدهای قبلی شما)
    $sql = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
    $conn->query($sql);
    
    // ب) تنظیم و ارسال ایمیل با PHPMailer
    $mail = new PHPMailer(true);

    try {
        // تنظیمات سرور ارسال‌کننده (SMTP)
        $mail->isSMTP();                                      // استفاده از پروتکل SMTP
        $mail->Host       = 'smtp.gmail.com';                 // اگر از هاست استفاده می‌کنید: ://yoursite.com
        $mail->SMTPAuth   = true;                             // فعال‌سازی تایید هویت SMTP
        $mail->Username   = 'azad22373@gmail.com';           // آدرس ایمیل شما (گوگل یا هاست)
        $mail->Password   = 'ftdb azhu ccsy adqw';         // رمز عبور ایمیل (توضیحات پایین را بخوانید)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // نوع رمزگذاری امنیتی
        $mail->Port       = 587;                              // پورت استاندارد برای ارسال امن
        $mail->CharSet    = 'UTF-8';                          // پشتیبانی کامل از حروف فارسی

        // تنظیمات فرستنده و گیرنده
        $mail->setFrom('your_gmail@gmail.com', 'فرم تماس سایت');
        $mail->addAddress('manager@yoursite.com');             // ایمیل مدیر سایت (دریافت‌کننده پیام)
        $mail->addReplyTo($email, $name);                     // تنظیم ایمیل کاربر برای پاسخ دادن مستقیم

        // محتوای ایمیل
        $mail->isHTML(true);                                  // ارسال ایمیل به صورت HTML (شیک و مرتب)
        $mail->Subject = 'پیام جدید از فرم تماس با ما';
        
        // قالب ظاهری ایمیل با کدهای HTML
        $mail->Body    = "
            <div style='direction: rtl; font-family: Tahoma; padding: 20px; border: 1px solid #eee;'>
                <h2 style='color: #007bff;'>یک پیام جدید دریافت شد</h2>
                <p><strong>نام فرستنده:</strong> $name</p>
                <p><strong>ایمیل فرستنده:</strong> $email</p>
                <p><strong>متن پیام:</strong></p>
                <div style='background: #f9f9f9; padding: 15px; border-radius: 5px; line-height: 1.6;'>
                    " . nl2br($message) . "
                </div>
            </div>
        ";
        
        // متن ساده برای ایمیل‌خوان‌های قدیمی
        $mail->AltBody = "نام: $name\nایمیل: $email\nمتن پیام:\n$message";

        $mail->send();
        echo "success";
        
    } catch (Exception $e) {
        // در صورت بروز خطا در ارسال ایمیل، خطای فنی در لاگ سرور ثبت می‌شود
        echo "error";
    }
} else {
    echo "error";
}

$conn->close();
?>
