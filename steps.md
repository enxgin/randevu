Harika bir proje ve çok detaylı bir teknik şartname! Bu şartnameye uygun olarak, CodeIgniter ve TailwindCSS kullanarak Güzellik Salonu Randevu ve Yönetim Sistemi'ni sıfırdan geliştirmek için izlenmesi gereken adımları, mantıksal bir sırayla ve aşamalandırılmış olarak aşağıda bulabilirsiniz.

Bu yol haritası, projenin temelden başlayarak modüllerin birbiri üzerine inşa edildiği, test edilebilir ve yönetilebilir adımlardan oluşur.


### **Güzellik Salonu Yönetim Sistemi Geliştirme Yol Haritası**

### **Aşama 0: Hazırlık ve Altyapı Kurulumu (Basamak 1-5)**

Bu aşama, projenin temelini oluşturur. Kod yazmaya başlamadan önce geliştirme ortamının ve proje iskeletinin doğru bir şekilde kurulması kritik öneme sahiptir.

- **Basamak 1: Geliştirme Ortamının Kurulması**
    - **Aksiyon:** Yerel makineye PHP, Composer, MySQL (veya MariaDB) ve Node.js (TailwindCSS için gerekli) kurulumlarının yapılması.
    - **Amaç:** CodeIgniter ve TailwindCSS'in çalışması için gerekli sunucu ve araçların hazır hale getirilmesi.
- **Basamak 2: CodeIgniter Projesinin Oluşturulması**
    - **Aksiyon:** Composer kullanılarak en güncel CodeIgniter 4 projesinin oluşturulması. `.env` dosyasının konfigüre edilerek veritabanı bağlantısı ve uygulama URL'i gibi temel ayarların yapılması.
    - **Amaç:** Projenin ana iskeletini oluşturmak ve veritabanı ile iletişimini sağlamak.
- **Basamak 3: TailwindCSS Entegrasyonu**
    - **Aksiyon:** `npm` (Node Package Manager) aracılığıyla TailwindCSS ve bağımlılıklarının (autoprefixer, postcss) projeye dahil edilmesi. `tailwind.config.js` ve ana CSS girdi dosyasının (`app.css` vb.) oluşturulması. Gerekli scriptlerin `package.json` dosyasına eklenerek derleme (build) işleminin otomatikleştirilmesi.
    - **Amaç:** Projenin tüm arayüzlerinin (frontend) **Modül 8.2**'de belirtilen responsive (duyarlı) yapıda geliştirilebilmesi için altyapıyı hazırlamak.
- **Basamak 4: Veritabanı Şemasının Tasarlanması ve Oluşturulması**
    - **Aksiyon:** Teknik şartnamedeki tüm modüller göz önünde bulundurularak veritabanı tablolarının tasarlanması. CodeIgniter Migrations (Göçler) kullanılarak bu tabloların oluşturulması.
    - **Önemli Tablolar:** `branches` (şubeler), `roles` (roller), `permissions` (izinler), `role_permissions` (rol-izin pivot), `users` (kullanıcılar), `customers` (müşteriler), `services` (hizmetler), `service_staff` (hizmet-personel pivot), `packages` (paketler), `appointments` (randevular), `payments` (ödemeler), `cash_movements` (kasa hareketleri), `commissions` (primler), `notifications` (bildirimler).
    - **Kritik Not:** **Modül 8.2**'ye uygun olarak `appointments`, `customers`, `payments` gibi neredeyse tüm tablolara `branch_id` alanı eklenmelidir. Bu, sistemin en başından çoklu şube desteğine sahip olmasını sağlar.
    - **Amaç:** Sistemin veri modelini oluşturmak ve tüm verilerin organize bir şekilde saklanmasını garanti altına almak.
- **Basamak 5: Ana Arayüz (Layout) ve Temel Yapının Oluşturulması**
    - **Aksiyon:** TailwindCSS ile ana layout dosyasının (örn: `app.php`) oluşturulması. Bu dosya, tüm sayfalarda ortak olacak header (üst bilgi), sidebar (yan menü) ve footer (alt bilgi) alanlarını içermelidir.
    - **Amaç:** Tekrarlanan kodları önlemek ve tutarlı bir kullanıcı arayüzü sağlamak.

