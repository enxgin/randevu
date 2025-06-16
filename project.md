### **Güzellik Salonu Randevu ve Yönetim Sistemi - Teknik Şartname Taslağı**

Bu belge, geliştirilecek olan PHP CodeIgniter ve TailwindCSS tabanlı randevu ve yönetim sisteminin fonksiyonel ve teknik özelliklerini detaylandırmak amacıyla hazırlanmıştır.

### **Modül 1: Kullanıcı Yönetimi ve Yetkilendirme**

Sisteme giriş yapacak kullanıcıların rollere göre farklı yetkilere sahip olacağı dinamik bir yapı kurgulanacaktır.

- **1.1. Rol Tanımları ve Yetki Matrisi:**
    - **Admin (Süper Yönetici):**
        - Tüm sistem ayarlarına erişim (şube ekleme/çıkarma, SMS/WhatsApp ayarları, prim kuralları vb.).
        - Tüm şubelerin verilerini (randevular, finansal raporlar, müşteri listeleri) görme ve yönetme.
        - Tüm kullanıcı rollerini oluşturma, düzenleme, silme.
        - Sistemdeki tüm verileri silme ve düzenleme yetkisi.
    - **Yönetici (Şube Müdürü):**
        - Sadece kendi şubesine ait verileri yönetme.
        - Şube personeli ekleme/çıkarma.
        - Şube randevu takvimini yönetme.
        - Şubenin finansal raporlarını (kasa, ciro, prim) görüntüleme.
        - Hizmet ve paket fiyatlarını (belki sadece kendi şubesi için) yönetme.
        - Admin'in izin verdiği sistem ayarlarına erişim.
    - **Danışma (Resepsiyon):**
        - Randevu oluşturma, düzenleme, silme.
        - Müşteri profili oluşturma ve güncelleme.
        - Ödeme alma, fatura/fiş oluşturma, borç kaydı girme.
        - Günlük kasa açma/kapama işlemlerini yapma.
        - Sadece kendi şubesinin takvimini ve müşteri listesini görme. Finansal özet raporları göremez.
    - **Personel (Uzman/Terapist):**
        - Sadece kendi adına atanmış randevuları takvimde görme.
        - Randevu ile ilgili notları görme ve ekleme (hizmet notu).
        - Kendi prim raporlarını belirtilen tarih aralığında görüntüleme.
        - Müşteri iletişim bilgilerini göremez (opsiyonel, KVKK için önemli).
        - Randevu oluşturma/silme yetkisi yoktur.

### **Modül 2: Randevu Yönetimi**

Sistemin kalbi olan bu modül, esnek ve hatasız bir randevu akışı sağlamalıdır.

- **2.1. Takvim Arayüzü (FullCalendar veya eşdeğeri):**
    - **Görünümler:** Günlük (personel bazlı kolonlar), Haftalık, Aylık.
    - **Sürükle-Bırak:** Randevuları sürükleyerek zamanını veya atandığı personeli değiştirme.
    - **Yeniden Boyutlandırma:** Randevu süresini takvim üzerinden uzatıp kısaltma.
    - **Renklendirme:** Randevu durumuna (onaylandı, tamamlandı, gelmedi) veya hizmet türüne göre otomatik renk kodlaması.
- **2.2. Randevu Oluşturma Sihirbazı:**
    - **Adım 1: Müşteri Seçimi:** Kayıtlı müşteri arama veya yeni müşteri hızlı ekleme formu.
    - **Adım 2: Hizmet/Paket Seçimi:**
        - Müşterinin mevcut paketi varsa, paket kullanımı otomatik olarak seçili gelir.
        - Normal hizmetler listelenir. Hizmet seçildiğinde, o hizmeti verebilen personel otomatik olarak filtrelenir.
    - **Adım 3: Personel Seçimi:** Hizmeti verebilen personellerden birini seçme.
    - **Adım 4: Tarih ve Saat Seçimi:**
        - Seçilen personele ve hizmet süresine göre takvimde sadece uygun olan zaman aralıkları gösterilir.
        - Çift rezervasyon ve personelin mola/çalışma saatleri dışına randevu verilmesi engellenir.
- **2.3. Randevu Türleri:**
    - **Tek Seferlik:** Standart randevu.
    - **Tekrar Eden Randevu:** Haftalık, 2 haftada bir, aylık gibi periyotlarda otomatik randevu serisi oluşturma.