### **Aşama 1: Temel Sistem ve Kullanıcı Yönetimi (Basamak 6-9) - Modül 1 & 8**

Altyapı hazırlandıktan sonra, sisteme giriş yapacak kullanıcıların ve onların yetkilerinin yönetileceği çekirdek modüller geliştirilir.

- **Basamak 6: Şube ve Rol/Yetki Yönetimi (Admin Paneli)**
    - **Aksiyon:** Sadece "Admin" rolünün erişebileceği, yeni şube, rol ve yetki oluşturma, düzenleme, silme (CRUD) arayüzlerinin yapılması.
    - **Amaç:** **Modül 1.1**'deki dinamik yetkilendirme altyapısının temelini atmak.
- **Basamak 7: Kullanıcı Yönetimi (Admin & Yönetici Paneli)**
    - **Aksiyon:** Admin'in tüm şubeler için, Yöneticinin ise sadece kendi şubesi için personel ekleyip silebileceği CRUD arayüzlerinin oluşturulması. Her kullanıcıya bir şube ve bir rol atanmalıdır.
    - **Amaç:** Sisteme kullanıcıların (personel, danışma vb.) kaydedilmesini sağlamak.
- **Basamak 8: Güvenli Giriş (Login) ve Yetkilendirme (Authentication & Authorization)**
    - **Aksiyon:** Giriş sayfası ve formunun oluşturulması. Kullanıcı giriş yaptığında, rolü ve şubesi Session'a (oturum) kaydedilmelidir. **Modül 8.3**'e uygun olarak şifreler veritabanına `hash`'lenerek kaydedilmelidir. Erişimi kısıtlamak için bir Controller Filtresi veya Middleware yazılarak her istekte kullanıcının rolü ve yetkileri sunucu tarafında kontrol edilmelidir.
    - **Amaç:** Sistemi güvenli hale getirmek ve herkesin sadece yetkisi dahilindeki sayfa ve verileri görmesini sağlamak.
- **Basamak 9: Kişiselleştirilmiş Arayüz**
    - **Aksiyon:** Giriş yapan kullanıcının rolüne göre yan menünün (sidebar) dinamik olarak oluşturulması. Örneğin, "Personel" rolü sadece "Randevu Takvimim" ve "Prim Raporum" menülerini görürken, "Yönetici" kendi şubesinin tüm menülerini görmelidir.
    - **Amaç:** Kullanıcı deneyimini iyileştirmek ve kafa karışıklığını önlemek.

### **Aşama 2: Müşteri ve Hizmet Altyapısı (Basamak 10-12) - Modül 3 & 4**

Randevu oluşturmanın ön koşulları olan müşteri ve hizmet modülleri geliştirilir.

- **Basamak 10: Müşteri Yönetimi (CRM)**
    - **Aksiyon:** **Modül 3**'te belirtilen müşteri profili (CRUD), notlar ve etiketleme özelliklerinin geliştirilmesi. Müşteri detay sayfasında Randevu Geçmişi, Paket Kullanımları gibi sekmeler için boş alanların hazırlanması.
    - **Amaç:** Sistemin müşteri veritabanını oluşturmak.
- **Basamak 11: Hizmet Yönetimi**
    - **Aksiyon:** **Modül 4.1**'deki hizmet tanımlama arayüzünün (CRUD) geliştirilmesi. Hizmetin süresi, fiyatı ve o hizmeti hangi personellerin verebileceğinin seçileceği (`service_staff` pivot tablosu) ilişki kurulmalıdır.
    - **Amaç:** Randevularda seçilebilecek hizmetleri tanımlamak.
- **Basamak 12: Paket Yönetimi**
    - **Aksiyon:** **Modül 4.2**'deki paket tanımlama arayüzünün (CRUD) geliştirilmesi. Paket türü (adet/dakika), geçerlilik süresi ve kapsadığı hizmetlerin seçilmesi sağlanmalıdır.
    - **Amaç:** Seanslı veya süreli paket satışları için altyapıyı hazırlamak.

### **Aşama 3: Çekirdek Fonksiyon: Randevu Yönetimi (Basamak 13-16) - Modül 2**

Sistemin kalbi olan randevu modülü bu aşamada geliştirilir.

- **Basamak 13: Takvim Arayüzünün Entegrasyonu ve Veri Akışı**
    - **Aksiyon:** FullCalendar.js kütüphanesinin projeye entegre edilmesi. CodeIgniter'da bir API endpoint'i oluşturarak takvimin veritabanından randevuları JSON formatında çekmesinin sağlanması. Kullanıcının rolüne göre (Admin tümü, Yönetici şubesi, Personel kendisi) randevuları filtreleyerek getirmesi.
    - **Amaç:** **Modül 2.1**'deki takvim görünümünü işlevsel hale getirmek.
- **Basamak 14: Randevu Oluşturma Sihirbazının Geliştirilmesi**
    - **Aksiyon:** **Modül 2.2**'de tarif edilen adımları içeren bir modal (popup) formun oluşturulması.
        1. AJAX ile kayıtlı müşteri arama / yeni müşteri ekleme.
        2. Hizmet seçildiğinde, o hizmeti verebilen personelleri AJAX ile filtreleme.
        3. Personel seçimi.
        4. Seçilen personele ve hizmet süresine göre uygun zaman aralıklarını gösteren (dolu saatleri pasif yapan) bir arayüz geliştirme.
    - **Amaç:** Hızlı, akıllı ve hatasız randevu girişini sağlamak.