- **2.4. Randevu Durum Yönetimi:**
    - **Onay Bekliyor:** (Opsiyonel) Müşterinin online rezervasyonu için.
    - **Onaylandı:** Standart durum.
    - **Tamamlandı:** Hizmet verildi, ödeme bekleniyor/alındı.
    - **İptal Edildi:** Müşteri veya salon tarafından iptal edildi.
    - **No-Show (Gelmedi):** Müşteri randevuya gelmedi. Bu durum müşteri profiline işlenir ve raporlanabilir.

### **Modül 3: Müşteri Yönetimi (CRM)**

- **3.1. Müşteri Profili:**
    - Ad, Soyad, Telefon, E-posta, Doğum Günü.
    - **Müşteriye Özel Notlar:** Alerjiler, tercihler vb.
    - **Etiketleme:** "VIP", "Sorunlu Müşteri", "İlk Ziyaret" gibi etiketler ekleyebilme.
- **3.2. Müşteri Geçmişi Sekmeleri:**
    - **Randevu Geçmişi:** Tüm geçmiş ve gelecek randevuları listesi (durumlarıyla birlikte).
    - **Paket Kullanımları:** Satın aldığı paketler, kalan seans/dakika ve kullanım detayları.
    - **Ödeme Geçmişi:** Yapılan tüm ödemeler, borç durumu ve iadeler.
    - **Gönderilen Mesajlar:** Müşteriye gönderilen tüm SMS/WhatsApp mesajlarının kaydı.

### **Modül 4: Hizmet ve Paket Yönetimi**

- **4.1. Hizmet Tanımlama:**
    - Hizmet Adı, Kategorisi (Cilt Bakımı, Lazer vb.).
    - **Süre:** Dakika cinsinden standart hizmet süresi.
    - **Fiyat:** Standart hizmet ücreti.
    - **Personel Ataması:** Bu hizmeti hangi personellerin verebileceğini seçme.
- **4.2. Paket Tanımlama:**
    - Paket Adı ve Açıklaması.
    - **Paket Türü:**
        - **Adet Bazlı:** Örn: "10 Seans Lazer Epilasyon".
        - **Dakika Bazlı:** Örn: "300 Dakika Masaj Paketi".
    - Toplam Seans/Dakika.
    - Fiyat.
    - **Geçerlilik Süresi:** Ay cinsinden (örn: satın alımdan itibaren 6 ay geçerli).
    - **Kapsadığı Hizmetler:** Paketin hangi hizmetler için kullanılabileceğini seçme.
- **4.3. Paket Satışı ve Takibi:**
    - Müşteri profili üzerinden paket satışı yapılır. Satış tarihi otomatik kaydedilir.
    - Randevu "Tamamlandı" olarak işaretlendiğinde, eğer paketle ilişkili bir hizmet ise, müşterinin paketinden otomatik olarak 1 seans veya hizmet süresi kadar dakika düşülür.
    - Bitiş tarihi geçen paketler otomatik olarak "geçersiz" statüsüne alınır.

### **Modül 5: Personel ve Prim Yönetimi**

- **5.1. Prim Kuralı Tanımlama Arayüzü:**
    - **Kural Tipi:**
        - **Yüzdesel (%):** Hizmet bedelinin belirli bir yüzdesi.
        - **Sabit Tutar (₺):** Her hizmet başına sabit bir tutar.
        - **Hizmete Özel:** "Cilt Bakımı" için %10, "Lazer" için %12 gibi farklı oranlar tanımlayabilme.
    - **Ayrım:** Normal hizmetler için ayrı, paketli hizmetler için ayrı prim oranı/tutarı tanımlanabilmelidir. (Örn: Paketli hizmet primi daha düşük olabilir).
- **5.2. Prim Hesaplama Motoru:**
    - Her "Tamamlandı" statüsündeki randevu için, ilgili personelin primini otomatik olarak hesaplar ve kaydeder.
    - **İade Logic'i:** Bir hizmet veya ürün iade edildiğinde, o işlemden kazanılan prim, personelin bir sonraki prim hakedişinden otomatik olarak düşülür.