- **Basamak 15: Randevu Düzenleme ve Durum Yönetimi**
    - **Aksiyon:** Takvim üzerinde randevuların sürükle-bırak ile zamanının/personelinin değiştirilmesi ve kenarlarından çekerek süresinin uzatılıp kısaltılması (FullCalendar event'leri ile). Randevuya tıklandığında açılan bir menü ile durumunun (`Onaylandı`, `Tamamlandı`, `İptal`, `Gelmedi`) güncellenmesi.
    - **Amaç:** Randevu yönetimini pratik ve görsel hale getirmek.
- **Basamak 16: Paket Satışı ve Otomatik Düşüm**
    - **Aksiyon:** **Modül 4.3**'e uygun olarak müşteri profili üzerinden paket satışı ekranının yapılması. Bir randevu "Tamamlandı" olarak işaretlendiğinde, sistemin otomatik olarak ilgili hizmetin müşterinin aktif paketinden düşmesini (seans veya dakika olarak) sağlayan logic'in yazılması.
    - **Amaç:** Paket takibini otomatikleştirmek.

### **Aşama 4: Finansal İşlemler ve Prim Sistemi (Basamak 17-19) - Modül 5 & 7**

Randevular tamamlandığında ortaya çıkan finansal süreçler ve personel hak edişleri bu aşamada kodlanır.

- **Basamak 17: Ödeme Alma ve Kasa Yönetimi**
    - **Aksiyon:** **Modül 7.1, 7.2 ve 7.3**'ü içeren arayüzlerin geliştirilmesi. Ödeme tipi (Nakit, Kart vb.), parçalı ödeme ve veresiye (borç) kaydının oluşturulması. Günlük kasa açılış/kapanış ve manuel kasa hareketleri (gider/ek gelir) modüllerinin yapılması.
    - **Amaç:** Salonun finansal döngüsünü eksiksiz yönetmek.
- **Basamak 18: Prim Kuralı Tanımlama ve Hesaplama Motoru**
    - **Aksiyon:** **Modül 5.1**'de belirtilen, hizmet bazlı yüzde veya sabit tutar prim kurallarının tanımlanabildiği bir admin arayüzü oluşturulması. **Modül 5.2**'ye uygun olarak, "Tamamlandı" statüsündeki her randevu için ilgili personelin primini bu kurallara göre otomatik hesaplayıp `commissions` tablosuna kaydeden arka plan motorunun yazılması. İade durumunda primin geri alınması logic'i de eklenmelidir.
    - **Amaç:** Prim hesaplamasını otomatize ederek manuel iş yükünü ortadan kaldırmak.
- **Basamak 19: Finansal Raporlama ve Prim Raporu**
    - **Aksiyon:** **Modül 7.4**'teki Günlük Kasa, Detaylı Kasa Geçmişi ve Alacak/Borç Raporlarının oluşturulması. **Modül 5.3**'e uygun olarak, personelin veya yöneticinin tarih aralığı seçerek detaylı prim raporu alabileceği sayfanın geliştirilmesi.
    - **Amaç:** Yöneticilere anlık ve geçmişe dönük finansal verileri sunmak.

### **Aşama 5: Otomasyon ve Ek Özellikler (Basamak 20-22) - Modül 6**

Sistemin verimliliğini artıracak otomasyonlar bu son geliştirme aşamasında eklenir.

- **Basamak 20: Bildirim Servisi Entegrasyonu**
    - **Aksiyon:** **Modül 6.1**'e göre, Admin panelinde Netgsm gibi bir SMS veya WAHA (WhatsApp) sağlayıcısının API anahtarlarının girileceği bir ayar sayfası oluşturulması. Bu servislerle mesaj gönderebilen temel bir sınıfın (class) yazılması.
    - **Amaç:** Otomatik bildirimler için altyapıyı kurmak.
- **Basamak 21: Otomatik Mesaj Şablonları ve Tetikleyiciler**
    - **Aksiyon:** **Modül 6.2**'deki gibi değişkenli (`{musteri_adi}`, `{randevu_tarihi}` vb.) mesaj şablonlarının panelden düzenlenebilir hale getirilmesi. **Modül 6.3**'teki tetikleyicilerin (Randevu Hatırlatma, Paket Uyarısı, No-Show, Doğum Günü) ayarlanması. Bu işlemler için sunucuda düzenli olarak çalışacak bir **Cron Job** (veya CodeIgniter'ın kendi `Scheduled Tasks` özelliği) kurulmalıdır.
    - **Amaç:** Müşteri iletişimini ve sadakatini otomatize etmek, "gelmeme" oranlarını düşürmek.
- **Basamak 22: Müşteri Geçmişi Sekmelerinin Doldurulması**
    - **Aksiyon:** Proje boyunca oluşturulan verileri (randevular, ödemeler, paket kullanımları, gönderilen mesajlar) **Modül 3.2**'ye uygun olarak müşteri profili altındaki ilgili sekmelerde listeleyerek göstermek.
    - **Amaç:** Müşteri ile ilgili tüm bilgilere tek bir ekrandan ulaşılmasını sağlamak.

### **Aşama 6: Son Kontroller ve Yayına Alma (Basamak 23-26)**

- **Basamak 23: Kapsamlı Test ve Hata Ayıklama**
    - **Aksiyon:** Tüm rollerle sisteme giriş yaparak yetki kontrollerinin, tüm işlevlerin (randevu, ödeme, prim), raporların ve bildirimlerin doğru çalışıp çalışmadığının test edilmesi. Farklı cihazlarda (mobil, tablet, masaüstü) responsive tasarımın kontrol edilmesi.
    - **Amaç:** Yazılımın kararlı ve hatasız olduğundan emin olmak.
- **Basamak 24: Güvenlik Denetimi ve Optimizasyon**
    - **Aksiyon:** **Modül 8.3**'e göre SQL Injection, XSS gibi zafiyetlere karşı tüm formların ve endpoint'lerin kontrol edilmesi. Veritabanı sorgularının optimize edilmesi, CSS ve JS dosyalarının birleştirilip küçültülmesi (minification) ile sistem performansının artırılması.
    - **Amaç:** Güvenli ve hızlı bir uygulama sunmak.
- **Basamak 25: Dokümantasyon Hazırlığı**
    - **Aksiyon:** Her rol için (Admin, Yönetici, Danışma, Personel) sistemin nasıl kullanılacağını anlatan basit kullanıcı kılavuzları hazırlanması.
    - **Amaç:** Kullanıcıların sistemi kolayca benimsemesini sağlamak.
- **Basamak 26: Yayına Alma (Deployment)**
    - **Aksiyon:** Projenin canlı sunucuya yüklenmesi. `.env` dosyasının canlı sunucuya göre ayarlanması. Veritabanı göçlerinin (migrations) çalıştırılması ve gerekli Cron Job'ların sunucuya tanımlanması.
    - **Amaç:** Projeyi son kullanıcının hizmetine sunmak.

Bu adımları takip ederek, teknik şartnamede belirtilen tüm özellikleri karşılayan, sağlam, güvenli ve ölçeklenebilir bir güzellik salonu yönetim sistemi geliştirebilirsiniz.