- **5.3. Prim Raporlama:**
    - Personel bazında ve tüm personeller için tarih aralığı seçilerek (haftalık, aylık, özel aralık) detaylı prim raporu oluşturma. Raporda hizmet adı, işlem tarihi, hizmet tutarı ve kazanılan prim tutarı yer alır.

### **Modül 6: Otomatik Bildirim Sistemi (SMS & WhatsApp)**

- **6.1. Entegrasyon Ayarları:**
    - Yönetim panelinden Netgsm veya seçilecek başka bir sağlayıcının API bilgilerini girme alanı.
    - WAHA (WhatsApp HTTP API) için API endpoint ve token bilgilerini girme alanı.
- **6.2. Otomatik Mesaj Şablonları:**
    - Panel üzerinden düzenlenebilir metin şablonları (`{musteri_adi}`, `{randevu_tarihi}`, `{randevu_saati}`, `{salon_adi}` gibi değişkenler kullanılabilmeli).
- **6.3. Tetikleyiciler (Triggers):**
    - **Randevu Hatırlatma:** Randevudan 24 saat ve 2 saat önce otomatik gönderim.
    - **Paket Uyarısı:** Müşterinin paketinde son 1 seans/kullanımlık dakika kaldığında, randevu tamamlandığında otomatik gönderim.
    - **No-Show Bildirimi:** Randevu "Gelmedi" olarak işaretlendikten 1 saat sonra otomatik "Sizi aramızda göremedik, yeni bir randevu için bize ulaşabilirsiniz." mesajı.
    - **Doğum Günü Kutlaması:** (Ekstra Özellik) Müşterinin doğum gününde otomatik kutlama mesajı.

### **Modül 7: Ödeme, Kasa ve Finans Yönetimi**

- **7.1. Ödeme Alma Ekranı:**
    - Randevu sonrası veya doğrudan müşteri profilinden ödeme alma.
    - **Ödeme Tipleri:** Nakit, Kredi Kartı, Havale/EFT, Hediye Çeki.
    - **Parçalı Ödeme:** Toplam tutarın bir kısmını nakit, kalanını kredi kartı gibi alabilme.
- **7.2. Borç (Veresiye) Yönetimi:**
    - Ödeme eksik alındığında kalan tutar otomatik olarak müşterinin borç hanesine yazılır.
    - Müşteri profilinde ve raporlarda borçlu müşteriler listesi.
- **7.3. Kasa Yönetimi:**
    - **Kasa Açılışı/Kapanışı:** Her iş günü başında açılış tutarı girilir, gün sonunda sistemdeki işlemlerle birlikte toplam ciro ve son bakiye gösterilerek kasa kapatılır.
    - **Manuel Kasa Hareketleri:** Kasaya giren (örn: sermaye eklemesi) veya çıkan (örn: fatura ödemesi, personel avansı) paraları manuel olarak işleyebilme (açıklama zorunlu).
- **7.4. Finansal Raporlama:**
    - **Günlük Kasa Raporu:** O günkü tüm nakit, kredi kartı vb. girişleri, manuel çıkışları ve toplam ciroyu gösteren rapor.
    - **Detaylı Kasa Geçmişi:** Tarih aralığına, işlem tipine (randevu, manuel giriş vb.) göre filtrelenebilir kasa hareketleri raporu.
    - **Alacak/Borç Raporu:** Tüm borçlu müşterileri ve toplam alacak tutarını gösteren rapor.

### **Modül 8: Teknik Gereksinimler ve Mimari**

- **8.1. Teknoloji Stack'i:**
    - **Backend:** PHP - CodeIgniter
    - **Frontend:** HTML5, CSS3, JavaScript - ailwindCSS
    - **Veritabanı:** MySQL
- **8.2. Mimari:**
    - **Responsive Tasarım:** TailwindCSS
    - **Çoklu Şube Desteği:** Veritabanı şeması en başından itibaren `branch_id` (şube kimliği) gibi alanlar içermelidir. Tüm randevular, müşteriler, ödemeler ve personel bir şubeye bağlı olmalıdır. Başlangıçta tek şube kullanılsa bile sistem bu yapıya hazır olacaktır.
- **8.3. Güvenlik:**
    - SQL Injection, XSS gibi zafiyetlere karşı önlemler alınmalı.
    - Parolalar hash'lenerek saklanmalı.
    - Rol bazlı yetkilendirme sunucu tarafında (backend) kontrol edilmeli.
